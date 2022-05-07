<?php 

namespace Ryssbowh\CraftTriggers\events;

use Ryssbowh\CraftTriggers\models\Trigger;
use Ryssbowh\CraftTriggers\records\Trigger as TriggerRecord;
use yii\base\Event;

class TriggerEvent extends Event
{
    /**
     * @var Trigger|TriggerRecord
     */
    public $trigger;

    /**
     * @var bool
     */
    public $isNew;
}