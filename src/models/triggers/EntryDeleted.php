<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\services\Elements;
use yii\base\Event;

class EntryDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an entry is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Elements::class, Elements::EVENT_AFTER_DELETE_ELEMENT, function (ElementEvent $e) use ($_this) {
            if (!$e->element instanceof Entry) {
                return;
            }
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'entry' => $e->element,
                'event' => $e
            ]);
        });
    }
}