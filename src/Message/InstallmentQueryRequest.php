<?php

namespace Omnipay\Paynet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Paynet\Helpers\Helper;
use Omnipay\Paynet\Traits\GettersSettersTrait;

/**
 * Paynet Installment Query Request (BIN based)
 *
 * Queries available installment rates for a card BIN number.
 * Endpoint: /v1/ratio/Get
 */
class InstallmentQueryRequest extends RemoteAbstractRequest
{
    use GettersSettersTrait;

    protected $endpoint = '/v1/ratio/Get';

    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validateAll();

        return [
            'bin' => $this->getBin(),
            'amount' => Helper::formatAmount($this->getAmount()),
            'addcomission_to_amount' => $this->getAddCommission() ?? true,
        ];
    }

    /**
     * @throws InvalidRequestException
     */
    protected function validateAll(): void
    {
        $this->validateSettings();

        $this->validate('bin', 'amount');
    }

    /**
     * @param array $data
     * @return ResponseInterface|InstallmentQueryResponse
     */
    public function sendData($data)
    {
        $url = $this->getBaseUrl() . $this->endpoint;

        $httpResponse = $this->sendRequest($data, $url);

        return $this->createResponse($httpResponse);
    }

    protected function createResponse($data): InstallmentQueryResponse
    {
        return $this->response = new InstallmentQueryResponse($this, $data);
    }
}
