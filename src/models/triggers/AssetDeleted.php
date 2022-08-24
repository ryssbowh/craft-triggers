<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\elements\Asset;
use craft\events\ElementEvent;
use craft\services\Elements;
use yii\base\Event;

class AssetDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an asset is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'asset-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Elements::class, Elements::EVENT_AFTER_DELETE_ELEMENT, function (ElementEvent $e) use ($_this) {
            if (!$e->element instanceof Asset) {
                return;
            }
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'asset' => $e->element,
                'event' => $e
            ]);
        });
    }
}