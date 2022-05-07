<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\elements\Category;
use yii\base\Event;

class CategoryStatus extends Condition
{
    /**
     * @var array
     */
    public $statuses;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['statuses', function () {
                if (!is_array($this->statuses) or sizeof($this->statuses) == 0) {
                    $this->addError('statuses', \Craft::t('triggers', '{attr} is required', ['attr' => 'Statuses']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Status');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->statuses) {
            return \Craft::t('triggers', 'status not chosen');
        }
        $statuses = $this->getAllStatuses();
        $statuses = array_map(function ($status) use ($statuses) {
            return $statuses[$status];
        }, $this->statuses);
        return \Craft::t('triggers', 'status is one of : {statuses}', ['statuses' => implode(', ', $statuses)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'category-status';
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
        return 'triggers/conditions/status';
    }

    /**
     * Get all statuses
     * 
     * @return array
     */
    public function getAllStatuses(): array
    {
        return Category::statuses();
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return in_array($data['category']->status, $this->statuses);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['category-saved', 'category-deleted'];
    }
}