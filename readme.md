# Payment component for Yii 2

## Configuration
Add to `config/main.php`
```php
'components' => [
...
    'payment' => [
        'class' => dyonis\yii2\payment\PaymentComponent::class,
        'defaultProvider' => 'stripe',
        'providers' => [
            'stripe' => [
                'class' => dyonis\yii2\payment\providers\stripe\StripeProvider::class,
                'responseUrl' => 'https://your-site.com/payment/stripe/process',
                'publicKey' => '',
                'secretKey' => '',
                'hookKey' => '',
                'deviceName' => '',
                'on payment-success' => [my\response\Processor::class, 'processStripe'],
            ],
            'cloudPayments' => [
                'class' => dyonis\yii2\payment\providers\cloudPayments\CloudPaymentsProvider::class,
                'responseUrl' => 'https://your-site.com/payment/cloudPayments/process',
                'publicId' => '',
                'apiKey' => '',
                'on payment-success' => [my\response\Processor::class, 'processCloudPayments'],
            ],
            // ... Other payment systems
        ],
        'on payment-success' => [my\response\Processor::class, 'processCommon'],
        'on payment-fail' => [my\response\Processor::class, 'processFail'],
    ],
],

```

## Usage

Controller

```php
class PaymentController extends Controller
{
    /**
     * Url: https://site.com/payment/process?providerId=stripe
     */
    public function actionProcess(string $providerId): Response
    {
        try {
            $provider = Yii::$app->payment->getProvider($providerId);
            $response = $provider->processPaymentRequest($this->request, $this->response);
            
            return $response;
        } catch (ProviderNotFoundException $e) {
            // Provider not found
        } catch (PaymentException $e) {
            // Payment error
        }
    }
}
```

Payment processor class
```php
class Processor 
{
    public static function processSuccessEvent(PaymentEvent $event)
    {
        /** @var PaymentResponse $paymentResponse */
        $paymentResponse = $event->paymentData;
        
        // Do stuff...
    }
}

```
