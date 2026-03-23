<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Paynet\Gateway;
use Omnipay\Paynet\Message\CancelRequest;
use Omnipay\Paynet\Message\CompletePurchaseRequest;
use Omnipay\Paynet\Message\InstallmentQueryRequest;
use Omnipay\Paynet\Message\Purchase3dRequest;
use Omnipay\Paynet\Message\PurchaseRequest;
use Omnipay\Paynet\Message\RefundRequest;
use Omnipay\Paynet\Tests\TestCase;

class GatewayTest extends TestCase
{
	public function test_gateway_name()
	{
		$this->assertEquals('Paynet', $this->gateway->getName());
	}

	public function test_gateway_default_parameters()
	{
		$defaults = $this->gateway->getDefaultParameters();

		$this->assertArrayHasKey('secretKey', $defaults);
		$this->assertArrayHasKey('installment', $defaults);
		$this->assertArrayHasKey('addCommission', $defaults);
		$this->assertArrayHasKey('testMode', $defaults);
	}

	public function test_gateway_secret_key()
	{
		$this->gateway->setSecretKey('test-secret');

		$this->assertEquals('test-secret', $this->gateway->getSecretKey());
	}

	public function test_gateway_purchase()
	{
		$request = $this->gateway->purchase([
			'secretKey'     => 'test',
			'amount'        => '10.00',
			'transactionId' => 'ORDER-1',
		]);

		$this->assertInstanceOf(PurchaseRequest::class, $request);
	}

	public function test_gateway_purchase_3d()
	{
		$request = $this->gateway->purchase3d([
			'secretKey'     => 'test',
			'amount'        => '10.00',
			'transactionId' => 'ORDER-1',
			'returnUrl'     => 'https://example.com/callback',
		]);

		$this->assertInstanceOf(Purchase3dRequest::class, $request);
	}

	public function test_gateway_complete_purchase()
	{
		$request = $this->gateway->completePurchase([
			'secretKey' => 'test',
			'sessionId' => 'SES-1',
			'tokenId'   => 'TKN-1',
		]);

		$this->assertInstanceOf(CompletePurchaseRequest::class, $request);
	}

	public function test_gateway_void()
	{
		$request = $this->gateway->void([
			'secretKey' => 'test',
			'xactId'    => 'TXN-1',
		]);

		$this->assertInstanceOf(CancelRequest::class, $request);
	}

	public function test_gateway_refund()
	{
		$request = $this->gateway->refund([
			'secretKey' => 'test',
			'xactId'    => 'TXN-1',
			'amount'    => '5.00',
		]);

		$this->assertInstanceOf(RefundRequest::class, $request);
	}

	public function test_gateway_installment_query()
	{
		$request = $this->gateway->installmentQuery([
			'secretKey' => 'test',
			'bin'       => '415565',
			'amount'    => '100.00',
		]);

		$this->assertInstanceOf(InstallmentQueryRequest::class, $request);
	}
}
