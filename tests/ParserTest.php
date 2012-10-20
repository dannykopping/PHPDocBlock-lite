<?php
use DocBlock\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var     Parser
     */
    private $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new Parser();
    }

    /**
     * Test that the parser instance used in this class is valid
     */
    public function testParserInstanceType()
    {
        $this->assertInstanceOf("\\DocBlock\\Parser", $this->parser);
    }

    public function testAllowInheritanceEnabled()
    {
        $this->allowInheritance(true);
    }

    public function testAllowInheritanceDisabled()
    {
        $this->allowInheritance(false);
    }

    public function testMethodFiltering()
    {
        $test = $this->createTestClass();
        $this->parser->setMethodFilter(ReflectionMethod::IS_PRIVATE);
        $this->parser->analyze($test);

        $classes = $this->parser->getClasses();
        $class   = $classes[0];

        $methods = $class->getMethods();
        foreach ($methods as $method) {
            $reflectionObj = $method->getReflectionObject();
            $this->assertEquals(true, $reflectionObj->isPrivate());
        }
    }

    private function allowInheritance($allow = true)
    {
        $test = $this->createTestClass(true);

        $this->parser->setAllowInherited($allow);
        // allow all access modifications - public, protected & private
        $this->parser->setMethodFilter(null);
        $this->parser->analyze($test);

        $classes = $this->parser->getClasses();
        $class   = $classes[0];
        $methods = $class->getMethods();

        // if inheritance is not allowed, there should be no methods found
        if (!$allow) {
            $this->assertEquals(0, count($methods));
            return;
        }

        if (count($methods) < 1) {
            $this->fail("Expected to get at least one method, none found.");
        }

        // DerivedClass has no methods of its own, therefore all of its methods
        // should be declared by its parent class *only*
        foreach ($methods as $method) {
            $reflectionObj = $method->getReflectionObject();
            $this->assertNotEquals($reflectionObj->class, get_class($test));
        }
    }

    /**
     * Test class factory
     *
     * @param bool $derived
     *
     * @return mixed
     */
    private function createTestClass($derived = false)
    {
        $className = $derived ? "DerivedClass" : "TestClass";
        return new $className;
    }
}


class TestClass
{
    /**
     * @some            annotation
     * @with            multiple
     *                  lines
     *                  of text with        different spacing
     */
    private function iAmPrivate()
    {
    }

    /**
     * @by              Tony Soprano
     * @position        The Boss
     */
    protected function iAmProtected()
    {
    }

    /**
     * @annotation      value
     */
    public function iAmPublic()
    {
    }
}

class DerivedClass extends TestClass
{
}
