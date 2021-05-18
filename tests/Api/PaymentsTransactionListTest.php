<?php

namespace EscolaLms\Payments\Tests\Api;

class PaymentsTransactionListTest extends \EscolaLms\Payments\Tests\TestCase
{
	public function testStudentCanListRegisteredPayments() {
		//@todo login as student
		//@todo register some payments of that user internally
		//@todo check if payments are returned through api
	}

	public function testStudentCannotListRegisteredPaymentsOfOthers() {
		//@todo login as student
		//@todo register some payments of that user internally
		//@todo check if payments are returned through api
	}

	public function testAdminCanListAllRegisteredPayments() {
		//@todo login as admin
		//@todo register some payments of different users internally
		//@todo check if payments are returned through api
	}

	public function testAdminCanListAllRegisteredPaymentsWithFilter() {
		//@todo login as admin
		//@todo register some payments of different users internally
		//@todo check if payments are returned through api with filter applied (course, user, date)
	}

	public function testTutorCanListRegisteredPaymentsOfHisStudents() {
		//@todo login as tutor
		//@todo register some payments of different users on product that is owned by user
		//@todo check if payments are returned through api
	}

	public function testTutorCanListRegisteredPaymentsOfHisStudentsWithFilter() {
		//@todo login as tutor
		//@todo register some payments of different users on product that is owned by user
		//@todo check if payments are returned through api with filter applied (course, user, date)
	}

	public function testTutorCannotListRegisteredPaymentsOfDifferentStudents() {
		//@todo login as tutor
		//@todo register some payments of different users on product that is owned by different user
		//@todo make sure that payments are not returned through api
	}
}
