<?php

namespace Ryssbowh\CraftTriggers;

use Ryssbowh\CraftTriggers\services\TriggersService;
use Ryssbowh\CraftTriggers\variables\TriggersVariable;
use craft\base\Plugin;
use craft\events\RebuildConfigEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\ProjectConfig;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use yii\base\Event;

class Triggers extends Plugin
{
    /**
     * @var Triggers
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */
    public $hasCpSection = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'triggers' => TriggersService::class,
        ]);

        $this->registerProjectConfig();
        $this->registerTwigVariables();
        $this->registerPermissions();

        if (\Craft::$app->plugins->isPluginInstalled('triggers')) {
            $this->initializeTriggers();
        }

        if (\Craft::$app->request->getIsCpRequest()) {
            $this->registerCpRoutes();
        }
    }

    /**
     * Register new twig variable craft.triggers
     */
    public function registerTwigVariables()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
            $e->sender->set('triggers', TriggersVariable::class);
        });
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem ()
    {
        if (\Craft::$app->user->checkPermission('manageTriggers')) {
            $item = parent::getCpNavItem();
            $item['label'] = \Craft::t('triggers', 'Triggers');
            return $item;
        }
        return null;
    }

    /**
     * Initialize triggers
     */
    protected function initializeTriggers()
    {
        foreach($this->triggers->activeTriggers as $trigger) {
            $trigger->initialize();
        }
    }

    /**
     * Registers permissions
     */
    protected function registerPermissions()
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {
                $event->permissions[\Craft::t('triggers', 'Triggers')] = [
                    'manageTriggers' => [
                        'label' => \Craft::t('triggers', 'Manage triggers')
                    ]
                ];
            }
        );
    }

    /**
     * Registers project config events
     */
    protected function registerProjectConfig()
    {
        \Craft::$app->projectConfig
            ->onAdd(TriggersService::CONFIG_KEY.'.{uid}',    [$this->triggers, 'handleChanged'])
            ->onUpdate(TriggersService::CONFIG_KEY.'.{uid}', [$this->triggers, 'handleChanged'])
            ->onRemove(TriggersService::CONFIG_KEY.'.{uid}', [$this->triggers, 'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            Triggers::$plugin->triggers->rebuildConfig($e);
        });
    }

    /**
     * Register cp routes
     */
    protected function registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'triggers' => 'triggers/cp-triggers',
            ]);
            if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                $event->rules = array_merge($event->rules, [
                    'triggers/add' => 'triggers/cp-triggers/add',
                    'triggers/edit/<id:\d+>' => 'triggers/cp-triggers/edit',
                ]);
            }
        });
    }
}
