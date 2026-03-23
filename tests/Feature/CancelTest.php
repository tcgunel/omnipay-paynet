<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Paynet\Message\CancelRequest;
use Omnipay\Paynet\Message\CancelResponse;
use Omnipay\Paynet\Tests\TestCase;

class CancelTest extends TestCase
{
    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function test_cancel_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/CancelRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new CancelRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        self::assertArrayHasKey('xact_id', $data);

        self::assertEquals('PAY-TXN-00001', $data['xact_id']);
    }

    public function test_cancel_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/CancelRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new CancelRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    public function test_cancel_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('CancelResponseSuccess.txt');

        $response = new CancelResponse($this->getMockRequest(), $httpResponse);

        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('Islem basariyla iptal edildi', $response->getMessage());
    }

    public function test_cancel_response_api_error()
    {
        $httpResponse = $this->getMockHttpResponse('CancelResponseApiError.txt');

        $response = new CancelResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $this->assertEquals('Iptal islemi sirasinda bir hata olustu', $response->getMessage());
    }
}
