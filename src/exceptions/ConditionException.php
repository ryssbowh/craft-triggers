<?php 

namespace Ryssbowh\CraftTriggers\exceptions;

class ConditionException extends \Exception
{
    public static function noId(int $id)
    {
        return new static('Condition with id '.$id.' doesn\'t exist');
    }

    public static function noHandle(string $handle)
    {
        return new static('Condition with handle "'.$handle.'" doesn\'t exist');
    }

    public static function handleMissing()
    {
        return new static('Condition handle is missing from array of parameters');
    }

    public static function handleDefined(string $handle)
    {
        return new static("Condition handle '$handle' is already defined");
    }
}