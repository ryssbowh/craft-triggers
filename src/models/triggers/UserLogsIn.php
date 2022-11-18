<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\web\User;
use yii\base\Event;

class UserLogsIn extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user logs in');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-logs-in';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(User::class, User::EVENT_AFTER_LOGIN, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->identity,
                'event' => $e
            ]);
        });
    }
}