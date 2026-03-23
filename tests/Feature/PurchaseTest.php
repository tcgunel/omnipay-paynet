<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Paynet\Message\PurchaseRequest;
use Omnipay\Paynet\Message\PurchaseResponse;
use Omnipay\Paynet\Tests\TestCase;

class PurchaseTest extends TestCase
{
    /**
     * @throws InvalidRequestException
     * @throws InvalidCreditCardException
     * @throws \JsonException
     */
    public function test_purchase_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        self::assertArrayHasKey('amount', $data);
        self::assertArrayHasKey('reference_no', $data);
        self::assertArrayHasKey('pan', $data);
        self::assertArrayHasKey('month', $data);
        self::assertArrayHasKey('year', $data);
        self::assertArrayHasKey('cvc', $data);
        self::assertArrayHasKey('transaction_type', $data);

        self::assertEquals('15000', $data['amount']);
        self::assertEquals('ORDER-12345', $data['reference_no']);
        self::assertEquals('4155650100416111', $data['pan']);
        self::assertEquals(1, $data['instalment']);
        self::assertEquals(1, $data['transaction_type']);
        self::assertFalse($data['add_commission']);
    }

    public function test_purchase_request_with_installment()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $options['installment'] = 3;

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        self::assertEquals(3, $data['instalment']);
        self::assertTrue($data['add_commission']);
    }

    public function test_purchase_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    public function test_purchase_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseResponseSuccess.txt');

        $response = new PurchaseResponse($this->getMockRequest(), $httpResponse);

        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('PAY-TXN-00001', $response->getTransactionReference());

        $this->assertNull($response->getMessage());
    }

    public function test_purchase_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseResponseApiError.txt');

        $response = new PurchaseResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $this->assertEquals('Kart numarasi gecersiz', $response->getMessage());

        $this->assertNull($response->getTransactionReference());
    }
}
