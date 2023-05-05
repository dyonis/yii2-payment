<?php

namespace dyonis\yii2\payment;

use Closure;
use dyonis\yii2\payment\response\BaseResponse;
use dyonis\yii2\payment\response\CheckResponse;
use dyonis\yii2\payment\response\FailResponse;
use dyonis\yii2\payment\response\SuccessResponse;
use yii\base\Component;
use yii\web\Request;
use yii\web\Response;

abstract class BasePaymentProvider extends Component implements PaymentProviderInterface
{
    const EVENT_PAYMENT_CHECK = 'payment-check';
    const EVENT_PAYMENT_SUCCESS = 'payment-success';
    const EVENT_PAYMENT_FAIL = 'payment-fail';

    public string $name = 'basePaymentProvider';

    public ?Closure $checkCallback = null;
    public ?Closure $successCallback = null;
    public ?Closure $failCallback = null;

    private ?PaymentComponent $parent = null;

    public function processCheckRequest(Request $request, Response $response): Response
    {
        try {
            $paymentResponse = new CheckResponse();
            // set response data here...
            $this->triggerPaymentCheck($paymentResponse);

            return $this->getSuccessResponse($response);
        } catch (\Exception $e) {
            return $this->getUnsuccessfulResponse($response);
        }
    }

    public function processPaymentRequest(Request $request, Response $response): Response
    {
        // Implement here payment processing logic

        try {
            $paymentResponse = new SuccessResponse();
            // set response data here...
            $this->triggerPaymentSuccess($paymentResponse);

            return $this->getSuccessResponse($response);
        } catch (\Exception $e) {
            return $this->getUnsuccessfulResponse($response);
        }
    }

    public function processFailRequest(Request $request, Response $response): Response
    {
        try {
            $paymentResponse = new FailResponse();
            // set response data here...
            $this->triggerPaymentFail($paymentResponse);

            return $this->getSuccessResponse($response);
        } catch (\Exception $e) {
            return $this->getUnsuccessfulResponse($response);
        }
    }

    /**
     * Trigger check event and run checkCallback
     */
    protected function triggerPaymentCheck(BaseResponse $response)
    {
        $this->triggerEvent(self::EVENT_PAYMENT_CHECK, $response);
        $this->runCallback($this->checkCallback, $response);
        $this->runCallback($this->parent->checkCallback, $response);
    }

    /**
     * Trigger success payment event and run successCallback
     */
    protected function triggerPaymentSuccess(BaseResponse $response)
    {
        $this->triggerEvent(self::EVENT_PAYMENT_SUCCESS, $response);
        $this->runCallback($this->successCallback, $response);
        $this->runCallback($this->parent->successCallback, $response);
    }

    /**
     * Trigger fail payment event and run failCallback
     */
    protected function triggerPaymentFail(BaseResponse $response)
    {
        $this->triggerEvent(self::EVENT_PAYMENT_FAIL, $response);
        $this->runCallback($this->failCallback, $response);
        $this->runCallback($this->parent->failCallback, $response);
    }

    /**
     * @return mixed|null
     */
    private function runCallback(?Closure $callback, BaseResponse $paymentResponse)
    {
        if (is_callable($callback)) {
            return call_user_func($callback, $paymentResponse);
        }

        return null;
    }

    private function triggerEvent(string $name, BaseResponse $paymentResponse)
    {
        $event = new PaymentEvent();
        $event->paymentResponse = $paymentResponse;

        $this->trigger($name, $event);
        $this->parent->trigger($name, $event);
    }

    /**
     * Send a response to the payment system
     * that the request has been successfully processed
     */
    protected function getSuccessResponse(Response $response): Response
    {
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * Send a response to the payment system
     * that the request was not processed correctly
     */
    protected function getUnsuccessfulResponse(Response $response): Response
    {
        $response->setStatusCode(503);

        return $response;
    }

    public function getParent(): PaymentComponent
    {
        return $this->parent;
    }

    public function setParent(PaymentComponent $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
