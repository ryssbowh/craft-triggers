<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Category;
use craft\events\ModelEvent;
use yii\base\Event;

class CategoryDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a category is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'category-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Category::class, Element::EVENT_AFTER_DELETE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'category' => $e->sender,
                'event' => $e
            ]);
        });
    }
}