<?php

namespace Ryssbowh\CraftTriggers\services;

use Ryssbowh\CraftTriggers\events\RegisterActionsEvent;
use Ryssbowh\CraftTriggers\events\RegisterConditionsEvent;
use Ryssbowh\CraftTriggers\events\RegisterTriggersEvent;
use Ryssbowh\CraftTriggers\events\TriggerEvent;
use Ryssbowh\CraftTriggers\events\TriggerTriggeredEvent;
use Ryssbowh\CraftTriggers\exceptions\ActionException;
use Ryssbowh\CraftTriggers\exceptions\ConditionException;
use Ryssbowh\CraftTriggers\exceptions\TriggerException;
use Ryssbowh\CraftTriggers\interfaces\ActionInterface;
use Ryssbowh\CraftTriggers\interfaces\ConditionInterface;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Trigger;
use Ryssbowh\CraftTriggers\records\Action as ActionRecord;
use Ryssbowh\CraftTriggers\records\Condition as ConditionRecord;
use Ryssbowh\CraftTriggers\records\Trigger as TriggerRecord;
use craft\base\Component;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;
use yii\base\Event;

class TriggersService extends Component
{
    const EVENT_REGISTER_TRIGGERS = 'register-triggers';
    const EVENT_REGISTER_CONDITIONS = 'register-conditions';
    const EVENT_REGISTER_ACTIONS = 'register-actions';
    const CONFIG_KEY = 'triggers';
    const EVENT_BEFORE_SAVE = 'before-save';
    const EVENT_AFTER_SAVE = 'after-save';
    const EVENT_BEFORE_APPLY_DELETE = 'before-apply-delete';
    const EVENT_AFTER_DELETE = 'after-delete';
    const EVENT_BEFORE_DELETE = 'before-delete';
    const EVENT_BEFORE_CHECKING_CONDITIONS = 'before-checking-conditions';
    const EVENT_AFTER_CHECKING_CONDITIONS = 'after-checking-conditions';
    const EVENT_BEFORE_APPLYING_ACTIONS = 'before-applying-actions';
    const EVENT_AFTER_APPLYING_ACTIONS = 'after-applying-actions';

    /**
     * @var array
     */
    protected $_triggers;

    /**
     * @var array
     */
    protected $_conditions;

    /**
     * @var array
     */
    protected $_actions;

    /**
     * @var array
     */
    protected $_registeredTriggers;

    /**
     * @var array
     */
    protected $_registeredConditions;

    /**
     * @var array
     */
    protected $_registeredActions;

    /**
     * Get all triggers
     * 
     * @return array
     */
    public function getAllTriggers(): array
    {
        if ($this->_triggers === null) {
            $this->_triggers = [];
            foreach (TriggerRecord::find()->orderBy('name asc')->all() as $record) {
                try {
                    $this->_triggers[] = $record->toModel();
                } catch (\Throwable $e) {
                    \Craft::$app->errorHandler->logException($e);
                }
            }
        }
        return $this->_triggers;
    }
    
    /**
     * Get active triggers
     * 
     * @return array
     */
    public function getActiveTriggers(): array
    {
        return array_filter($this->allTriggers, function ($trigger) {
            return $trigger->active;
        });
    }

    /**
     * Get all conditions
     * 
     * @return array
     */
    public function getAllConditions(): array
    {
        if ($this->_conditions === null) {
            $this->_conditions = [];
            foreach (ConditionRecord::find()->orderBy('order asc')->all() as $record) {
                try {
                    $this->_conditions[] = $record->toModel();
                } catch (\Throwable $e) {
                    \Craft::$app->errorHandler->logException($e);
                }
            }
        }
        return $this->_conditions;
    }

    /**
     * Get all actions
     * 
     * @return array
     */
    public function getAllActions(): array
    {
        if ($this->_actions === null) {
            $this->_actions = [];
            foreach (ActionRecord::find()->orderBy('order asc')->all() as $record) {
                try {
                    $this->_actions[] = $record->toModel();
                } catch (\Throwable $e) {
                    \Craft::$app->errorHandler->logException($e);
                }
            }
        }
        return $this->_actions;
    }

    /**
     * Get trigger by id
     * 
     * @param  int $id
     * @return TriggerInterface
     */
    public function getTriggerById(int $id): TriggerInterface
    {
        foreach ($this->allTriggers as $trigger) {
            if ($id == $trigger->id) {
                return $trigger;
            }
        }
        throw TriggerException::noId($id);
    }

