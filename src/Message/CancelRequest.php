<?php

namespace Omnipay\Paynet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Paynet\Traits\GettersSettersTrait;

/**
 * Paynet Cancel (Void) Request
 *
 * Cancels a transaction by xact_id.
 * Endpoint: /v1/transaction/reversed_request
 */
class CancelRequest extends RemoteAbstractRequest
{
	use GettersSettersTrait;

	protected $endpoint = '/v1/transaction/reversed_request';

	/**
	 * @throws InvalidRequestException
	 */
	public function getData(): array
	{
		$this->validateAll();

		return [
			'xact_id' => $this->getXactId(),
		];
	}

	/**
	 * @throws InvalidRequestException
	 */
	protected function validateAll(): void
	{
		$this->validateSettings();

		$this->validate("xactId");
	}

	/**
	 * @param array $data
	 * @return ResponseInterface|CancelResponse
	 */
	public function sendData($data)
	{
		$url = $this->getBaseUrl() . $this->endpoint;

		$httpResponse = $this->sendRequest($data, $url);

		return $this->createResponse($httpResponse);
	}

	protected function createResponse($data): CancelResponse
	{
		return $this->response = new CancelResponse($this, $data);
	}
}
