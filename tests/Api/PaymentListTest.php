<?php

namespace EscolaLms\Payments\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Models\Payment;
use EscolaLms\Payments\Tests\Traits\CreatesBillable;

class PaymentListTest extends \EscolaLms\Payments\Tests\TestCase
{
	use CreatesBillable;
	use CreatesUsers;

	public function testStudentCanListRegisteredPayments()
	{
		Payment::truncate();

		$billable = $this->createBillableStudent();
		$payments = Payment::factory()->count(20)->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => get_class($billable)
		]);
		$billable2 = $this->createBillableStudent();
		$payments2 = Payment::factory()->count(20)->create([
			'billable_id' => $billable2->getKey(),
			'billable_type' => get_class($billable2)
		]);

		$response = $this->actingAs($billable)->json('GET', 'api/payments/', [
			'limit' => 100,
		]);
		$response->assertOk();
		$response->assertJsonFragment([
			'id' => $payments[0]->getKey(),
		]);
		$response->assertJsonCount(20, 'data');
	}

	public function testAdminCanListAllRegisteredPayments()
	{
		Payment::truncate();

		$billable = $this->createBillableStudent();
		$payments = Payment::factory()->count(10)->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => get_class($billable)
		]);
		$billable2 = $this->createBillableStudent();
		$payments2 = Payment::factory()->count(10)->create([
			'billable_id' => $billable2->getKey(),
			'billable_type' => get_class($billable2)
		]);

		$admin = $this->makeAdmin();
		$admin->save();

		$response = $this->actingAs($admin)->json('GET', 'api/admin/payments/', [
			'limit' => 100,
		]);
		$response->assertOk();
		$response->assertJsonFragment([
			'id' => $payments[0]->getKey()
		]);
		$response->assertJsonFragment([
			'id' => $payments2[0]->getKey()
		]);
		$response->assertJsonCount(20, 'data');
	}

	public function testAdminCanListAllRegisteredPaymentsWithFilter()
	{
		Payment::truncate();

		$billable = $this->createBillableStudent();
		$paymentsNew = Payment::factory()->count(5)->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => get_class($billable),
			'status' => PaymentStatus::NEW,
		]);
		$paymentsPaid = Payment::factory()->count(5)->create([
			'billable_id' => $billable->getKey(),
			'billable_type' => get_class($billable),
			'status' => PaymentStatus::PAID,
		]);

		$admin = $this->makeAdmin();
		$admin->save();

		$response = $this->actingAs($admin)->json('GET', 'api/admin/payments/', [
			'limit' => 100
		]);
		$response->assertOk();
		$response->assertJsonFragment([
			'id' => $paymentsNew[0]->getKey()
		]);
		$response->assertJsonFragment([
			'id' => $paymentsPaid[0]->getKey()
		]);
		$response->assertJsonCount(10, 'data');

		$response = $this->actingAs($admin)->json('GET', 'api/admin/payments/', [
			'limit' => 100,
			'status' => PaymentStatus::PAID,
		]);
		$response->assertOk();
		$response->assertJsonMissing([
			'id' => $paymentsNew[0]->getKey()
		]);
		$response->assertJsonFragment([
			'id' => $paymentsPaid[0]->getKey()
		]);
		$response->assertJsonCount(5, 'data');
	}
}
