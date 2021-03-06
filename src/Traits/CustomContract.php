<?php namespace inkvizytor\FluentForm\Traits;

use inkvizytor\FluentForm\Base\Field;

/**
 * Class CustomContract
 *
 * @package inkvizytor\FluentForm
 */
trait CustomContract
{
    /**
     * @param string $name
     * @param array $arguments
     * @return Field
     */
    public function __call($name, $arguments)
    {
        array_unshift($arguments, $this->handler());

        $reflector = new \ReflectionClass(config('fluentform.controls.'.$name));
        $constructor = $reflector->getConstructor();
        
        return $reflector->newInstanceArgs($arguments);
    }
}