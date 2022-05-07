<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\ModelEvent;
use yii\base\Event;

class AssetSaved extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'When an asset is saved');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'asset-saved';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Asset::class, Element::EVENT_AFTER_SAVE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'asset' => $e->sender,
                'isNew' => $e->isNew,
                'event' => $e
            ]);
        });
    }
}