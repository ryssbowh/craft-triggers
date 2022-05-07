<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\events\UserEvent;
use craft\services\Users;
use yii\base\Event;

class UserAssignedToGroups extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When a user is assigned to groups');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-assigned-groups';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Users::class, Users::EVENT_AFTER_ASSIGN_USER_TO_GROUPS, function (UserEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'user' => $e->user,
                'event' => $e
            ]);
        });
    }
}