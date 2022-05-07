<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;
use craft\helpers\App;
use yii\base\Event;

class Environment extends Condition
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $variable;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['variable', 'value'], 'required']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {

        return \Craft::t('triggers', 'Environment variable');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        if ($this->variable and $this->value) {
            return \Craft::t('triggers', '{var} equals "{value}"', ['var' => $this->variable, 'value' => $this->value]);
        }
        return \Craft::t('triggers', 'Variable not set');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'environment';
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
        return 'triggers/conditions/environment';
    }

    /**
     * Get all environment variables
     * 
     * @return array
     */
    public function getVariables(): array
    {
        $vars = [];
        foreach (array_keys($_SERVER) as $var) {
            if (is_string($var) && is_string($env = App::env($var))) {
                $vars[$var] = $var;
            }
        }
        asort($vars);
        return ['' => \Craft::t('triggers', 'Select variable')] + $vars;
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        return $_SERVER[$this->variable] == $this->value;
    }
}