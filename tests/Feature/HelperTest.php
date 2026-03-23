<?php

namespace Omnipay\Paynet\Tests\Feature;

use Omnipay\Paynet\Helpers\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
	public function test_format_amount_integer()
	{
		$this->assertEquals('1000', Helper::formatAmount(10.00));
	}

	public function test_format_amount_decimal()
	{
		$this->assertEquals('1050', Helper::formatAmount(10.50));
	}

	public function test_format_amount_string()
	{
		$this->assertEquals('15000', Helper::formatAmount('150.00'));
	}

	public function test_format_amount_small()
	{
		$this->assertEquals('100', Helper::formatAmount('1.00'));
	}

	public function test_format_amount_zero()
	{
		$this->assertEquals('000', Helper::formatAmount(0));
	}

	public function test_extract_domain_valid_url()
	{
		$this->assertEquals('example.com', Helper::extractDomain('https://example.com/payment/callback'));
	}

	public function test_extract_domain_with_subdomain()
	{
		$this->assertEquals('www.example.com', Helper::extractDomain('https://www.example.com/path'));
	}

	public function test_extract_domain_null()
	{
		$this->assertEquals('localhost', Helper::extractDomain(null));
	}

	public function test_extract_domain_empty()
	{
		$this->assertEquals('localhost', Helper::extractDomain(''));
	}
}
