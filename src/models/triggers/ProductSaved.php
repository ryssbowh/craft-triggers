<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\events\ModelEvent;
use yii\base\Event;

class ProductSaved extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a product is saved');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'product-saved';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Product::class, Element::EVENT_AFTER_SAVE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'product' => $e->sender,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}