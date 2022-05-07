<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;

class Request extends Condition
{
    /**
     * @var array
     */
    public $types;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['types', function () {
                if (!is_array($this->types) or sizeof($this->types) == 0) {
                    $this->addError('sites', \Craft::t('triggers', '{attr} is required', ['attr' => 'Types of request']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Request');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->types) {
            return \Craft::t('triggers', 'type of request not defined');
        }
        $_this = $this;
        $types = array_map(function ($type) use ($_this) {
            return $_this->allTypes[$type];
        }, $this->types);
        return \Craft::t('triggers', 'type of request is one of : {requests}', ['requests' => implode(', ', $types)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'request';
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
        return 'triggers/conditions/request';
    }

    /**
     * Get all requests types
     * 
     * @return array
     */
    public function getAllTypes(): array
    {
        return [
            'console' => \Craft::t('triggers', 'Console'),
            'site' => \Craft::t('triggers', 'Site'),
            'cp' => \Craft::t('triggers', 'Control panel')
        ];
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        if (in_array('cp', $this->types) and \Craft::$app->request->isCpRequest) {
            return true;
        }
        if (in_array('site', $this->types) and \Craft::$app->request->isSiteRequest) {
            return true;
        }
        if (in_array('console', $this->types) and \Craft::$app->request->isConsoleRequest) {
            return true;
        }
        return false;
    }
}