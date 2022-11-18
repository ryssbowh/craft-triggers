<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class UserGroup extends Condition
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
        return \Craft::t('triggers', 'User group');
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
            return \Craft::$app->userGroups->getGroupByUid($group)->name;
        }, $this->groups);
        return \Craft::t('triggers', 'is in of the groups : {groups}', ['groups' => implode(', ', $groups)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'user-group';
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
        return 'triggers/conditions/user-group';
    }

    /**
     * Get all user groups
     * 
     * @return array
     */
    public function getAllGroups(): array
    {
        $groups = [];
        foreach (\Craft::$app->userGroups->getAllGroups() as $group) {
            $groups[$group->uid] = $group->name;
        }
        return $groups;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        foreach ($data['user']->groups as $group) {
            if (in_array($group->uid, $this->groups)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['user-saved', 'user-deleted', 'user-email-verified', 'user-activated', 'user-locked', 'user-unlocked', 'user-suspended', 'user-unsuspended', 'user-assigned-groups', 'user-logs-in'];
    }
}