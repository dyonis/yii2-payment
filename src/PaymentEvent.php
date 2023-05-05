<?php

namespace dyonis\yii2\payment;

use dyonis\yii2\payment\response\BaseResponse;
use yii\base\Event;

class PaymentEvent extends Event
{
    public ?BaseResponse $paymentResponse = null;
}
