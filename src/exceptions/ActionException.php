<?php 

namespace Ryssbowh\CraftTriggers\exceptions;

class ActionException extends \Exception
{
    public static function noId(int $id)
    {
        return new static('Action with id '.$id.' doesn\'t exist');
    }

    public static function noHandle(string $handle)
    {
        return new static('Action with handle "'.$handle.'" doesn\'t exist');
    }

    public static function handleDefined(string $handle)
    {
        return new static("Action handle '$handle' is already defined");
    }

    public static function handleMissing()
    {
        return new static('Action handle is missing from array of parameters');
    }
}