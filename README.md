# Triggers for Craft CMS ^3.5

This plugin defines the structure for trigger management in the control panel. A trigger is triggered by a Craft event under certain conditions and applies certain actions when those conditions are met. Conditions can be specific to each trigger or global.

This plugin defines triggers and conditions and a Log action for testing purposes, **it does not come with "real" actions** as this would be site specific to your needs, actions can be easily created and registered on this plugin.

To define new triggers, conditions or actions you'll need a custom module or plugin, find more documentation on the [Craft docs](https://craftcms.com/docs/3.x/extend/plugin-guide.html) on how to define a new plugin. Or see an example in the [examples](examples) folder.

Please open an new issue to request a usefull trigger/condition that doesn't exist yet or a pull request if you have one to add.

## Triggers

![Triggers](/images/triggers.png)

A trigger has a certain type which define which event it answers to, it also defines data that can be used by the conditions and actions.

Triggers defined by this plugin :


| Type                    | Data                         |
|-------------------------|------------------------------|
| Asset deleted           | `event`, `asset`             |
| Asset saved             | `event`, `asset`, `isNew`    |
| Category deleted        | `event`, `category`          |
| Category saved          | `event`, `category`, `isNew` |
| Entry deleted           | `event`, `entry`             |
| Entry saved             | `event`, `entry`, `isNew`    |
| User activated          | `event`, `user`              |
| User assigned to groups | `event`, `user`              |
| User email verified     | `event`, `user`              |
| User fails to login     | `event`, `user`              |
| User locked             | `event`, `user`              |
| User saved              | `event`, `user`, `isNew`     |
| User deleted            | `event`, `user`              |
| User suspended          | `event`, `user`              |
| User unlocked           | `event`, `user`              |
| User unsuspended        | `event`, `user`              |
| Custom                  | `event`                      |

If you're using Craft commerce, the following will also be available :

| Type                    | Data                                                 |
|-------------------------|------------------------------------------------------|
| Order authorized        | `event`, `order`                                     |
| Order completed         | `event`, `order`                                     |
| Order saved             | `event`, `order`                                     |
| Order deleted           | `event`, `order`                                     |
| Order paid              | `event`, `order`                                     |
| Payment captured        | `event`, `transaction`                               | 
| Payment completed       | `event`, `transaction`                               |
| Payment processed       | `event`, `transaction`, `order`, `form`, `response`  |
| Payment refunded        | `event`, `transaction`, `refundTransaction`, `amount`|
| Product saved           | `event`, `product`, `isNew`                          |
| Product deleted         | `event`, `product`                                   |

### Add a new trigger

Use the form to add a new trigger, then add conditions and actions. Conditions and actions that have config will have a little cog that opens the configuration form. They can be dragged around to change their order.

![Edit](/images/edit.png)

### Define a new trigger

Add a new trigger class, for example :

```
<?php

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\ModelEvent;
use yii\base\Event;

class AssetBeforeDeleted extends Trigger
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return \Craft::t('triggers', 'Before an asset is deleted');
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'asset-before-deleted';
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $_this = $this;
        Event::on(Asset::class, Element::EVENT_BEFORE_DELETE, function (ModelEvent $e) use ($_this) {
            Triggers::$plugin->triggers->onTriggerTriggered($_this, [
                'asset' => $e->sender
            ]);
        });
    }
}
```

You can define any number of attributes for your trigger and use them and validate them normally, they will automatically be added to project config and saved in database. You must not define the following attributes : id, uid, handle, type, name, active, triggered, dateCreated, dateUpdated, conditions, actions, config, data, instructions, tip.

Then register it :

```
use Ryssbowh\CraftTriggers\services\TriggersService;
use Ryssbowh\CraftTriggers\events\RegisterTriggersEvent;

Event::on(TriggersService::class, TriggersService::EVENT_REGISTER_TRIGGERS, function (RegisterTriggersEvent $e) {
    $e->add(new AssetBeforeDeleted);
});
```

You can define some configuration which will show on the add trigger form by defining those 2 methods on your trigger :

```
public function hasConfig(): bool
{
    return true;
}

/**
 * @inheritDoc
 */
public function configTemplate(): ?string
{
    return 'my-template';
}
```
You can show some instructions and/or tips to the user which would show on the add trigger form:
```
/**
 * @inheritDoc
 */
public function getInstructions(): string
{
    return 'These are instructions';
}

/**
 * @inheritDoc
 */
public function getTip(): string
{
    return 'This is a tip';
}
```

### Modify triggers worflow

Trigger workflow can be changed at several times during its execution through events :

```
use Ryssbowh\CraftTriggers\services\TriggersService;
use Ryssbowh\CraftTriggers\events\RegisterTriggersEvent;

Event::on(TriggersService::class, TriggersService::EVENT_BEFORE_CHECKING_CONDITIONS, function (TriggerTriggeredEvent $e) {
    $e->handled = true; //This stops the execution entirely
    $e->triggerData['my-variable'] = 'my-value';
});

Event::on(TriggersService::class, TriggersService::EVENT_AFTER_CHECKING_CONDITIONS, function (TriggerTriggeredEvent $e) {
    //$e->result contains the result of conditions (true or false)
    $e->triggerData['my-variable'] = 'my-value';
});

//Those won't be executed if the result of conditions is false :

Event::on(TriggersService::class, TriggersService::EVENT_BEFORE_APPLYING_ACTIONS, function (TriggerTriggeredEvent $e) {
    $e->handled = true; //This stops the execution entirely
    $e->triggerData['my-variable'] = 'my-value';
});

Event::on(TriggersService::class, TriggersService::EVENT_AFTER_APPLYING_ACTIONS, function (TriggerTriggeredEvent $e) {
});
```

