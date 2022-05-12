<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\commerce\events\ProcessPaymentEvent;
use craft\commerce\services\Payments;
use yii\base\Event;

class PaymentProcessed extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a payment is processed');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'payment-processed';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Payments::class, Payments::EVENT_AFTER_PROCESS_PAYMENT, function (ProcessPaymentEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'order' => $e->order,
                'transaction' => $e->transaction,
                'form' => $e->form,
                'response' => $e->response,
                'event' => $e
            ]);
        });
    }
}