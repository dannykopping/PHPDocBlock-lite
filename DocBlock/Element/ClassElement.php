<?php
namespace DocBlock\Element;

use DocBlock\Element\Base;

/**
 *    Defines a class element with several parsed MethodElement instances
 */
class ClassElement extends Base
{
    public $name;
    private $methods;

    /**
     * @param MethodElement $method        Add a parsed method to this class
     */
    public function addMethod(MethodElement $method)
    {
        if (empty($this->methods)) {
            $this->methods = array();
        }

        $this->methods[] = $method;
    }

    /**
     * Get an array of MethodElement instances
     *
     * @return array[MethodElement]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get a specific MethodElement instance by name
     *
     * @param $name
     *
     * @return MethodElement
     */
    public function getMethod($name)
    {
        if (count($this->methods) <= 0) {
            return null;
        }

        foreach ($this->methods as $method) {
            if ($method->name == trim($name)) {
                return $method;
            }
        }

        return null;
    }
}
