<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use craft\commerce\elements\Product;

class RelatedToProduct extends RelatedToEntry
{
    /**
     * @inheritDoc
     */
    public function getElementType(): string
    {
        return Product::class;
    }
}