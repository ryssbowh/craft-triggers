<?php 

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\exceptions\ActionException;
use Ryssbowh\CraftTriggers\interfaces\ActionInterface;
use yii\base\Event;

class RegisterActionsEvent extends Event
{
    /**
     * @var array
     */
    public $_actions = [];

    /**
     * Actions getter
     * 
     * @return array
     */
    public function getActions(): array
    {
        return $this->_actions;
    }

    /**
     * Add an action
     * 
     * @param ActionInterface $action
     * @param bool            $replaceIfExists
     */
    public function add(ActionInterface $action, bool $replaceIfExists = false)
    {
        $handle = $action->getHandle();
        if (isset($this->_actions[$handle]) and !$replaceIfExists) {
            throw ActionException::handleDefined($handle);
        }
        $this->_actions[$handle] = $action;
    }

    /**
     * Add many actions
     * 
     * @param array $actions
     * @param bool  $replaceIfExists
     */
    public function addMany(array $actions, bool $replaceIfExists = false)
    {
        foreach ($actions as $action) {
            $this->add($action, $replaceIfExists);
        }
    }
}