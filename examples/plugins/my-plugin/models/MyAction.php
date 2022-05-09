<?php

namespace Plugins\MyPlugin\models;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Action;

class MyAction extends Action
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
    public function getName(): string
    {
        return 'My Action';
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'my-action';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'This is an action that does nothing';
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
        return 'my-plugin/my-action';
    }

    /**
     * @inheritDoc
     */
    public function apply(TriggerInterface $trigger, array $data)
    {
        //Whatever you need to do here
    }
}