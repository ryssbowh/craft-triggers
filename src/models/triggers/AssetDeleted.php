<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\ModelEvent;
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
        Event::on(Asset::class, Element::EVENT_AFTER_DELETE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'asset' => $e->sender,
                'event' => $e
            ]);
        });
    }
}