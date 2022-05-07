<?php 

namespace Ryssbowh\CraftTriggers\records;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use craft\db\ActiveRecord;

class Trigger extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%triggers_triggers}}';
    }

    /**
     * Turn record to model
     * 
     * @return TriggerInterface
     */
    public function toModel(): TriggerInterface
    {
        $class = Triggers::$plugin->triggers->getTriggerClass($this->handle);
        return new $class($this->getAttributes());
    }
}