    /**
     * Get registered triggers
     * 
     * @return array
     */
    public function getRegisteredTriggers(): array
    {
        if ($this->_registeredTriggers === null) {
            $this->registerTriggers();
        }
        return $this->_registeredTriggers;
    }

    /**
     * Is a trigger handle registered
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function isTriggerRegistered(string $handle): bool
    {
        return isset($this->registeredTriggers[$handle]);
    }

    /**
     * Get a registered trigger by handle
     * 
     * @param  string $handle
     * @return TriggerInterface
     */
    public function getRegisteredTrigger(string $handle): TriggerInterface
    {
        if ($this->isTriggerRegistered($handle)) {
            return clone $this->registeredTriggers[$handle];
        }
        throw TriggerException::noHandle($handle);
    }

    /**
     * Get the class for a trigger handle
     * 
     * @param  string $handle
     * @return string
     */
    public function getTriggerClass(string $handle): string
    {
        $trigger = $this->getRegisteredTrigger($handle);
        return get_class($trigger);
    }

    /**
     * Callback when a trigger is triggered
     * 
     * @param  TriggerInterface $trigger
     * @param  array            $data
     */
    public function onTriggerTriggered(TriggerInterface $trigger, array $data = [])
    {
        $event = new TriggerTriggeredEvent([
            'trigger' => $trigger,
            'triggerData' => $data
        ]);
        $this->trigger(self::EVENT_BEFORE_CHECKING_CONDITIONS, $event);
        if ($event->handled) {
            return;
        }
        $data = $event->triggerData;
        $result = $trigger->checkConditions($data);
        $event = new TriggerTriggeredEvent([
            'trigger' => $trigger,
            'result' => $result,
            'triggerData' => $data
        ]);
        $this->trigger(self::EVENT_AFTER_CHECKING_CONDITIONS, $event);
        $data = $event->triggerData;
        if ($event->result) {
            $event = new TriggerTriggeredEvent([
                'trigger' => $trigger,
                'triggerData' => $data
            ]);
            $this->trigger(self::EVENT_BEFORE_APPLYING_ACTIONS, $event);
            if ($event->handled) {
                return;
            }
            $trigger->applyActions($event->triggerData);
            $this->incrementTriggerCounter($trigger->id);
            $event = new TriggerTriggeredEvent([
                'trigger' => $trigger,
                'triggerData' => $event->triggerData
            ]);
            $this->trigger(self::EVENT_AFTER_APPLYING_ACTIONS, $event);
        }
    }

    /**
     * Increment the triggered counter for a trigger id
     * 
     * @param int $triggerId
     */
    public function incrementTriggerCounter(int $triggerId)
    {
        $record = TriggerRecord::findOne(['id' => $triggerId]);
        if ($record) {
            $record->triggered = $record->triggered + 1;
            $record->save(false);
        }
    }

    /**
     * Get registered conditions
     * 
     * @return array
     */
    public function getRegisteredConditions(): array
    {
        if ($this->_registeredConditions === null) {
            $this->registerConditions();
        }
        return $this->_registeredConditions;
    }

    /**
     * is a condition handle registered
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function isConditionRegistered(string $handle): bool
    {
        return isset($this->registeredConditions[$handle]);
    }

    /**
     * Get a registered condition by handle
     * 
     * @param  string $handle
     * @return ConditionInterface
     */
    public function getRegisteredCondition(string $handle): ConditionInterface
    {
        if ($this->isConditionRegistered($handle)) {
            return clone $this->registeredConditions[$handle];
        }
        throw ConditionException::noHandle($handle);
    }

    /**
     * Get the condition class for a handle
     * 
     * @param  string $handle
     * @return string
     */
    public function getConditionClass(string $handle): string
    {
        $condition = $this->getRegisteredCondition($handle);
        return get_class($condition);
    }

    /**
     * Get the conditions for a trigger
     * 
     * @param  int    $triggerId
     * @return array
     */
    public function getConditionsForTrigger(int $triggerId): array
    {
        return array_filter($this->allConditions, function ($condition) use ($triggerId) {
            return $condition->trigger_id == $triggerId and $condition->group_id == null;
        });
    }

