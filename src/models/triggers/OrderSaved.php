<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\commerce\elements\Order;
use craft\events\ElementEvent;
use craft\services\Elements;
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
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function (ElementEvent $e) use ($_this) {
            if (!$e->element instanceof Order) {
                return;
            }
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'order' => $e->element,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}