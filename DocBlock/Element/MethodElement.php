<?php
namespace DocBlock\Element;

/**
 *    Defines a method element relating to a DocBlock
 */
class MethodElement extends Base
{
    private $class;

    public function __construct(ClassElement $class)
    {
        $this->class = $class;
    }

    /**
     * Get the related ClassElement instance
     *
     * @return ClassElement
     */
    public function getClass()
    {
        return $this->class;
    }
}
