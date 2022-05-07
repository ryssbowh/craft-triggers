<?php 

namespace Ryssbowh\CraftTriggers\models;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\exceptions\ActionException;
use Ryssbowh\CraftTriggers\exceptions\ConditionException;
use Ryssbowh\CraftTriggers\exceptions\TriggerException;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use craft\base\Model;
use yii\base\Event;

class Trigger extends Model implements TriggerInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $active = true;

    /**
     * @var integer
     */
    public $triggered = 0;

    /**
     * @var \DateTime
     */
    public $dateCreated;

    /**
     * @var \DateTime
     */
    public $dateUpdated;

    /**
     * @var array
     */
    protected $_conditions;

    /**
     * @var array
     */
    protected $_actions;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['id', 'uid', 'dateCreated', 'dateUpdated'], 'safe'],
            [['conditions', 'actions'], 'safe'],
            [['handle', 'name'], 'required'],
            [['handle', 'name'], 'string'],
            ['active', 'boolean'],
            ['active', 'filter', 'filter' => 'boolval']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'handle' => \Craft::t('triggers', 'Type')
        ];
    }

    /**
     * @inheritDoc
     */
    public function hasConfig(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function configTemplate(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getInstructions(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function setData($data)
    {
        if ($data === null) {
            $data = [];
        } elseif (is_string($data)) {
            $data = json_decode($data, true);
        }
        $data = array_intersect_key($data, $this->attributes);
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public function getConditions(): array
    {
        if ($this->_conditions === null and $this->id) {
            $this->_conditions = Triggers::$plugin->triggers->getConditionsForTrigger($this->id);
        }
        return $this->_conditions ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getActiveConditions(): array
    {
        return array_filter($this->conditions, function ($condition) {
            return $condition->active;
        });
    }

    /**
     * @inheritDoc
     */
    public function setConditions(array $conditions)
    {
        $this->_conditions = $conditions;
    }

    /**
     * @inheritDoc
     */
    public function getActions(): array
    {
        if ($this->_actions === null and $this->id) {
            $this->_actions = Triggers::$plugin->triggers->getActionsForTrigger($this->id);
        }
        return $this->_actions ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getActiveActions(): array
    {
        return array_filter($this->actions, function ($action) {
            return $action->active;
        });
    }

    /**
     * @inheritDoc
     */
    public function setActions(array $actions)
    {
        $this->_actions = $actions;
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $conditions = $actions = [];
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
        foreach ($data['actions'] ?? [] as $actionData) {
            if ($actionData['id'] ?? null) {
                $action = Triggers::$plugin->triggers->getActionById($actionData['id']);
            } elseif ($actionData['handle'] ?? null) {
                $action = Triggers::$plugin->triggers->getRegisteredAction($actionData['handle']);
            } else {
                throw ActionException::handleMissing();
            }
            $action->populateFromData($actionData);
            $actions[] = $action;
        }
        uasort($conditions, function ($c1, $c2) {
            return $c1->order > $c2->order ? 1 : -1;
        });
        uasort($actions, function ($c1, $c2) {
            return $c1->order > $c2->order ? 1 : -1;
        });
        $data['conditions'] = $conditions;
        $data['actions'] = $actions;
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);    
        }
        foreach ($this->conditions as $condition) {
            if ($condition->hasErrors()) {
                return true;
            }
        }
        foreach ($this->actions as $action) {
            if ($action->hasErrors()) {
                return true;
            }
        }
        return parent::hasErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->conditions as $condition) {
            $condition->validate();
        }
        foreach ($this->actions as $action) {
            $action->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $data = array_diff_key($this->attributes, array_flip($this->baseAttributes()));
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'active' => $this->active,
            'data' => $data,
            'conditions' => array_map(function ($condition) {
                return $condition->getConfig();
            }, $this->conditions),
            'actions' => array_map(function ($action) {
                return $action->getConfig();
            }, $this->actions)
        ];
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {}

    /**
     * @inheritDoc
     */
    public function checkConditions(array $data): bool
    {
        $res = true;
        foreach ($this->activeConditions as $condition) {
            $conditionResult = $condition->check($this, $data);
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
    public function applyActions(array $data)
    {
        foreach ($this->activeActions as $action) {
            $action->apply($this, $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return $this->attributes();
    }

    /**
     * Get all base attributes, all attributes not in this array will be considered data
     * 
     * @return array
     */
    protected function baseAttributes(): array
    {
        return ['name', 'handle', 'active', 'id', 'uid', 'dateCreated', 'dateUpdated', 'triggered'];
    }
}
