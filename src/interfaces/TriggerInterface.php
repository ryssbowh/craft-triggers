<?php

namespace Ryssbowh\CraftTriggers\interfaces;

interface TriggerInterface
{
    /**
     * Type getter
     * 
     * @return string
     */
    public function getType(): string;

    /**
     * Handle getter
     * 
     * @return string
     */
    public function getHandle(): string;

    /**
     * Does this trigger have config
     * 
     * @return bool
     */
    public function hasConfig(): bool;

    /**
     * Get configuration template
     * 
     * @return ?string
     */
    public function configTemplate(): ?string;

    /**
     * Get instructions
     * 
     * @return string
     */
    public function getInstructions(): string;

    /**
     * Get tip
     * 
     * @return string
     */
    public function getTip(): string;

    /**
     * Initialize trigger
     */
    public function initialize();

    /**
     * Data setter
     * 
     * @param array|string|null $data
     */
    public function setData($data);

    /**
     * Conditions getter
     * 
     * @return array
     */
    public function getConditions(): array;

    /**
     * Get active conditions
     * 
     * @return array
     */
    public function getActiveConditions(): array;

    /**
     * Conditions setter
     * 
     * @param array $conditions
     */
    public function setConditions(array $conditions);

    /**
     * Get actions
     * 
     * @return array
     */
    public function getActions(): array;

    /**
     * Get active actions
     * 
     * @return array
     */
    public function getActiveActions(): array;

    /**
     * Actions setter
     * 
     * @param array $actions
     */
    public function setActions(array $actions);

    /**
     * Populate trigger from array of data
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
     * Check trigger conditions
     * 
     * @param  array  $data
     * @return bool
     */
    public function checkConditions(array $data): bool;

    /**
     * Apply trigger conditions
     * 
     * @param array $data
     */
    public function applyActions(array $data);
}