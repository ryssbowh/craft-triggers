<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\events\UserEvent;
use craft\services\Users;
use yii\base\Event;

class UserUnlocked extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user is unlocked');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-unlocked';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Users::class, Users::EVENT_BEFORE_UNLOCK_USER, function (UserEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->user,
                'event' => $e
            ]);
        });
    }
}