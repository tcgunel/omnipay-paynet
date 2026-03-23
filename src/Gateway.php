<?php

namespace Omnipay\Paynet;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Paynet\Message\CancelRequest;
use Omnipay\Paynet\Message\CompletePurchaseRequest;
use Omnipay\Paynet\Message\InstallmentQueryRequest;
use Omnipay\Paynet\Message\Purchase3dRequest;
use Omnipay\Paynet\Message\PurchaseRequest;
use Omnipay\Paynet\Message\RefundRequest;
use Omnipay\Paynet\Traits\GettersSettersTrait;

/**
 * Paynet Gateway
 * (c) Tolga Can Gunel
 * 2015, mobius.studio
 * http://www.github.com/tcgunel/omnipay-paynet
 *
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface authorize(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface capture(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = [])
 */
class Gateway extends AbstractGateway
{
	use GettersSettersTrait;

	public function getName(): string
	{
		return 'Paynet';
	}

	public function getDefaultParameters(): array
	{
		return [
			"secretKey"     => "",
			"installment"   => 1,
			"addCommission" => false,
			"testMode"      => false,
		];
	}

	/**
	 * Direct (non-3D) sale.
	 *
	 * @param array $options
	 * @return AbstractRequest|PurchaseRequest
	 */
	public function purchase(array $options = []): AbstractRequest
	{
		return $this->createRequest(PurchaseRequest::class, $options);
	}

	/**
	 * 3D Secure sale initiation.
	 *
	 * @param array $options
	 * @return AbstractRequest|Purchase3dRequest
	 */
	public function purchase3d(array $options = []): AbstractRequest
	{
		return $this->createRequest(Purchase3dRequest::class, $options);
	}

	/**
	 * Complete a 3D Secure purchase (tds_charge).
	 *
	 * @param array $options
	 * @return AbstractRequest|CompletePurchaseRequest
	 */
	public function completePurchase(array $options = []): AbstractRequest
	{
		return $this->createRequest(CompletePurchaseRequest::class, $options);
	}

	/**
	 * Cancel (void) a transaction.
	 *
	 * @param array $options
	 * @return AbstractRequest|CancelRequest
	 */
	public function void(array $options = []): AbstractRequest
	{
		return $this->createRequest(CancelRequest::class, $options);
	}

	/**
	 * Refund a transaction (partial or full).
	 *
	 * @param array $options
	 * @return AbstractRequest|RefundRequest
	 */
	public function refund(array $options = []): AbstractRequest
	{
		return $this->createRequest(RefundRequest::class, $options);
	}

	/**
	 * Query installment rates by BIN number.
	 *
	 * @param array $options
	 * @return AbstractRequest|InstallmentQueryRequest
	 */
	public function installmentQuery(array $options = []): AbstractRequest
	{
		return $this->createRequest(InstallmentQueryRequest::class, $options);
	}
}
