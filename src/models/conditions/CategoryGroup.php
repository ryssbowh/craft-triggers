<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class CategoryGroup extends Condition
{
    /**
     * @var array
     */
    public $groups;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['groups', function () {
                if (!is_array($this->groups) or sizeof($this->groups) == 0) {
                    $this->addError('groups', \Craft::t('triggers', '{attr} is required', ['attr' => 'Groups']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Category group');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->groups) {
            return \Craft::t('triggers', 'groups not defined');
        }
        $groups = array_map(function ($group) {
            return \Craft::$app->categories->getGroupByUid($group)->name;
        }, $this->groups);
        return \Craft::t('triggers', 'group is one of : {groups}', ['groups' => implode(', ', $groups)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'category-group';
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
        return 'triggers/conditions/category-group';
    }

    /**
     * Get all volumes
     * 
     * @return array
     */
    public function getAllGroups(): array
    {
        $groups = [];
        foreach (\Craft::$app->categories->getAllGroups() as $group) {
            $groups[$group->uid] = $group->name;
        }
        return $groups;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return in_array($data['category']->group->uid, $this->groups);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['category-saved', 'category-deleted'];
    }
}