    /**
     * Get the conditions for a group
     * 
     * @param  int $groupId
     * @return array
     */
    public function getConditionsForGroup(int $groupId): array
    {
        return array_filter($this->allConditions, function ($condition) use ($groupId) {
            return $condition->group_id == $groupId;
        });
    }

    /**
     * Get a condition by id
     * 
     * @param  int    $id
     * @return ConditionInterface
     */
    public function getConditionById(int $id): ConditionInterface
    {
        foreach ($this->allConditions as $condition) {
            if ($id == $condition->id) {
                return $condition;
            }
        }
        throw ConditionException::noId($id);
    }

    /**
     * Get registered actions
     * 
     * @return array
     */
    public function getRegisteredActions(): array
    {
        if ($this->_registeredActions === null) {
            $this->registerActions();
        }
        return $this->_registeredActions;
    }

    /**
     * Is an action handle registered
     * 
     * @param  string  $handle
     * @return boolean
     */
    public function isActionRegistered(string $handle): bool
    {
        return isset($this->registeredActions[$handle]);
    }

    /**
     * Get a registered action by handle
     * 
     * @param  string $handle
     * @return ActionInterface
     */
    public function getRegisteredAction(string $handle): ActionInterface
    {
        if ($this->isActionRegistered($handle)) {
            return clone $this->registeredActions[$handle];
        }
        throw ActionException::noHandle($handle);
    }

    /**
     * Get an action class by handle
     * 
     * @param  string $handle
     * @return string
     */
    public function getActionClass(string $handle): string
    {
        $action = $this->getRegisteredAction($handle);
        return get_class($action);
    }

    /**
     * Get the actions for a trigger
     * 
     * @param  int    $triggerId
     * @return array
     */
    public function getActionsForTrigger(int $triggerId): array
    {
        return array_filter($this->allActions, function ($action) use ($triggerId) {
            return $action->trigger_id == $triggerId;
        });
    }

    /**
     * Get an action by id
     * 
     * @param  int    $id
     * @return ActionInterface
     */
    public function getActionById(int $id): ActionInterface
    {
        foreach ($this->allActions as $action) {
            if ($id == $action->id) {
                return $action;
            }
        }
        throw ActionException::noId($id);
    }

