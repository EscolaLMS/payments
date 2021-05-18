<?php

namespace EscolaLms\Payments\Tests\Api;

class PaymentsDetailsTest extends \EscolaLms\Payments\Tests\TestCase
{
	public function testStudentCanUseRegisteredDetails() {
		//@todo login as student
		//@todo register payment details internally
		//@todo check if api does return payment details
	}

	public function testStudentCannotUseMissingDetails() {
		//@todo login as student
		//@todo make sure that payment details are blank
	}
}
