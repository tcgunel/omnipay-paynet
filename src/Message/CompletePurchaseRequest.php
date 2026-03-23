<?php

namespace Omnipay\Paynet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Paynet\Constants\TransactionType;
use Omnipay\Paynet\Traits\GettersSettersTrait;

/**
 * Paynet Complete Purchase Request (3D Secure Charge)
 *
 * After the 3D redirect callback, use session_id and token_id
 * to finalize the payment.
 * Endpoint: /v2/transaction/tds_charge
 */
class CompletePurchaseRequest extends RemoteAbstractRequest
{
	use GettersSettersTrait;

	protected $endpoint = '/v2/transaction/tds_charge';

	/**
	 * @throws InvalidRequestException
	 */
	public function getData(): array
	{
		$this->validateAll();

		return [
			'session_id'       => $this->getSessionId(),
			'token_id'         => $this->getTokenId(),
			'transaction_type' => TransactionType::SALE,
		];
	}

	/**
	 * @throws InvalidRequestException
	 */
	protected function validateAll(): void
	{
		$this->validateSettings();

		$this->validate("sessionId", "tokenId");
	}

	/**
	 * @param array $data
	 * @return ResponseInterface|CompletePurchaseResponse
	 */
	public function sendData($data)
	{
		$url = $this->getBaseUrl() . $this->endpoint;

		$httpResponse = $this->sendRequest($data, $url);

		return $this->createResponse($httpResponse);
	}

	protected function createResponse($data): CompletePurchaseResponse
	{
		return $this->response = new CompletePurchaseResponse($this, $data);
	}
}