    /**
     * Save a trigger
     * 
     * @param  Trigger $trigger
     * @param  bool    $validate
     * @return bool
     */
    public function save(Trigger $trigger, bool $validate = true): bool
    {
        if ($validate and !$trigger->validate()) {
            return false;
        }
        $isNew = !$trigger->id;
        $uid = $isNew ? StringHelper::UUID() : $trigger->uid;

        $this->trigger(self::EVENT_BEFORE_SAVE, new TriggerEvent([
            'trigger' => $trigger,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $trigger->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getTriggerRecordByUid($uid);
        $trigger->setAttributes($record->getAttributes(), false);
        
        $this->_triggers = null;

        return true;
    }

    /**
     * Delete a trigger
     * 
     * @param  Email $email
     * @param  bool  $force
     * @return bool
     */
    public function delete(Trigger $trigger): bool
    {
        $this->trigger(self::EVENT_BEFORE_DELETE, new TriggerEvent([
            'trigger' => $trigger
        ]));

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $trigger->uid);

        $this->_triggers = null;

        return true;
    }

    /**
     * Handle project config change
     * 
     * @param  ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();

        try {
            $trigger = $this->getTriggerRecordByUid($uid);
            $isNew = $trigger->getIsNewRecord();

            $trigger->uid = $uid;
            $trigger->handle = $data['handle'];
            $trigger->name = $data['name'];
            $trigger->active = $data['active'];
            $trigger->data = $data['data'] ?? [];
            $trigger->save(false);

            $toKeep = [];
            foreach ($data['conditions'] ?? [] as $conditionData) {
                $toKeep = array_merge($toKeep, $this->handleChangedCondition($trigger, $conditionData));
            }
            $toDelete = ConditionRecord::find()->where([
                'trigger_id' => $trigger->id
            ])->andWhere(['not in', 'id', $toKeep])->all();
            foreach ($toDelete as $record) {
                $record->delete();
            }

            $toKeep = [];
            foreach ($data['actions'] ?? [] as $actionData) {
                $toKeep[] = $this->handleChangedAction($trigger, $actionData);
            }
            $toDelete = ActionRecord::find()->where([
                'trigger_id' => $trigger->id
            ])->andWhere(['not in', 'id', $toKeep])->all();
            foreach ($toDelete as $record) {
                $record->delete();
            }
            
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->trigger(self::EVENT_AFTER_SAVE, new TriggerEvent([
            'trigger' => $trigger,
            'isNew' => $isNew,
        ]));
    }

    /**
     * Handle project config deletion
     * 
     * @param  ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $trigger = $this->getTriggerRecordByUid($uid);

        if (!$trigger) {
            return;
        }

        $this->trigger(self::EVENT_BEFORE_APPLY_DELETE, new TriggerEvent([
            'trigger' => $trigger
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(TriggerRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->trigger(self::EVENT_AFTER_DELETE, new TriggerEvent([
            'trigger' => $trigger
        ]));
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        foreach ($this->allTriggers as $trigger) {
            $e->config[self::CONFIG_KEY][$trigger->uid] = $trigger->getConfig();
        }
    }

    /**
     * Apply config changes to a condition
     * 
     * @param  TriggerRecord $trigger
     * @param  array         $data
     * @param  int|null      $group_id
     * @return array
     */
    protected function handleChangedCondition(TriggerRecord $trigger, array $data, ?int $group_id = null): array
    {
        $condition = $this->getConditionRecordByUid($data['uid']);
        $condition->active = $data['active'];
        $condition->order = $data['order'];
        $condition->operator = $data['operator'];
        $condition->handle = $data['handle'];
        $condition->group_id = $group_id;
        $condition->trigger_id = $trigger->id;
        $condition->data = $data['data'] ?? [];
        $condition->save(false);
        $ids = [$condition->id];
        if ($condition->handle == 'group') {
            foreach ($data['conditions'] ?? [] as $conditionData) {
                $ids = array_merge($ids, $this->handleChangedCondition($trigger, $conditionData, $condition->id));
            }
        }
        return $ids;
    }

    /**
     * Apply config changes to an action
     * 
     * @param  TriggerRecord $trigger
     * @param  array         $data
     * @return int
     */
    protected function handleChangedAction(TriggerRecord $trigger, array $data): int
    {
        $action = $this->getActionRecordByUid($data['uid']);
        $action->active = $data['active'];
        $action->order = $data['order'];
        $action->handle = $data['handle'];
        $action->trigger_id = $trigger->id;
        $action->data = $data['data'] ?? [];
        $action->save(false);
        return $action->id;
    }

    /**
     * Registers triggers
     */
    protected function registerTriggers()
    {
        $e = new RegisterTriggersEvent;
        $this->trigger(self::EVENT_REGISTER_TRIGGERS, $e);
        $this->_registeredTriggers = $e->triggers;
        uasort($this->_registeredTriggers, function ($elem1, $elem2) {
            return $elem1->type > $elem2->type ? 1 : -1;
        });
    }

    /**
     * Registers conditions
     */
    protected function registerConditions()
    {
        $e = new RegisterConditionsEvent;
        $this->trigger(self::EVENT_REGISTER_CONDITIONS, $e);
        $this->_registeredConditions = $e->conditions;
        uasort($this->_registeredConditions, function ($elem1, $elem2) {
            return $elem1->name > $elem2->name ? 1 : -1;
        });
    }

    /**
     * Registers actions
     */
    protected function registerActions()
    {
        $e = new RegisterActionsEvent;
        $this->trigger(self::EVENT_REGISTER_ACTIONS, $e);
        $this->_registeredActions = $e->actions;
        uasort($this->_registeredActions, function ($elem1, $elem2) {
            return $elem1->name > $elem2->name ? 1 : -1;
        });
    }

    /**
     * Get trigger record by uid
     * 
     * @param  string $uid
     * @return TriggerRecord
     */
    protected function getTriggerRecordByUid(string $uid): TriggerRecord
    {
        return TriggerRecord::findOne(['uid' => $uid]) ?? new TriggerRecord;
    }

    /**
     * Get action record by uid
     * 
     * @param  string $uid
     * @return ActionRecord
     */
    protected function getActionRecordByUid(string $uid): ActionRecord
    {
        return ActionRecord::findOne(['uid' => $uid]) ?? new ActionRecord;
    }

    /**
     * Get condition record by uid
     * 
     * @param  string $uid
     * @return ConditionRecord
     */
    protected function getConditionRecordByUid(string $uid): ConditionRecord
    {
        return ConditionRecord::findOne(['uid' => $uid]) ?? new ConditionRecord;
    }
}
