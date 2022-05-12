<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\elements\Entry;
use craft\helpers\ElementHelper;
use yii\base\Event;

class Revision extends Condition
{
    /**
     * @var bool
     */
    public $isRevision;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['isRevision', 'boolean'],
            ['isRevision', 'filter', 'filter' => 'boolval']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Revision');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->isRevision ? \Craft::t('triggers', 'is a revision') : \Craft::t('triggers', 'is not a revision');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'revision';
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
        return 'triggers/conditions/revision';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        $revision = ElementHelper::isRevision($data['entry']);
        return ($this->isRevision === $revision);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['entry-saved', 'entry-deleted', 'category-saved', 'category-deleted', 'order-saved', 'product-saved', 'product-deleted'];
    }
}