<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\commerce\elements\Order;
use yii\base\Event;

class OrderPaid extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an order is paid');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'order-paid';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Order::class, Order::EVENT_AFTER_ORDER_PAID, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'order' => $e->sender,
                'event' => $e
            ]);
        });
    }
}