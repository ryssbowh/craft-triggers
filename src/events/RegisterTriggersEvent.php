<?php 

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\exceptions\TriggerException;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\triggers\AssetDeleted;
use Ryssbowh\CraftTriggers\models\triggers\AssetSaved;
use Ryssbowh\CraftTriggers\models\triggers\Custom;
use Ryssbowh\CraftTriggers\models\triggers\EntryDeleted;
use Ryssbowh\CraftTriggers\models\triggers\EntrySaved;
use Ryssbowh\CraftTriggers\models\triggers\UserActivated;
use Ryssbowh\CraftTriggers\models\triggers\UserAssignedToGroups;
use Ryssbowh\CraftTriggers\models\triggers\UserEmailVerified;
use Ryssbowh\CraftTriggers\models\triggers\UserFailsToLogin;
use Ryssbowh\CraftTriggers\models\triggers\UserLocked;
use Ryssbowh\CraftTriggers\models\triggers\UserSaved;
use Ryssbowh\CraftTriggers\models\triggers\UserSuspended;
use Ryssbowh\CraftTriggers\models\triggers\UserUnlocked;
use Ryssbowh\CraftTriggers\models\triggers\UserUnsuspended;
use yii\base\Event;

class RegisterTriggersEvent extends Event
{
    /**
     * @var array
     */
    public $_triggers = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->addMany([
            new EntrySaved,
            new AssetSaved,
            new UserSaved,
            new UserEmailVerified,
            new UserActivated,
            new UserLocked,
            new UserUnlocked,
            new UserSuspended,
            new UserUnsuspended,
            new UserAssignedToGroups,
            new AssetDeleted,
            new EntryDeleted,
            new UserFailsToLogin,
            new Custom,
        ]);
    }

    /**
     * Triggers getter
     * 
     * @return array
     */
    public function getTriggers(): array
    {
        return $this->_triggers;
    }

    /**
     * Add a trigger
     * 
     * @param TriggerInterface $trigger
     * @param bool             $replaceIfExists
     */
    public function add(TriggerInterface $trigger, bool $replaceIfExists = false)
    {
        $handle = $trigger->getHandle();
        if (isset($this->_triggers[$handle]) and !$replaceIfExists) {
            throw TriggerException::handleDefined($handle);
        }
        $this->_triggers[$handle] = $trigger;
    }

    /**
     * Add many triggers
     * 
     * @param array $triggers
     * @param bool  $replaceIfExists
     */
    public function addMany(array $triggers, bool $replaceIfExists = false)
    {
        foreach ($triggers as $trigger) {
            $this->add($trigger, $replaceIfExists);
        }
    }
}