<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\base\Element;
use craft\db\Query;
use craft\elements\Entry;
use yii\base\Event;

class RelatedToEntry extends Condition
{
    /**
     * @var int
     */
    public $elementId;

    /**
     * @var Element
     */
    protected $_element;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['elementId', function () {
                if (is_array($this->elementId)) {
                    $this->elementId = $this->elementId[0] ?? null;
                }
                if (!$this->element) {
                    $this->addError('elementId', \Craft::t('triggers', $this->elementName . ' cannot be empty'));
                }
            }, 'skipOnEmpty' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('triggers', 'Related to ' . strtolower($this->elementName));
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if (!$this->element) {
            return \Craft::t('triggers', strtolower($this->elementName) . ' not chosen');
        }
        return \Craft::t('triggers', 'is related to ' . lcfirst($this->elementName) . ' {title}', ['title' => '<a target="_blank" href="' . $this->element->cpEditUrl . '">' . $this->element->{$this->elementTitle} . '</a>']);
    }

    /**
     * Get the element chosen
     * 
     * @return ?Element
     */
    public function getElement(): ?Element
    {
        if ($this->_element === null and $this->elementId) {
            $this->_element = $this->elementType::find()->id($this->elementId)->anyStatus()->one();
        }
        return $this->_element;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'related-to-' . strtolower($this->elementName);
    }

    /**
     * @inheritDoc
     */
    public function hasConfig(): bool
    {
        return true;
    }

    /**
     * Get the element type
     * 
     * @return string
     */
    public function getElementType(): string
    {
        return Entry::class;
    }

    /**
     * Get the element name
     * 
     * @return string
     */
    public function getElementName(): string
    {
        $elems = explode('\\', $this->elementType);
        return end($elems);
    }

    /**
     * Get the element type
     * 
     * @return string
     */
    public function getElementTitle(): string
    {
        return 'title';
    }

    /**
     * @inheritDoc
     */
    public function configTemplate(): string
    {
        return 'triggers/conditions/related-to';
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        $element = $data['asset'] ?? $data['entry'] ?? $data['user'] ?? $data['category'];
        $query = (new Query())
            ->select(['relations.id'])
            ->from(['{{%relations}}'])
            ->leftJoin('{{%elements}}', 'elements.id = relations.sourceId')
            ->where(['sourceId' => $element->id])
            ->andWhere(['targetId' => $this->elementId]);
        return $query->exists();
    }

    /**
     * @inheritDoc
     */
    protected function defineForTriggers(): ?array
    {
        return ['asset-saved', 'category-saved', 'entry-saved', 'product-saved', 'order-saved', 'user-email-verified', 'user-fails-login', 'user-locked', 'user-saved', 'user-suspended', 'user-unlocked', 'user-unsuspended'];
    }
}