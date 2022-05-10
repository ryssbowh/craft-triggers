<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\exceptions\ConditionException;
use Ryssbowh\CraftTriggers\exceptions\TriggerException;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\helpers\App;
use craft\helpers\StringHelper;
use yii\base\Event;

class Group extends Condition
{
    /**
     * @var array
     */
    protected $_conditions;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Group');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'group';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::$app->view->renderTemplate('triggers/conditions/group', [
            'condition' => $this
        ]);
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['conditions', 'safe']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->conditions as $condition) {
            $condition->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null): bool
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);    
        }
        foreach ($this->conditions as $condition) {
            if ($condition->hasErrors()) {
                return true;
            }
        }
        return parent::hasErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $conditions = [];
        foreach ($data['conditions'] ?? [] as $conditionData) {
            if ($conditionData['id'] ?? null) {
                $condition = Triggers::$plugin->triggers->getConditionById($conditionData['id']);
            } elseif ($conditionData['handle'] ?? null) {
                $condition = Triggers::$plugin->triggers->getRegisteredCondition($conditionData['handle']);
            } else {
                throw ConditionException::handleMissing();
            }
            $condition->populateFromData($conditionData);
            $conditions[] = $condition;
        }
        uasort($conditions, function ($c1, $c2) {
            return $c1->order > $c2->order ? 1 : -1;
        });
        $data['conditions'] = $conditions;
        $this->setAttributes($data);
    }

    /**
     * Get all conditions
     * 
     * @return array
     */
    public function getConditions(): array
    {
        if ($this->_conditions === null and $this->id) {
            $this->_conditions = Triggers::$plugin->triggers->getConditionsForGroup($this->id);
        }
        return $this->_conditions ?? [];
    }

    /**
     * Get all active conditions
     * 
     * @return array
     */
    public function getActiveConditions(): array
    {
        return array_filter($this->conditions, function ($condition) {
            return $condition->active;
        });
    }

    /**
     * Set conditions
     * 
     * @param array $conditions
     */
    public function setConditions(array $conditions)
    {
        $this->_conditions = $conditions;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        $res = true;
        foreach ($this->activeConditions as $condition) {
            $conditionResult = $condition->check($this, $e);
            if ($condition->operator === 'and') {
                $res = ($res and $conditionResult);
            } else {
                $res = ($res or $conditionResult);
            }
        }
        return $res;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $data = array_diff_key($this->attributes, array_flip($this->baseAttributes));
        return [
            'uid' => $this->uid ?? StringHelper::UUID(),
            'handle' => $this->handle,
            'active' => $this->active,
            'order' => $this->order,
            'operator' => $this->operator,
            'data' => $data,
            'conditions' => array_map(function ($condition) {
                return $condition->getConfig();
            }, $this->conditions)
        ];
    }
}