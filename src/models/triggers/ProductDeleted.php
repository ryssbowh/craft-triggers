<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\commerce\elements\Product;
use yii\base\Event;

class ProductDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a product is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'product-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Product::class, Element::EVENT_AFTER_DELETE, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'product' => $e->sender,
                'event' => $e
            ]);
        });
    }
}