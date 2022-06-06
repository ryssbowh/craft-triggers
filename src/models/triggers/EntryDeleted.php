<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Entry;
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
        Event::on(Entry::class, Element::EVENT_AFTER_DELETE, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'entry' => $e->sender,
                'event' => $e
            ]);
        });
    }
}