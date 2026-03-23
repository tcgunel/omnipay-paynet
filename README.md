[![License](https://poser.pugx.org/tcgunel/omnipay-paynet/license)](https://packagist.org/packages/tcgunel/omnipay-paynet)

# Omnipay Paynet Gateway
Omnipay gateway for Paynet - REST JSON API. All available methods of Paynet implemented for easy usage.

## Requirements
| PHP   | Package |
|-------|---------|
| ^8.0  | v1.0.0  |

## Installation

```
composer require tcgunel/omnipay-paynet
```

## Usage

### Gateway Initialization

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Paynet');

$gateway->setSecretKey('YOUR_BASE64_ENCODED_SECRET_KEY');
$gateway->setTestMode(true); // Use test environment
```

### Authentication

Paynet uses **Basic Authentication** with a base64-encoded secret key. Set this via `setSecretKey()`.

- **Test URL:** `https://pts-api.paynet.com.tr`
- **Live URL:** `https://api.paynet.com.tr`

## Methods

### Payment Services

#### Direct Sale (Non-3D)

```php
$response = $gateway->purchase([
    'amount'        => '150.00',
    'transactionId' => 'ORDER-12345',
    'installment'   => 1,
    'card'          => [
        'firstName'   => 'Test',
        'lastName'    => 'User',
        'number'      => '4155650100416111',
        'expiryMonth' => '01',
        'expiryYear'  => '2030',
        'cvv'         => '123',
        'email'       => 'test@example.com',
        'phone'       => '5551234567',
    ],
])->send();

if ($response->isSuccessful()) {
    $transactionRef = $response->getTransactionReference(); // xact_id
}
```

#### 3D Secure Sale

```php
// Step 1: Initiate 3D Secure
$response = $gateway->purchase3d([
    'amount'        => '250.00',
    'transactionId' => 'ORDER-3D-12345',
    'installment'   => 3,
    'returnUrl'     => 'https://example.com/payment/callback',
    'card'          => [
        'firstName'   => 'Test',
        'lastName'    => 'User',
        'number'      => '4155650100416111',
        'expiryMonth' => '01',
        'expiryYear'  => '2030',
        'cvv'         => '123',
        'email'       => 'test@example.com',
        'phone'       => '5551234567',
    ],
])->send();

if ($response->isRedirect()) {
    $htmlContent = $response->getHtmlContent();
    // Render $htmlContent to redirect user to 3D Secure page
}
```

```php
// Step 2: Complete 3D Secure (after callback)
$response = $gateway->completePurchase([
    'sessionId' => $_POST['session_id'],
    'tokenId'   => $_POST['token_id'],
])->send();

if ($response->isSuccessful()) {
    $transactionRef = $response->getTransactionReference(); // xact_id
    $orderNumber    = $response->getTransactionId();        // reference_no
}
```

#### Cancel (Void)

```php
$response = $gateway->void([
    'xactId' => 'PAY-TXN-00001',
])->send();

if ($response->isSuccessful()) {
    echo $response->getMessage();
}
```

#### Refund

```php
$response = $gateway->refund([
    'xactId' => 'PAY-TXN-00001',
    'amount' => '50.00',
])->send();

if ($response->isSuccessful()) {
    echo $response->getMessage();
}
```

### Query Services

#### Installment Query (BIN Based)

```php
$response = $gateway->installmentQuery([
    'bin'           => '415565',
    'amount'        => '100.00',
    'addCommission' => true,
])->send();

if ($response->isSuccessful()) {
    $installments = $response->getInstallments();
    $bankInfo     = $response->getBankInfo();
    $tdsRequired  = $response->isTdsRequired();

    foreach ($installments as $inst) {
        echo "Taksit: {$inst['instalment']}, Toplam: {$inst['total_amount']}\n";
    }
}
```

## API Endpoints

| Method            | Endpoint                            | Description                       |
|-------------------|-------------------------------------|-----------------------------------|
| purchase          | /v2/transaction/payment             | Direct (non-3D) sale              |
| purchase3d        | /v2/transaction/tds_initial         | 3D Secure sale initiation         |
| completePurchase  | /v2/transaction/tds_charge          | Complete 3D Secure payment        |
| void              | /v1/transaction/reversed_request    | Cancel (void) a transaction       |
| refund            | /v1/transaction/reversed_request    | Refund a transaction              |
| installmentQuery  | /v1/ratio/Get                       | Query installment rates by BIN    |

## Test Credentials

Contact Paynet for sandbox credentials. Test environment is available at `https://pts-api.paynet.com.tr`.

Set `testMode` to `true` to use the test environment:

```php
$gateway->setTestMode(true);
```

## Tests
```
composer test
```
For Windows:
```
vendor\bin\paratest.bat
```

## License

MIT
