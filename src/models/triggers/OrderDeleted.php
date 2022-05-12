<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\commerce\elements\Order;
use yii\base\Event;

class OrderDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an order is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'order-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Order::class, Element::EVENT_AFTER_DELETE, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'order' => $e->sender,
                'event' => $e
            ]);
        });
    }
}