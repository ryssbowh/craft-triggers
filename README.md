# Triggers for Craft CMS 4

This plugin defines the structure for trigger management in the control panel. A trigger is triggered by a Craft event under certain conditions and applies certain actions when those conditions are met. Conditions can be specific to each trigger or global.

This plugin defines triggers and conditions and a Log action for testing purposes, **it does not come with "real" actions** as this would be site specific to your needs, actions can be easily created and registered on this plugin.

To define new triggers, conditions or actions you'll need a custom module or plugin, find more documentation on the [Craft docs](https://craftcms.com/docs/3.x/extend/plugin-guide.html) on how to define a new plugin. Or see an example in the [examples](examples) folder.

Please open an new issue to request a usefull trigger/condition that doesn't exist yet or a pull request if you have one to add.

Also available for [Craft 3.5](https://github.com/ryssbowh/craft-triggers/tree/1.0)

## Requirements

Craft >= 4.0.0

## Installation

`composer require ryssbowh/craft-triggers:^2.0`

## Documentation

- [Plugin documentation](https://puzzlers.run/plugins/triggers/2.x)
- [Class reference](https://ryssbowh.github.io/docs/craft-triggers/namespaces/ryssbowh-crafttriggers.html)