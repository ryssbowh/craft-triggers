<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

/**
 * @since 3.0.0
 */
class IsPrimary extends Condition
{
    /**
     * @var bool
     */
    public $isPrimary = true;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['isPrimary', 'boolean'],
            ['isPrimary', 'filter', 'filter' => 'boolval']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Is primary');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->isPrimary ? \Craft::t('triggers', 'is primary') : \Craft::t('triggers', 'is not primary');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'is-primary';
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
        return 'triggers/conditions/is-primary';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        $isPrimary = is_null($data['entry']->primaryOwnerId);
        return ($this->isPrimary ? $isPrimary : !$isPrimary);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['entry-saved', 'entry-deleted'];
    }
}
