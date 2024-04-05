<?php

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\exceptions\ConditionException;
use Ryssbowh\CraftTriggers\interfaces\ConditionInterface;
use Ryssbowh\CraftTriggers\models\conditions\AssetKind;
use Ryssbowh\CraftTriggers\models\conditions\AssetVolume;
use Ryssbowh\CraftTriggers\models\conditions\CategoryGroup;
use Ryssbowh\CraftTriggers\models\conditions\CategoryStatus;
use Ryssbowh\CraftTriggers\models\conditions\Draft;
use Ryssbowh\CraftTriggers\models\conditions\ElementSite;
use Ryssbowh\CraftTriggers\models\conditions\EntrySection;
use Ryssbowh\CraftTriggers\models\conditions\EntryStatus;
use Ryssbowh\CraftTriggers\models\conditions\Environment;
use Ryssbowh\CraftTriggers\models\conditions\Group;
use Ryssbowh\CraftTriggers\models\conditions\IsNew;
use Ryssbowh\CraftTriggers\models\conditions\IsPrimary;
use Ryssbowh\CraftTriggers\models\conditions\RelatedToAsset;
use Ryssbowh\CraftTriggers\models\conditions\RelatedToCategory;
use Ryssbowh\CraftTriggers\models\conditions\RelatedToEntry;
use Ryssbowh\CraftTriggers\models\conditions\RelatedToProduct;
use Ryssbowh\CraftTriggers\models\conditions\RelatedToUser;
use Ryssbowh\CraftTriggers\models\conditions\Request;
use Ryssbowh\CraftTriggers\models\conditions\Revision;
use Ryssbowh\CraftTriggers\models\conditions\Site;
use Ryssbowh\CraftTriggers\models\conditions\Slug;
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
            new Group(),
            new ElementSite(),
            new Environment(),
            new EntrySection(),
            new EntryStatus(),
            new Draft(),
            new Revision(),
            new Slug(),
            new AssetVolume(),
            new AssetKind(),
            new IsNew(),
            new Site(),
            new RelatedToEntry(),
            new RelatedToCategory(),
            new RelatedToAsset(),
            new RelatedToUser(),
            new Request(),
            new UserStatus(),
            new UserGroup(),
            new CategoryStatus(),
            new CategoryGroup(),
            new IsPrimary(),
        ]);
        if (\Craft::$app->plugins->isPluginInstalled('commerce')) {
            $this->addMany([
                new RelatedToProduct()
            ]);
        }
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
