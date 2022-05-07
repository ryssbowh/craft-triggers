<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Category;
use craft\events\ModelEvent;
use yii\base\Event;

class CategorySaved extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a category is saved');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'category-saved';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Category::class, Element::EVENT_AFTER_SAVE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'category' => $e->sender,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}