<?php

namespace Ryssbowh\CraftTriggers\models\conditions;

use craft\elements\Asset;

class RelatedToAsset extends RelatedToEntry
{
    /**
     * @inheritDoc
     */
    public function getElementType(): string
    {
        return Asset::class;
    }
}