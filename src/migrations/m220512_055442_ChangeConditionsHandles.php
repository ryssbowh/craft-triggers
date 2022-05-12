<?php

namespace Ryssbowh\CraftTriggers\migrations;

use Craft;
use Ryssbowh\CraftTriggers\records\Condition;
use craft\db\Migration;

/**
 * m220512_055442_ChangeConditionsHandles migration.
 */
class m220512_055442_ChangeConditionsHandles extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        //Change all 'entry-revision' types into 'revision'
        $lines = Condition::find()->where(['handle' => 'entry-revision'])->all();
        foreach ($lines as $line) {
            $line->handle = 'revision';
            $line->save(false);
        }
        //Change all 'entry-draft' types into 'draft'
        $lines = Condition::find()->where(['handle' => 'entry-draft'])->all();
        foreach ($lines as $line) {
            $line->handle = 'draft';
            $line->save(false);
        }
        //Change all 'entry-slug' types into 'slug'
        $lines = Condition::find()->where(['handle' => 'entry-slug'])->all();
        foreach ($lines as $line) {
            $line->handle = 'slug';
            $line->save(false);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220512_055442_ChangeConditionsHandles cannot be reverted.\n";
        return false;
    }
}
