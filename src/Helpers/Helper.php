<?php

namespace Omnipay\Paynet\Helpers;

class Helper
{
	/**
	 * Format amount to Paynet format: no dots, no commas (e.g., 10.50 => "1050").
	 *
	 * @param float|string $amount
	 * @return string
	 */
	public static function formatAmount($amount): string
	{
		return number_format((float)$amount, 2, '', '');
	}

	/**
	 * Extract domain from a URL.
	 *
	 * @param string|null $url
	 * @return string
	 */
	public static function extractDomain(?string $url): string
	{
		if (empty($url)) {
			return 'localhost';
		}

		try {
			$parsed = parse_url($url);

			return $parsed['host'] ?? 'localhost';
		} catch (\Throwable $e) {
			return 'localhost';
		}
	}

	/**
	 * Pretty print data for debugging.
	 *
	 * @param mixed $data
	 */
	public static function prettyPrint($data): void
	{
		echo "<pre>" . print_r($data, true) . "</pre>";
	}
}
