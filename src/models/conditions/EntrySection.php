<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use yii\base\Event;

class EntrySection extends Condition
{
    /**
     * @var array
     */
    public $sections;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['sections', function () {
                if (!is_array($this->sections) or sizeof($this->sections) == 0) {
                    $this->addError('sections', \Craft::t('triggers', '{attr} is required', ['attr' => 'Sections']));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Section');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->sections) {
            return \Craft::t('triggers', 'sections not defined');
        }
        $sections = array_map(function ($section) {
            return \Craft::$app->sections->getSectionByUid($section)->name;
        }, $this->sections);
        return \Craft::t('triggers', 'section is one of : {sections}', ['sections' => implode(', ', $sections)]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'entry-section';
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
        return 'triggers/conditions/section';
    }

    /**
     * Get all sections
     * 
     * @return array
     */
    public function getAllSections(): array
    {
        $sections = [];
        foreach (\Craft::$app->sections->getAllSections() as $section) {
            $sections[$section->uid] = $section->name;
        }
        return $sections;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return in_array($data['entry']->section->uid, $this->sections);
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['entry-saved', 'entry-deleted'];
    }
}