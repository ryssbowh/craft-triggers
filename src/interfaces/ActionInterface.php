<?php

namespace Ryssbowh\CraftTriggers\interfaces;

use yii\base\Event;

interface ActionInterface
{
    /**
     * Type getter
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Description getter
     * 
     * @return string
     */
    public function getDescription(): string;

    /**
     * Handle getter
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Does this action have configuration
     * 
     * @return bool
     */
    public function hasConfig(): bool;

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
     * @param string|array|null $data
     */
    public function setData($data);

    /**
     * Populate action from array of data
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
     * Get configuration template
     * 
     * @return ?string
     */
    public function configTemplate(): ?string;

    /**
     * Apply action
     * 
     * @param  TriggerInterface $trigger
     * @param  array            $data
     */
    public function apply(TriggerInterface $trigger, array $data);
}