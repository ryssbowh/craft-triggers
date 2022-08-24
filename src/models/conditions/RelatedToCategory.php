<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use craft\elements\Category;

class RelatedToCategory extends RelatedToEntry
{
    /**
     * @inheritDoc
     */
    public function getElementType(): string
    {
        return Category::class;
    }
}