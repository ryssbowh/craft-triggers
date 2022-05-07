<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\elements\Entry;
use craft\helpers\ElementHelper;
use yii\base\Event;

class EntryDraft extends Condition
{
    /**
     * @var bool
     */
    public $isDraft;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['isDraft', 'boolean'],
            ['isDraft', 'filter', 'filter' => 'boolval']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Draft');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->isDraft ? \Craft::t('triggers', 'is a draft') : \Craft::t('triggers', 'is not a draft');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry-draft';
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
        return 'triggers/conditions/draft';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        $draft = ElementHelper::isDraft($data['entry']);
        return ($this->isDraft === $draft);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['entry-saved', 'entry-deleted'];
    }
}