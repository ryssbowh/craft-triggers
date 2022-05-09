<?php

namespace Plugins\MyPlugin\models;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;

class MyCondition extends Condition
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
        return 'My Condition';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'this is a new condition'
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'my-condition';
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
    public function configTemplate(): string
    {
        return 'my-plugin/my-condition';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        if ($data['my-condition-is-met']) {
            return true;
        }
        return false;
    }

    /**
     * Return null for a global condition
     */
    protected function defineForTriggers(): ?array
    {
        return ['my-trigger'];
    }
}