<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class ElementSite extends Condition
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
        return \Craft::t('triggers', 'Element Site');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->sites) {
            return \Craft::t('triggers', 'element sites not defined');
        }
        $sites = array_map(function ($site) {
            return \Craft::$app->sites->getSiteByUid($site)->name;
        }, $this->sites);
        return \Craft::t('triggers', 'element site is one of : {sites}', ['sites' => implode(', ', $sites)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'element-site';
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
        if ($data['order'] ?? null) {
            return in_array($data['order']->orderSite->uid, $this->sites);
        }
        $element = $data['entry'] ?? $data['category'] ?? $data['product'] ?? null;
        if (!$element) {
            return false;
        }
        return in_array($element->site->uid, $this->sites);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['category-saved', 'entry-saved', 'product-saved', 'order-saved', 'category-deleted', 'entry-deleted', 'product-deleted', 'order-deleted', 'order-authorized', 'order-completed', 'order-paid'];
    }
}
