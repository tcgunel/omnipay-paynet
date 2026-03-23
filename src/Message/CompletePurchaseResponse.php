<?php

namespace Omnipay\Paynet\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Paynet Complete Purchase Response (3D Secure Charge)
 *
 * Successful response contains is_succeed=true, xact_id, and reference_no.
 * Error response contains paynet_error_message.
 */
class CompletePurchaseResponse extends AbstractResponse
{
	protected $response;

	protected $request;

	public function __construct(RequestInterface $request, $data)
	{
		parent::__construct($request, $data);

		$this->request = $request;

		$this->response = $data;

		if ($data instanceof ResponseInterface) {

			$body = (string)$data->getBody();

			try {

				$this->response = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

			} catch (JsonException $e) {

				$this->response = [
					'is_succeed'           => false,
					'paynet_error_message' => $body,
				];

			}

		}
	}

	public function isSuccessful(): bool
	{
		return !empty($this->response['is_succeed']) && $this->response['is_succeed'] === true;
	}

	public function getMessage(): ?string
	{
		return $this->response['paynet_error_message'] ?? $this->response['message'] ?? null;
	}

	public function getTransactionReference(): ?string
	{
		return $this->response['xact_id'] ?? null;
	}

	public function getTransactionId(): ?string
	{
		return $this->response['reference_no'] ?? null;
	}

	public function getData(): array
	{
		return is_array($this->response) ? $this->response : [];
	}

	public function getCode(): ?string
	{
		return isset($this->response['code']) ? (string)$this->response['code'] : null;
	}

	public function getRedirectData()
	{
		return null;
	}

	public function getRedirectUrl(): string
	{
		return '';
	}
}
