<?php 

namespace Ryssbowh\CraftTriggers\models;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\events\DefineConditionTriggers;
use Ryssbowh\CraftTriggers\interfaces\ConditionInterface;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use craft\base\Model;
use craft\helpers\StringHelper;

abstract class Condition extends Model implements ConditionInterface
{
    const EVENT_DEFINE_FOR_TRIGGERS = 'event-define-for-triggers';
    
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
     * @var int
     */
    public $order;

    /**
     * @var string
     */
    public $operator = 'and';

    /**
     * @var boolean
     */
    public $active = true;

    /**
     * @var int
     */
    public $group_id;

    /**
     * @var int
     */
    public $trigger_id;

    /**
     * @var \DateTime
     */
    public $dateCreated;

    /**
     * @var \DateTime
     */
    public $dateUpdated;

    /**
     * @var TriggerInterface
     */
    protected $_trigger;

    /**
     * @var boolean
     */
    protected $_forTriggers = false;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['id', 'uid', 'dateCreated', 'dateUpdated'], 'safe'],
            [['order', 'group_id', 'trigger_id'], 'integer'],
            [['handle'], 'required'],
            [['handle'], 'string'],
            ['active', 'boolean'],
            ['order', 'integer'],
            ['active', 'filter', 'filter' => 'boolval'],
            ['order', 'filter', 'filter' => 'intval'],
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
    public function configTemplate(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTrigger(): ?TriggerInterface
    {
        if ($this->_trigger === null and $this->trigger_id) {
            $this->_trigger = Triggers::$plugin->triggers->getTriggerById($this->trigger_id);
        }
        return $this->_trigger;
    }

    /**
     * @inheritDoc
     */
    public function setTrigger(TriggerInterface $trigger)
    {
        $this->_trigger = $trigger;
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
    public function forTriggers(): ?array
    {
        if ($this->_forTriggers === false) {
            $event = new DefineConditionTriggers([
                'triggers' => $this->defineForTriggers()
            ]);
            $this->trigger(self::EVENT_DEFINE_FOR_TRIGGERS, $event);
            $this->_forTriggers = $event->triggers;
        }
        return $this->_forTriggers;
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $data = array_diff_key($this->attributes, array_flip($this->baseAttributes()));
        return [
            'uid' => $this->uid ?? StringHelper::UUID(),
            'handle' => $this->handle,
            'active' => $this->active,
            'order' => $this->order,
            'operator' => $this->operator,
            'data' => $data
        ];
    }

    /**
     * Get all base attributes, all attributes not in this array will be considered data
     * 
     * @return array
     */
    protected function baseAttributes(): array
    {
        return ['group_id', 'trigger_id', 'operator', 'order', 'handle', 'active', 'id', 'uid', 'dateCreated', 'dateUpdated'];
    }

    /**
     * Triggers for which this condition applies. null means all
     * 
     * @return ?array
     */
    protected function defineForTriggers(): ?array
    {
        return null;
    }
}
