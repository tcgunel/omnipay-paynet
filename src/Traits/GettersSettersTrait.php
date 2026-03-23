<?php

namespace Omnipay\Paynet\Traits;

trait GettersSettersTrait
{
	public function getSecretKey()
	{
		return $this->getParameter('secretKey');
	}

	public function setSecretKey($value)
	{
		return $this->setParameter('secretKey', $value);
	}

	public function getInstallment()
	{
		return $this->getParameter('installment');
	}

	public function setInstallment($value)
	{
		return $this->setParameter('installment', $value);
	}

	public function getAddCommission()
	{
		return $this->getParameter('addCommission');
	}

	public function setAddCommission($value)
	{
		return $this->setParameter('addCommission', $value);
	}

	public function getDomain()
	{
		return $this->getParameter('domain');
	}

	public function setDomain($value)
	{
		return $this->setParameter('domain', $value);
	}

	public function getXactId()
	{
		return $this->getParameter('xactId');
	}

	public function setXactId($value)
	{
		return $this->setParameter('xactId', $value);
	}

	public function getSessionId()
	{
		return $this->getParameter('sessionId');
	}

	public function setSessionId($value)
	{
		return $this->setParameter('sessionId', $value);
	}

	public function getTokenId()
	{
		return $this->getParameter('tokenId');
	}

	public function setTokenId($value)
	{
		return $this->setParameter('tokenId', $value);
	}

	public function getBin()
	{
		return $this->getParameter('bin');
	}

	public function setBin($value)
	{
		return $this->setParameter('bin', $value);
	}

	public function getEndpoint()
	{
		return $this->endpoint;
	}
}
