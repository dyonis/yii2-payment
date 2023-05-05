<?php

namespace dyonis\yii2\payment\response;

use yii\base\BaseObject;

abstract class BaseResponse extends BaseObject
{
    public string $paySystemName;
    public int $amount = 0;
    public string $currency;
    public bool $testMode = false;

    /**
     * Local Invoice ID
     */
    public string $invoiceId;

    /**
     * Local User ID
     */
    public int $userId;

    /**
     * @var mixed Transaction ID in payment system
     */
    public $transactionId;

    /**
     * Payment payload payment parameters
     */
    public ?array $payload = null;

    /**
     * Payment system raw data
     */
    public array $data;
}
