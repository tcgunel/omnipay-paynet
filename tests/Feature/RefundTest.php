<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Paynet\Helpers\Helper;
use Omnipay\Paynet\Message\RefundRequest;
use Omnipay\Paynet\Message\RefundResponse;
use Omnipay\Paynet\Tests\TestCase;

class RefundTest extends TestCase
{
	/**
	 * @throws InvalidRequestException
	 * @throws \JsonException
	 */
	public function test_refund_request()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/RefundRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		self::assertArrayHasKey('xact_id', $data);
		self::assertArrayHasKey('amount', $data);

		self::assertEquals('PAY-TXN-00001', $data['xact_id']);
		self::assertEquals('5000', $data['amount']);
	}

	public function test_refund_request_validation_error()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/RefundRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidRequestException::class);

		$request->getData();
	}

	public function test_refund_response_success()
	{
		$httpResponse = $this->getMockHttpResponse('RefundResponseSuccess.txt');

		$response = new RefundResponse($this->getMockRequest(), $httpResponse);

		$this->assertTrue($response->isSuccessful());

		$this->assertEquals('Iade islemi basariyla gerceklesti', $response->getMessage());
	}

	public function test_refund_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('RefundResponseApiError.txt');

		$response = new RefundResponse($this->getMockRequest(), $httpResponse);

		$this->assertFalse($response->isSuccessful());

		$this->assertEquals('Iade islemi sirasinda bir hata olustu', $response->getMessage());
	}
}
