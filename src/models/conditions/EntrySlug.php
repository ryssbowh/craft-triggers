<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\elements\Entry;
use craft\helpers\ElementHelper;
use yii\base\Event;

class EntrySlug extends Condition
{
    /**
     * @var string
     */
    public $slug;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['slug', 'required']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Slug');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->slug ? \Craft::t('triggers', 'slug equals "{slug}"', ['slug' => $this->slug]) : \Craft::t('triggers', 'slug not defined');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry-slug';
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
        return 'triggers/conditions/slug';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return ($data['entry']->slug === $this->slug);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['entry-saved', 'entry-deleted'];
    }
}