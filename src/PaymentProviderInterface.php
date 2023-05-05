<?php

namespace dyonis\yii2\payment;

use yii\web\Request;
use yii\web\Response;

interface PaymentProviderInterface
{
    public function processCheckRequest(Request $request, Response $response): Response;
    public function processPaymentRequest(Request $request, Response $response): Response;
    public function processFailRequest(Request $request, Response $response): Response;
}
