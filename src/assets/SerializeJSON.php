<?php

namespace Ryssbowh\CraftTriggers\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SerializeJSON extends AssetBundle
{
    public $sourcePath = __DIR__ . '/lib';

    public $js = [
        'serializejson.js'
    ];

    public $depends = [
        CpAsset::class
    ];
}