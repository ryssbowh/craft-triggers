<?php

namespace Plugins\MyPlugin\models;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\ModelEvent;
use yii\base\Event;

class MyTrigger extends Trigger
{
    /**
     * @var string
     */
    public $myVariable;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['myVariable', 'required']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('my-plugin', 'My custom trigger');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'my-trigger';
    }

    /**
     * @inheritDoc
     */
    public function hasConfig(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function configTemplate(): ?string
    {
        return 'my-plugin/my-trigger';
    }

    /**
     * @inheritDoc
     */
    public function getInstructions(): string
    {
        return 'I\'m the instructions, I will be displayed on the select trigger dropdown';
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return 'I\'m the tip, I will be displayed on the select trigger dropdown';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Asset::class, Element::EVENT_BEFORE_DELETE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'asset' => $e->sender,
                'my-condition-is-met' => true
            ]);
        });
    }
}