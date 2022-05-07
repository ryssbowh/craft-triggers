<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class AssetVolume extends Condition
{
    /**
     * @var array
     */
    public $volumes;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['volumes', function () {
                if (!is_array($this->volumes) or sizeof($this->volumes) == 0) {
                    $this->addError('volumes', \Craft::t('triggers', '{attr} is required', ['attr' => 'Volumes']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Volume');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->volumes) {
            return \Craft::t('triggers', 'volumes not defined');
        }
        $volumes = array_map(function ($volume) {
            return \Craft::$app->volumes->getVolumeByUid($volume)->name;
        }, $this->volumes);
        return \Craft::t('triggers', 'volume is one of : {volumes}', ['volumes' => implode(', ', $volumes)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'asset-volume';
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
        return 'triggers/conditions/volume';
    }

    /**
     * Get all volumes
     * 
     * @return array
     */
    public function getAllVolumes(): array
    {
        $volumes = [];
        foreach (\Craft::$app->volumes->getAllVolumes() as $volume) {
            $volumes[$volume->uid] = $volume->name;
        }
        return $volumes;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return in_array($data['asset']->volume->uid, $this->volumes);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['asset-saved', 'asset-deleted'];
    }
}