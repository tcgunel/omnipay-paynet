<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Paynet\Message\Purchase3dRequest;
use Omnipay\Paynet\Message\Purchase3dResponse;
use Omnipay\Paynet\Tests\TestCase;

class Purchase3dTest extends TestCase
{
    /**
     * @throws InvalidRequestException
     * @throws InvalidCreditCardException
     * @throws \JsonException
     */
    public function test_purchase_3d_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/Purchase3dRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new Purchase3dRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        self::assertArrayHasKey('amount', $data);
        self::assertArrayHasKey('reference_no', $data);
        self::assertArrayHasKey('return_url', $data);
        self::assertArrayHasKey('domain', $data);
        self::assertArrayHasKey('pan', $data);
        self::assertArrayHasKey('transaction_type', $data);

        self::assertEquals('25000', $data['amount']);
        self::assertEquals('ORDER-3D-12345', $data['reference_no']);
        self::assertEquals('https://example.com/payment/callback', $data['return_url']);
        self::assertEquals('example.com', $data['domain']);
        self::assertEquals(3, $data['instalment']);
        self::assertTrue($data['add_commission']);
    }

    public function test_purchase_3d_request_validation_error_missing_return_url()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/Purchase3dRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new Purchase3dRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    public function test_purchase_3d_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('Purchase3dResponseSuccess.txt');

        $response = new Purchase3dResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $this->assertTrue($response->isRedirect());

        $this->assertNotNull($response->getHtmlContent());

        $this->assertStringContainsString('3dform', $response->getHtmlContent());
    }

    public function test_purchase_3d_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('Purchase3dResponseApiError.txt');

        $response = new Purchase3dResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $this->assertFalse($response->isRedirect());

        $this->assertEquals('3D Secure baslatma hatasi', $response->getMessage());
    }
}
