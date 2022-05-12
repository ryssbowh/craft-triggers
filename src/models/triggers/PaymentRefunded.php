<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\commerce\events\RefundTransactionEvent;
use craft\commerce\services\Payments;
use yii\base\Event;

class PaymentRefunded extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a payment is refunded');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'payment-refunded';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Payments::class, Payments::EVENT_AFTER_REFUND_TRANSACTION, function (RefundTransactionEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'transaction' => $e->transaction,
                'refundTransaction' => $e->refundTransaction,
                'amount' => $e->amount,
                'event' => $e
            ]);
        });
    }
}