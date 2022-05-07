<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\controllers\UsersController;
use craft\events\LoginFailureEvent;
use craft\services\Users;
use yii\base\Event;

class UserFailsToLogin extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user fails to login');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-fails-login';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(UsersController::class, UsersController::EVENT_LOGIN_FAILURE, function (LoginFailureEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->user,
                'event' => $e
            ]);
        });
    }
}