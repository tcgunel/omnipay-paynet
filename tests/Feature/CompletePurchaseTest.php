<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Paynet\Message\CompletePurchaseRequest;
use Omnipay\Paynet\Message\CompletePurchaseResponse;
use Omnipay\Paynet\Tests\TestCase;

class CompletePurchaseTest extends TestCase
{
	/**
	 * @throws InvalidRequestException
	 * @throws \JsonException
	 */
	public function test_complete_purchase_request()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/CompletePurchaseRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		self::assertArrayHasKey('session_id', $data);
		self::assertArrayHasKey('token_id', $data);
		self::assertArrayHasKey('transaction_type', $data);

		self::assertEquals('SES-123456', $data['session_id']);
		self::assertEquals('TKN-789012', $data['token_id']);
		self::assertEquals(1, $data['transaction_type']);
	}

	public function test_complete_purchase_request_validation_error()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/CompletePurchaseRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidRequestException::class);

		$request->getData();
	}

	public function test_complete_purchase_response_success()
	{
		$httpResponse = $this->getMockHttpResponse('CompletePurchaseResponseSuccess.txt');

		$response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse);

		$this->assertTrue($response->isSuccessful());

		$this->assertEquals('PAY-TXN-3D-00001', $response->getTransactionReference());

		$this->assertEquals('ORDER-3D-12345', $response->getTransactionId());
	}

	public function test_complete_purchase_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('CompletePurchaseResponseApiError.txt');

		$response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse);

		$this->assertFalse($response->isSuccessful());

		$this->assertEquals('3D dogrulama basarisiz', $response->getMessage());

		$this->assertNull($response->getTransactionReference());
	}
}
