<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\events\UserEvent;
use craft\services\Users;
use yii\base\Event;

class UserEmailVerified extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user email is verified');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-email-verified';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Users::class, Users::EVENT_AFTER_VERIFY_EMAIL, function (UserEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->user,
                'event' => $e
            ]);
        });
    }
}