<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use craft\events\ModelEvent;
use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\elements\Entry;
use yii\base\Event;

class EntrySaved extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an entry is saved');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry-saved';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Entry::class, Entry::EVENT_AFTER_SAVE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'entry' => $e->sender,
                'isNew' => $e->sender->firstSave,
                'event' => $e
            ]);
        });
    }
}