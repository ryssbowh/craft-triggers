<?php 

namespace Ryssbowh\CraftTriggers\records;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\interfaces\ActionInterface;
use craft\db\ActiveRecord;

class Action extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%triggers_actions}}';
    }

    /**
     * Turn record to model
     * 
     * @return ActionInterface
     */
    public function toModel(): ActionInterface
    {
        $class = Triggers::$plugin->triggers->getActionClass($this->handle);
        return new $class($this->getAttributes());
    }
}