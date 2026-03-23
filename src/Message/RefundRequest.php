<?php

namespace Omnipay\Paynet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Paynet\Helpers\Helper;
use Omnipay\Paynet\Traits\GettersSettersTrait;

/**
 * Paynet Refund Request
 *
 * Refunds a transaction by xact_id with a given amount.
 * Uses the same endpoint as cancel but includes amount.
 * Endpoint: /v1/transaction/reversed_request
 */
class RefundRequest extends RemoteAbstractRequest
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
			'amount'  => Helper::formatAmount($this->getAmount()),
		];
	}

	/**
	 * @throws InvalidRequestException
	 */
	protected function validateAll(): void
	{
		$this->validateSettings();

		$this->validate("xactId", "amount");
	}

	/**
	 * @param array $data
	 * @return ResponseInterface|RefundResponse
	 */
	public function sendData($data)
	{
		$url = $this->getBaseUrl() . $this->endpoint;

		$httpResponse = $this->sendRequest($data, $url);

		return $this->createResponse($httpResponse);
	}

	protected function createResponse($data): RefundResponse
	{
		return $this->response = new RefundResponse($this, $data);
	}
}
