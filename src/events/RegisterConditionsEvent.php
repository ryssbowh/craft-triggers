<?php 

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\exceptions\ConditionException;
use Ryssbowh\CraftTriggers\interfaces\ConditionInterface;
use Ryssbowh\CraftTriggers\models\conditions\AssetKind;
use Ryssbowh\CraftTriggers\models\conditions\AssetVolume;
use Ryssbowh\CraftTriggers\models\conditions\EntryDraft;
use Ryssbowh\CraftTriggers\models\conditions\EntryRevision;
use Ryssbowh\CraftTriggers\models\conditions\EntrySection;
use Ryssbowh\CraftTriggers\models\conditions\EntrySlug;
use Ryssbowh\CraftTriggers\models\conditions\EntryStatus;
use Ryssbowh\CraftTriggers\models\conditions\Environment;
use Ryssbowh\CraftTriggers\models\conditions\Group;
use Ryssbowh\CraftTriggers\models\conditions\IsNew;
use Ryssbowh\CraftTriggers\models\conditions\Request;
use Ryssbowh\CraftTriggers\models\conditions\Site;
use Ryssbowh\CraftTriggers\models\conditions\UserGroup;
use Ryssbowh\CraftTriggers\models\conditions\UserStatus;
use yii\base\Event;

class RegisterConditionsEvent extends Event
{
    /**
     * @var array
     */
    public $_conditions = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->addMany([
            new Group,
            new Environment,
            new EntrySection,
            new EntryStatus,
            new EntryDraft,
            new EntryRevision,
            new EntrySlug,
            new AssetVolume,
            new AssetKind,
            new IsNew,
            new Site,
            new Request,
            new UserStatus,
            new UserGroup,
        ]);
    }

    /**
     * Conditions getter
     * 
     * @return array
     */
    public function getConditions(): array
    {
        return $this->_conditions;
    }

    /**
     * Add a condition
     * 
     * @param ConditionInterface $condition
     * @param bool               $replaceIfExists
     */
    public function add(ConditionInterface $condition, bool $replaceIfExists = false)
    {
        $handle = $condition->getHandle();
        if (isset($this->_conditions[$handle]) and !$replaceIfExists) {
            throw ConditionException::handleDefined($handle);
        }
        $this->_conditions[$handle] = $condition;
    }

    /**
     * Add many conditions
     * 
     * @param array $conditions
     * @param bool  $replaceIfExists
     */
    public function addMany(array $conditions, bool $replaceIfExists = false)
    {
        foreach ($conditions as $condition) {
            $this->add($condition, $replaceIfExists);
        }
    }
}