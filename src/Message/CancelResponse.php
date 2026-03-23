<?php

namespace Omnipay\Paynet\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Paynet\Constants\ResultCode;
use Psr\Http\Message\ResponseInterface;

/**
 * Paynet Cancel (Void) Response
 *
 * Successful response has code 0 or 100.
 */
class CancelResponse extends AbstractResponse
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
					'code'    => -1,
					'message' => $body,
				];

			}

		}
	}

	public function isSuccessful(): bool
	{
		$code = $this->response['code'] ?? -1;

		return $code === ResultCode::SUCCESS || $code === ResultCode::SUCCESS_ALT;
	}

	public function getMessage(): ?string
	{
		return $this->response['message'] ?? null;
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
