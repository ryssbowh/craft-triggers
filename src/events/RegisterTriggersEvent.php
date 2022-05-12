<?php 

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\exceptions\TriggerException;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\triggers\AssetDeleted;
use Ryssbowh\CraftTriggers\models\triggers\AssetSaved;
use Ryssbowh\CraftTriggers\models\triggers\CategoryDeleted;
use Ryssbowh\CraftTriggers\models\triggers\CategorySaved;
use Ryssbowh\CraftTriggers\models\triggers\Custom;
use Ryssbowh\CraftTriggers\models\triggers\EntryDeleted;
use Ryssbowh\CraftTriggers\models\triggers\EntrySaved;
use Ryssbowh\CraftTriggers\models\triggers\OrderAuthorized;
use Ryssbowh\CraftTriggers\models\triggers\OrderCompleted;
use Ryssbowh\CraftTriggers\models\triggers\OrderDeleted;
use Ryssbowh\CraftTriggers\models\triggers\OrderPaid;
use Ryssbowh\CraftTriggers\models\triggers\OrderSaved;
use Ryssbowh\CraftTriggers\models\triggers\PaymentCaptured;
use Ryssbowh\CraftTriggers\models\triggers\PaymentComplete;
use Ryssbowh\CraftTriggers\models\triggers\PaymentProcessed;
use Ryssbowh\CraftTriggers\models\triggers\PaymentRefunded;
use Ryssbowh\CraftTriggers\models\triggers\ProductDeleted;
use Ryssbowh\CraftTriggers\models\triggers\ProductSaved;
use Ryssbowh\CraftTriggers\models\triggers\UserActivated;
use Ryssbowh\CraftTriggers\models\triggers\UserAssignedToGroups;
use Ryssbowh\CraftTriggers\models\triggers\UserDeleted;
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
            new EntryDeleted,
            new AssetSaved,
            new AssetDeleted,
            new CategorySaved,
            new CategoryDeleted,
            new UserSaved,
            new UserDeleted,
            new UserEmailVerified,
            new UserActivated,
            new UserLocked,
            new UserUnlocked,
            new UserSuspended,
            new UserUnsuspended,
            new UserAssignedToGroups,
            new UserFailsToLogin,
            new Custom,
        ]);
        if (\Craft::$app->plugins->isPluginInstalled('commerce')) {
            $this->addMany([
                new OrderDeleted,
                new OrderSaved,
                new OrderAuthorized,
                new OrderCompleted,
                new OrderPaid,
                new PaymentCaptured,
                new PaymentComplete,
                new PaymentProcessed,
                new PaymentRefunded,
                new ProductDeleted,
                new ProductSaved
            ]);
        }
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