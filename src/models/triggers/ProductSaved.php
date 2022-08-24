<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\commerce\elements\Product;
use craft\events\ElementEvent;
use craft\services\Elements;
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
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function (ElementEvent $e) use ($_this) {
            if (!$e->element instanceof Product) {
                return;
            }
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'product' => $e->element,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}