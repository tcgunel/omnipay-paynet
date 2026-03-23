<?php

namespace Omnipay\Paynet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Paynet\Traits\GettersSettersTrait;

abstract class RemoteAbstractRequest extends AbstractRequest
{
    use GettersSettersTrait;

    protected $testEndpoint = 'https://pts-api.paynet.com.tr';

    protected $liveEndpoint = 'https://api.paynet.com.tr';

    /**
     * @throws InvalidRequestException
     */
    protected function validateSettings(): void
    {
        $this->validate('secretKey');
    }

    protected function getBaseUrl(): string
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function get_card($key)
    {
        return $this->getCard() ? $this->getCard()->$key() : null;
    }

    /**
     * Send a JSON POST request to Paynet API with Basic auth.
     *
     * @param array $data
     * @param string $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function sendRequest(array $data, string $url)
    {
        return $this->httpClient->request(
            'POST',
            $url,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->getSecretKey(),
            ],
            json_encode($data)
        );
    }

    abstract protected function createResponse($data);
}
