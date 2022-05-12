<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\commerce\elements\Order;
use yii\base\Event;

class OrderSaved extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an order is saved');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'order-saved';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Order::class, Element::EVENT_AFTER_SAVE, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'order' => $e->sender,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}