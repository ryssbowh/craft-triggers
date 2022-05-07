<?php

namespace Ryssbowh\CraftTriggers\migrations;

use Craft; 
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%triggers_triggers}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'handle' => $this->string(255)->notNull(),
            'active' => $this->boolean(),
            'data' => $this->text(),
            'triggered' => $this->integer(11)->defaultValue(0),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%triggers_conditions}}', [
            'id' => $this->primaryKey(),
            'handle' => $this->string(255)->notNull(),
            'operator' => $this->string(255)->notNull(),
            'group_id' => $this->integer(11),
            'trigger_id' => $this->integer(11),
            'order' => $this->integer(11),
            'data' => $this->text(),
            'active' => $this->boolean(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%triggers_actions}}', [
            'id' => $this->primaryKey(),
            'handle' => $this->string(255)->notNull(),
            'trigger_id' => $this->integer(11),
            'order' => $this->integer(11),
            'data' => $this->text(),
            'active' => $this->boolean(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
        $this->addForeignKey('triggers_conditions_trigger_id_fk', '{{%triggers_conditions}}', ['trigger_id'], '{{%triggers_triggers}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('triggers_actions_trigger_id_fk', '{{%triggers_actions}}', ['trigger_id'], '{{%triggers_triggers}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('triggers_conditions_group_id_fk', '{{%triggers_conditions}}', ['group_id'], '{{%triggers_conditions}}', ['id'], 'CASCADE', null);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%triggers_conditions}}');
        $this->dropTableIfExists('{{%triggers_actions}}');
        $this->dropTableIfExists('{{%triggers_triggers}}');
    }
}
