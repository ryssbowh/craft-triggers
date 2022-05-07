<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\helpers\Assets;
use yii\base\Event;

class AssetKind extends Condition
{
    public $kinds;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['kinds', function () {
                if (!is_array($this->kinds) or sizeof($this->kinds) == 0) {
                    $this->addError('kinds', \Craft::t('triggers', '{attr} is required', ['attr' => 'Kinds']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Kind');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->kinds) {
            return \Craft::t('triggers', 'kinds not defined');
        }
        $definedKinds = Assets::getFileKinds();
        $kinds = array_map(function ($kind) use ($definedKinds) {
            return $definedKinds[$kind]['label'];
        }, $this->kinds);
        return \Craft::t('triggers', 'kind is one of : {kinds}', ['kinds' => implode(', ', $kinds)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'asset-kind';
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
        return 'triggers/conditions/kind';
    }

    /**
     * Get all defined kinds
     * 
     * @return array
     */
    public function getAllKinds(): array
    {
        $kinds = [];
        foreach (Assets::getFileKinds() as $handle => $kind) {
            $kinds[$handle] = $kind['label'];
        }
        return $kinds;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return in_array($data['asset']->kind, $this->kinds);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['asset-saved', 'asset-deleted'];
    }
}