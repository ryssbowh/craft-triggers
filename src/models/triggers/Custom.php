<?php

namespace Ryssbowh\CraftTriggers\models\triggers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use yii\base\Event;

class Custom extends Trigger
{
    /**
     * @var string
     */
    public $senderClass;

    /**
     * @var string
     */
    public $eventName;

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['senderClass', 'eventName'], 'required'],
            ['senderClass', function () {
                if (!class_exists($this->senderClass)) {
                    $this->addError('senderClass', \Craft::t('triggers', 'Class {class} does not exist', ['class' => $this->senderClass]));
                }
            }]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'Custom');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'custom';
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
    public function configTemplate(): ?string
    {
        return 'triggers/triggers/custom';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on($this->senderClass, $this->eventName, function (Event $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'event' => $e
            ]);
        });
    }
}