<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\commerce\events\TransactionEvent;
use craft\commerce\services\Payments;
use yii\base\Event;

class PaymentComplete extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a payment is complete');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'payment-complete';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Payments::class, Payments::EVENT_AFTER_COMPLETE_PAYMENT, function (TransactionEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'transaction' => $e->transaction,
                'event' => $e
            ]);
        });
    }
}