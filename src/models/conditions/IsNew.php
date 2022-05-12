<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class IsNew extends Condition
{
    /**
     * @var bool
     */
    public $isNew;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['isNew', 'boolean'],
            ['isNew', 'filter', 'filter' => 'boolval']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Is new');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->isNew ? \Craft::t('triggers', 'is new') : \Craft::t('triggers', 'is not new');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'is-new';
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
        return 'triggers/conditions/is-new';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return ($this->isNew === $data['isNew']);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['entry-saved', 'asset-saved', 'category-saved', 'user-saved', 'order-saved', 'product-saved'];
    }
}