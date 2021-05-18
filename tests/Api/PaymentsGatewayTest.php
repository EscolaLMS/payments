<?php

namespace EscolaLms\Payments\Tests\Api;

class PaymentsGatewayTest extends \EscolaLms\Payments\Tests\TestCase
{
	public function testGatewayCanReceiveStatusNotification() {
		//@todo register payment
		//@todo create event in external system that leads to status change
		//@todo check if the status had been changed internally
	}
}
