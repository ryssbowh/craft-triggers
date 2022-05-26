<?php

namespace Plugins\MyPlugin;

use Plugins\MyPlugin\models\MyAction;
use Plugins\MyPlugin\models\MyCondition;
use Plugins\MyPlugin\models\MyTrigger;
use Ryssbowh\CraftTriggers\events\RegisterActionsEvent;
use Ryssbowh\CraftTriggers\events\RegisterConditionsEvent;
use Ryssbowh\CraftTriggers\events\RegisterTriggersEvent;
use Ryssbowh\CraftTriggers\services\TriggersService;

class MyPlugin extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();

        Event::on(TriggersService::class, TriggersService::EVENT_REGISTER_ACTIONS, function (RegisterActionsEvent $e) {
            $e->add(new MyAction);
        });

        Event::on(TriggersService::class, TriggersService::EVENT_REGISTER_TRIGGERS, function (RegisterTriggersEvent $e) {
            $e->add(new MyTrigger);
        });

        Event::on(TriggersService::class, TriggersService::EVENT_REGISTER_CONDITIONS, function (RegisterConditionsEvent $e) {
            $e->add(new MyCondition);
        });
    }
}

?>