<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\elements\User;
use craft\events\ElementEvent;
use craft\services\Elements;
use yii\base\Event;

class UserDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Elements::class, Elements::EVENT_AFTER_DELETE_ELEMENT, function (ElementEvent $e) use ($_this) {
            if (!$e->element instanceof User) {
                return;
            }
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->element,
                'event' => $e
            ]);
        });
    }
}