<?php

namespace dyonis\yii2\payment;

use Yii;
use yii\base\BaseObject;

class PaymentLogger extends BaseObject
{
    const CATEGORY = 'payment';

    const TYPE_DEBUG = 'debug';
    const TYPE_INFO = 'info';
    const TYPE_ERROR = 'error';

    private ?string $message = null;
    private string $type = self::TYPE_INFO;

    /** @var mixed $data */
    private $data;

    private BasePaymentProvider $provider;

    public function log()
    {
        $msg = [
            'type' => $this->type,
            'provider' => $this->provider->name ?: get_class($this->provider),
            'data' => $this->data,
        ];

        if ($this->message) {
            $msg['message'] = $this->message;
        }

        switch ($this->type) {
            case self::TYPE_DEBUG:
                Yii::debug($msg, self::CATEGORY);

                break;
            case self::TYPE_INFO:
                Yii::info($msg, self::CATEGORY);

                break;
            case self::TYPE_ERROR:
                Yii::error($msg, self::CATEGORY);

                break;
            default: ;
        }
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setProvider(BasePaymentProvider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
