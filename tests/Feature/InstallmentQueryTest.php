<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Paynet\Message\InstallmentQueryRequest;
use Omnipay\Paynet\Message\InstallmentQueryResponse;
use Omnipay\Paynet\Tests\TestCase;

class InstallmentQueryTest extends TestCase
{
	/**
	 * @throws InvalidRequestException
	 * @throws \JsonException
	 */
	public function test_installment_query_request()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/InstallmentQueryRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new InstallmentQueryRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		self::assertArrayHasKey('bin', $data);
		self::assertArrayHasKey('amount', $data);
		self::assertArrayHasKey('addcomission_to_amount', $data);

		self::assertEquals('415565', $data['bin']);
		self::assertEquals('10000', $data['amount']);
		self::assertTrue($data['addcomission_to_amount']);
	}

	public function test_installment_query_request_validation_error()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/InstallmentQueryRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new InstallmentQueryRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidRequestException::class);

		$request->getData();
	}

	public function test_installment_query_response_success()
	{
		$httpResponse = $this->getMockHttpResponse('InstallmentQueryResponseSuccess.txt');

		$response = new InstallmentQueryResponse($this->getMockRequest(), $httpResponse);

		$this->assertTrue($response->isSuccessful());

		$installments = $response->getInstallments();

		$this->assertCount(3, $installments);

		$this->assertEquals(2, $installments[0]['instalment']);
		$this->assertEquals(3, $installments[1]['instalment']);
		$this->assertEquals(6, $installments[2]['instalment']);

		$bankInfo = $response->getBankInfo();

		$this->assertEquals('Akbank', $bankInfo['bank_name']);
		$this->assertEquals('VISA', $bankInfo['card_type']);
	}

	public function test_installment_query_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('InstallmentQueryResponseApiError.txt');

		$response = new InstallmentQueryResponse($this->getMockRequest(), $httpResponse);

		$this->assertFalse($response->isSuccessful());

		$this->assertEquals('BIN numarasi gecersiz', $response->getMessage());

		$this->assertEmpty($response->getInstallments());
	}

	public function test_installment_query_tds_required()
	{
		$httpResponse = $this->getMockHttpResponse('InstallmentQueryResponseSuccess.txt');

		$response = new InstallmentQueryResponse($this->getMockRequest(), $httpResponse);

		$this->assertFalse($response->isTdsRequired());
	}
}
