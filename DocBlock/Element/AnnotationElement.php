<?php
namespace DocBlock\Element;

use Exception;
use Reflector;

/**
 *    Defines an annotation defined in a DocBlock
 */
class AnnotationElement extends Base
{
    /**
     * @var array    The values associated with this annotation
     */
    public $values = array();

    /**
     * @var    MethodElement    The associated MethodElement instance to which this annotation belongs
     */
    private $element;

    /**
     * @param $base    Base    The associated element instance to which this annotation belongs
     */
    public function __construct($base)
    {
        $this->element = $base;
    }

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * Return first element
     *
     * @return array
     */
    public function getValue()
    {
        return count($this->values) >= 1 ? $this->values[0] : $this->values;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return MethodElement    The associated MethodElement instance to which this annotation belongs
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @override
     *
     * @param Reflector $reflectionObject
     *
     * @throws Exception
     */
    public function setReflectionObject(Reflector $reflectionObject)
    {
        throw new Exception("Annotations do not have corresponding Reflection objects");
    }

    /**
     * @override
     *
     * @throws Exception
     */
    public function getReflectionObject()
    {
        throw new Exception("Annotations do not have corresponding Reflection objects");
    }
}
