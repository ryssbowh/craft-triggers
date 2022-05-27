<?php

namespace Ryssbowh\CraftTriggers\models\actions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Action;

class Log extends Action
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Log a message');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'log';
    }

    /**
     * @inheritDoc
     */
    public function hasConfig(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function configTemplate(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Log a message';
    }

    /**
     * @inheritDoc
     */
    public function apply(TriggerInterface $trigger, array $data)
    {
        \Craft::info("The trigger '" . $trigger->type . "' has triggered the log action", __METHOD__);
    }
}