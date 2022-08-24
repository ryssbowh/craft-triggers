<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use craft\elements\User;

class RelatedToUser extends RelatedToEntry
{
    /**
     * @inheritDoc
     */
    public function getElementType(): string
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function getElementTitle(): string
    {
        return 'friendlyName';
    }
}