<?php 

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use yii\base\Event;

class TriggerTriggeredEvent extends Event
{
    /**
     * @var TriggerInterface
     */
    public $trigger;

    /**
     * @var boolean
     */
    public $result;

    /**
     * @var array
     */
    public $triggerData;

    /**
     * @var boolean
     */
    public $handled = false;
}