<?php

namespace EscolaLms\Payments\Tests\Api;

class PaymentsTransactionDetailsTest extends \EscolaLms\Payments\Tests\TestCase
{
	public function testStudentCanViewPaymentDetails() {
		//@todo login as student
		//@todo register a payment internally
		//@todo fetch details that should include dates and amounts
	}

	public function testStudentCannotViewPaymentDetailsOfOthers() {
		//@todo login as student
		//@todo register others user payment
		//@todo make sure that returned response doesn't include payment details
	}

	public function testAdminCanViewPaymentDetails() {
		//@todo login as admin
		//@todo register a different users payment internally
		//@todo check details that should include dates and amounts
		//  AND history of communication with payment gateway related to that payment
	}

	public function testTutorCanViewPaymentDetailsOfHisStudents() {
		//@todo login as tutor
		//@todo register a different users payment internally
		//@todo check details that should include dates and amounts
		//  AND history of communication with payment gateway related to that payment
	}

	public function testTutorCannotViewPaymentDetailsOfNotHisStudents() {
		//@todo login as tutor
		//@todo register a different users payment internally which are not associated with the tutors courses
		//@todo make sure that returned response doesn't include payment details
	}
}
