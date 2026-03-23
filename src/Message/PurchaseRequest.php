<?php

namespace Omnipay\Paynet\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Paynet\Constants\TransactionType;
use Omnipay\Paynet\Helpers\Helper;
use Omnipay\Paynet\Traits\GettersSettersTrait;

/**
 * Paynet Purchase (Sale) Request
 *
 * Sends a direct (non-3D) payment request to Paynet.
 * Endpoint: /v2/transaction/payment
 */
class PurchaseRequest extends RemoteAbstractRequest
{
    use GettersSettersTrait;

    protected $endpoint = '/v2/transaction/payment';

    /**
     * @throws InvalidRequestException
     * @throws InvalidCreditCardException
     */
    public function getData(): array
    {
        $this->validateAll();

        $installment = (int) ($this->getInstallment() ?: 1);

        $domain = $this->getDomain()
            ?: Helper::extractDomain($this->getReturnUrl());

        return [
            'amount' => Helper::formatAmount($this->getAmount()),
            'reference_no' => $this->getTransactionId(),
            'domain' => $domain,
            'card_holder' => $this->get_card('getName'),
            'pan' => $this->get_card('getNumber'),
            'month' => $this->get_card('getExpiryMonth'),
            'year' => $this->get_card('getExpiryYear'),
            'cvc' => $this->get_card('getCvv'),
            'card_holder_phone' => $this->get_card('getPhone') ?? '',
            'card_holder_mail' => $this->get_card('getEmail') ?? '',
            'instalment' => $installment,
            'add_commission' => $installment > 1 ? ($this->getAddCommission() ?? true) : false,
            'transaction_type' => TransactionType::SALE,
        ];
    }

    /**
     * @throws InvalidRequestException
     * @throws InvalidCreditCardException
     */
    protected function validateAll(): void
    {
        $this->validateSettings();

        $this->getCard()->validate();

        $this->validate('amount', 'transactionId');
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $url = $this->getBaseUrl() . $this->endpoint;

        $httpResponse = $this->sendRequest($data, $url);

        return $this->createResponse($httpResponse);
    }

    protected function createResponse($data): PurchaseResponse
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
