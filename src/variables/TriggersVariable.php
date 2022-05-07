<?php

namespace Ryssbowh\CraftTriggers\variables;

use Ryssbowh\CraftTriggers\Triggers;

class TriggersVariable
{
    public function triggers()
    {
        return Triggers::$plugin->triggers;
    }
}