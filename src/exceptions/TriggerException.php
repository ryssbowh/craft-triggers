<?php 

namespace Ryssbowh\CraftTriggers\exceptions;

class TriggerException extends \Exception
{
    public static function noId(int $id)
    {
        return new static('Trigger with id '.$id.' doesn\'t exist');
    }

    public static function noHandle(string $handle)
    {
        return new static('Trigger with handle "'.$handle.'" doesn\'t exist');
    }

    public static function handleMissing()
    {
        return new static('Trigger handle is missing from array of parameters');
    }

    public static function handleDefined(string $handle)
    {
        return new static("Trigger handle '$handle' is already defined");
    }
}