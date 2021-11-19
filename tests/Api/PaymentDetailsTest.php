<?php

namespace EscolaLms\Payments\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Payments\Models\Payment;
use EscolaLms\Payments\Tests\Traits\CreatesBillable;

class PaymentDetailsTest extends \EscolaLms\Payments\Tests\TestCase
{
	use CreatesBillable;
	use CreatesUsers;

	public function testStudentCanViewPaymentDetails()
	{
		$billable = $this->createBillableStudent();
		$payment = Payment::factory()->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => $billable->getMorphClass(),
		]);

		$response = $this->actingAs($billable)->json('GET', 'api/payments/' . $payment->getKey());
		$response->assertOk();
		$response->assertJsonFragment([
			'id' => $payment->getKey()
		]);
	}

	public function testStudentCannotViewPaymentDetailsOfOthers()
	{
		$billable = $this->createBillableStudent();
		$billable2 = $this->createBillableStudent();
		$payment = Payment::factory()->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => $billable->getMorphClass(),
		]);

		$response = $this->actingAs($billable2)->json('GET', 'api/payments/' . $payment->getKey());
		$response->assertForbidden();
	}

	public function testAdminCanViewPaymentDetails()
	{
		$billable = $this->createBillableStudent();
		$payment = Payment::factory()->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => $billable->getMorphClass(),
		]);

		$admin = $this->makeAdmin();
		$admin->save();

		$response = $this->actingAs($admin)->json('GET', 'api/admin/payments/' . $payment->getKey());
		$response->assertOk();
		$response->assertJsonFragment([
			'id' => $payment->getKey()
		]);
	}
}
