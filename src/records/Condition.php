<?php 

namespace Ryssbowh\CraftTriggers\records;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\interfaces\ConditionInterface;
use craft\db\ActiveRecord;

class Condition extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%triggers_conditions}}';
    }

    /**
     * Turn record to model
     * 
     * @return ConditionInterface
     */
    public function toModel(): ConditionInterface
    {
        $class = Triggers::$plugin->triggers->getConditionClass($this->handle);
        return new $class($this->getAttributes());
    }
}