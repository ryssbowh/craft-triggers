<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class Site extends Condition
{
    /**
     * @var array
     */
    public $sites;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['sites', function () {
                if (!is_array($this->sites) or sizeof($this->sites) == 0) {
                    $this->addError('sites', \Craft::t('triggers', '{attr} is required', ['attr' => 'Sites']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Site');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->sites) {
            return \Craft::t('triggers', 'sites not defined');
        }
        $sites = array_map(function ($site) {
            return \Craft::$app->sites->getSiteByUid($site)->name;
        }, $this->sites);
        return \Craft::t('triggers', 'site is one of : {sites}', ['sites' => implode(', ', $sites)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'site';
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
        return 'triggers/conditions/sites';
    }

    /**
     * Get all sites
     * 
     * @return array
     */
    public function getAllSites(): array
    {
        $sites = [];
        foreach (\Craft::$app->sites->getAllSites() as $site) {
            $sites[$site->uid] = $site->name;
        }
        return $sites;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        if (!\Craft::$app->sites->currentSite) {
            return false;
        }
        return in_array(\Craft::$app->sites->currentSite->uid, $this->sites);
    }
}