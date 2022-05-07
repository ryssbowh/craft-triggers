<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\User;
use craft\events\ModelEvent;
use yii\base\Event;

class UserSaved extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user is saved');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-saved';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(User::class, Element::EVENT_AFTER_SAVE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->sender,
                'event' => $e
            ]);
        });
    }
}