<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\events\UserEvent;
use craft\services\Users;
use yii\base\Event;

class UserActivated extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user is activated');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-activated';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Users::class, Users::EVENT_AFTER_ACTIVATE_USER, function (UserEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->user,
                'event' => $e
            ]);
        });
    }
}