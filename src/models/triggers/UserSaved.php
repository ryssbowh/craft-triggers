<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\elements\User;
use craft\events\ElementEvent;
use craft\services\Elements;
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
        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function (ElementEvent $e) use ($_this) {
            if (!$e->element instanceof User) {
                return;
            }
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'entry' => $e->element,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}