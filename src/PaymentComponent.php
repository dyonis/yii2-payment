<?php

namespace dyonis\yii2\payment;

use Closure;
use dyonis\yii2\payment\exceptions\ProviderNotFoundException;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class PaymentComponent extends Component
{
    public ?string $defaultProvider = null;

    /**
     * @var array|BasePaymentProvider[] Providers config
     */
    public array $providers = [];

    public ?Closure $checkCallback = null;
    public ?Closure $successCallback = null;
    public ?Closure $failCallback = null;

    public function init()// called each time when DI is used (wrong!)
    {
        parent::init();

        if (!$this->providers) {
            throw new InvalidConfigException('At least one payment provider must be configured');
        }

        foreach ($this->providers as $name => $providerConfig) {
            /** @var BasePaymentProvider $provider */
            $provider = Yii::createObject($providerConfig);
            $provider->setParent($this);
            $this->providers[$name] = $provider;
        }

        $this->defaultProvider = $this->defaultProvider ?: array_key_first($this->providers);
    }

    public function getProvider(string $providerId): PaymentProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->getName() == $providerId) {
                return $provider;
            }
        }

        $provider = $this->providers[$providerId] ?? null;

        if (!$provider) {
            throw new ProviderNotFoundException("Provider '$providerId' is not registered.");
        }

        return $provider;
    }

    public function getDefaultProvider(): PaymentProviderInterface
    {
        return $this->getProvider($this->defaultProvider);
    }
}
