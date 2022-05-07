<?php

namespace Ryssbowh\CraftTriggers\interfaces;

interface ConditionInterface
{
    /**
     * Name getter
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Description getter
     * 
     * @return atring
     */
    public function getDescription(): string;

    /**
     * Handle getter
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Does this condition have configuration
     * 
     * @return bool
     */
    public function hasConfig(): bool;

    /**
     * Get configuration template
     * 
     * @return string
     */
    public function configTemplate(): string;

    /**
     * To which triggers this condition applies
     * 
     * @return ?array
     */
    public function forTriggers(): ?array;

    /**
     * Trigger getter
     * 
     * @return ?TriggerInterface
     */
    public function getTrigger(): ?TriggerInterface;

    /**
     * Trigger setter
     * 
     * @param TriggerInterface $trigger
     */
    public function setTrigger(TriggerInterface $trigger);

    /**
     * Data setter
     * 
     * @param array|string|null $data
     */
    public function setData($data);

    /**
     * Populate condition from array of data
     * 
     * @param array $data
     */
    public function populateFromData(array $data);

    /**
     * Get project config
     * 
     * @return array
     */
    public function getConfig(): array;

    /**
     * Check if this condition applies
     * 
     * @param  TriggerInterface $trigger
     * @param  array            $data
     * @return bool
     */
    public function check(TriggerInterface $trigger, array $data): bool;
}