## Conditions

Conditions can be global (they can be added to any trigger), or trigger specific. Each condition has an operator ("and" or "or") which allows building complex conditions requirements.

Conditions defined by this plugin :

| Name                 | Description                                               | For trigger                                                                                                                            |
|----------------------|-----------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| Asset kind           | Choose one or several asset kind                          | Asset saved, Asset deleted                                                                                                             |
| Asset volume         | Choose one or several volume                              | Asset saved, Asset deleted                                                                                                             |
| Draft                | Draft or not                                              | Entry saved, Entry deleted, Category saved, Category deleted, Product saved, Product deleted                                           |
| Revision             | Revision or not                                           | Entry saved, Entry deleted, Category saved, Category deleted, Product saved, Product deleted                                           |
| Entry section        | Choose one or several section                             | Entry saved, Entry deleted                                                                                                             |
| Slug                 | Choose a slug                                             | Entry saved, Entry deleted, Category saved, Category deleted, Product saved, Product deleted                                           |
| Entry status         | Choose one or several statuses                            | Entry saved, Entry deleted                                                                                                             |
| Category group       | Choose one or several groups                              | Category saved, Category deleted                                                                                                       |
| Category status      | Choose one or several statuses                            | Category saved, Category deleted                                                                                                       |
| Environment variable | Choose an environment variable and a value for it         | Global                                                                                                                                 |
| Is new               | Is new or not                                             | Entry saved, Asset saved, Category saved, Product saved                                                                                |
| Request              | Choose one or several type of request (site, cp, console) | Global                                                                                                                                 |
| Site                 | Choose one or several sites                               | Global                                                                                                                                 |
| User group           | Choose one or several user group                          | User saved, User email verified, User activated, User locked, User unlocked, User suspended, User unsuspended, User assigned to groups |
| User status          | Choose one or several status                              | User saved, User assigned to groups                                                                                                    |
| Related to entry     | Choose one entry                                          | All element related triggers                                                                                                           |
| Related to asset     | Choose one asset                                          | All element related triggers                                                                                                           |
| Related to user      | Choose one user                                           | All element related triggers                                                                                                           |
| Related to category  | Choose one category                                       | All element related triggers                                                                                                           |
| Related to product   | Choose one product                                        | All element related triggers                                                                                                           |

### Groups

There's a special condition "Group" which is simply a group of conditions under an operator. You can have as many groups as you want in any number of nested levels (groups inside groups).

### Define new conditions

Add a new trigger class, for example :

```
<?php

use Ryssbowh\CraftTriggers\interfaces\TriggerInterface;
use Ryssbowh\CraftTriggers\models\Condition;

class MyCondition extends Condition
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'My Condition';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'this is a new condition'
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'my-condition';
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
        return 'my-template';
    }

    /**
     * Return null for a global condition
     */
    protected function defineForTriggers(): ?array
    {
        return ['asset-saved', 'asset-deleted'];
    }

    /**
     * @inheritDoc
     */
    public function check(TriggerInterface $trigger, array $data): bool
    {
        if ($data['my-condition-is-met']) {
            return true;
        }
        return false;
    }
}
```

You can define any number of attributes for your condition and use them and validate them normally, they will automatically be added to project config and saved in database. You must not define the following attributes : id, uid, name, handle, order, operator, active, group_id, group, trigger_id, trigger, dateCreated, dateUpdated, config, data, description.

Then register it :

```
use Ryssbowh\CraftTriggers\services\TriggersService;
use Ryssbowh\CraftTriggers\events\RegisterTriggersEvent;

Event::on(TriggersService::class, TriggersService::EVENT_REGISTER_CONDITIONS, function (RegisterConditionsEvent $e) {
    $e->add(new MyCondition);
});
```

### Modify which triggers a condition applies to

Triggers for which conditions apply are hard coded on the trigger class itself but can be modified through an event :

```
use Ryssbowh\CraftTriggers\events\DefineConditionTriggers;

Event::on(Mycondition::class, Mycondition::EVENT_DEFINE_FOR_TRIGGERS, function (DefineConditionTriggers $e) {
    $e->triggers[] = 'trigger-handle';
});
```

## Actions

An action can do anything you want, it won't be executed if the conditions added to a trigger aren't met.

### Define a new action

```
class MyAction extends Action
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'My Action';
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return 'my-action';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'This is an action that does nothing';
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
        return 'my-template';
    }

    /**
     * @inheritDoc
     */
    public function apply(TriggerInterface $trigger, array $data)
    {
        //Whatever you need to do here
    }
}
```

You can define any number of attributes for your action and use them and validate them normally, they will automatically be added to project config and saved in database. You must not define the following attributes : id, uid, name, handle, order, active, trigger_id, trigger, dateCreated, dateUpdated, config, data, description.

Then register it :

```
use Ryssbowh\CraftTriggers\services\TriggersService;
use Ryssbowh\CraftTriggers\events\RegisterActionsEvent;

Event::on(TriggersService::class, TriggersService::EVENT_REGISTER_ACTIONS, function (RegisterActionsEvent $e) {
    $e->add(new MyAction);
});
```

## Requirements

php >= 7.4  
Craft >= 3.6.5

## Installation

`composer require ryssbowh/craft-triggers`

## Documentation

[Class reference](https://ryssbowh.github.io/docs/craft-triggers1/namespaces/ryssbowh-crafttriggers.html)


Icon created by [Freepik - Flaticon](https://www.flaticon.com/free-icons/finger)