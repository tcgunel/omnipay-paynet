<?php

namespace Omnipay\Paynet\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Paynet\Constants\ResultCode;
use Psr\Http\Message\ResponseInterface;

/**
 * Paynet Installment Query Response
 *
 * Returns installment rates for a given BIN.
 * Response structure: code, message, data[].ratio[]
 */
class InstallmentQueryResponse extends AbstractResponse
{
    protected $response;

    protected $request;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->request = $request;

        $this->response = $data;

        if ($data instanceof ResponseInterface) {

            $body = (string) $data->getBody();

            try {

                $this->response = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            } catch (JsonException $e) {

                $this->response = [
                    'code' => -1,
                    'message' => $body,
                ];

            }

        }
    }

    public function isSuccessful(): bool
    {
        $code = $this->response['code'] ?? -1;

        return $code === ResultCode::SUCCESS;
    }

    public function getMessage(): ?string
    {
        return $this->response['message'] ?? null;
    }

    /**
     * Get the installment data array.
     *
     * @return array
     */
    public function getInstallments(): array
    {
        $data = $this->response['data'] ?? [];

        if (!empty($data) && isset($data[0]['ratio'])) {
            return $data[0]['ratio'];
        }

        return [];
    }

    /**
     * Get bank information from the response.
     *
     * @return array|null
     */
    public function getBankInfo(): ?array
    {
        $data = $this->response['data'] ?? [];

        if (!empty($data)) {
            return [
                'bank_id' => $data[0]['bank_id'] ?? null,
                'bank_name' => $data[0]['bank_name'] ?? null,
                'bank_logo' => $data[0]['bank_logo'] ?? null,
                'card_type' => $data[0]['card_type'] ?? null,
            ];
        }

        return null;
    }

    /**
     * Whether 3D Secure is required for this BIN.
     *
     * @return bool
     */
    public function isTdsRequired(): bool
    {
        return !empty($this->response['tds_required']);
    }

    public function getData(): array
    {
        return is_array($this->response) ? $this->response : [];
    }

    public function getCode(): ?string
    {
        return isset($this->response['code']) ? (string) $this->response['code'] : null;
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
