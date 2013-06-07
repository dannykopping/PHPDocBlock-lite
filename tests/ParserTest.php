<?php
use DocBlock\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test that the parser instance used in this class is valid
     */
    public function testParserInstanceType()
    {
        $parser = new Parser();
        $this->assertInstanceOf("\\DocBlock\\Parser", $parser);
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
        $parser = new Parser();

        $test = $this->createTestClass();
        $parser->setMethodFilter(ReflectionMethod::IS_PRIVATE);
        $parser->analyze($test);

        $class = $parser->getClass("TestClass");
        $this->assertNotNull($class, "Could not find class");

        $methods = $class->getMethods();
        foreach ($methods as $method) {
            $reflectionObj = $method->getReflectionObject();
            $this->assertEquals(true, $reflectionObj->isPrivate());
        }
    }

    public function testAnnotationsExistence()
    {
        $elements = array($this->getTestClass(), $this->getMethodFromTestClass());

        foreach ($elements as $element) {
            $this->assertEquals(true, $element->hasAnnotation("simple"));
        }
    }

    public function testAnnotationsValueSimple()
    {
        $elements = array($this->getTestClass(), $this->getMethodFromTestClass());

        foreach ($elements as $element) {
            $annotation = $element->getAnnotation("simple");
            $this->assertNotNull($annotation, "Could not find annotation");

            $value = "annotation";
            $this->assertEquals($value, implode("", $annotation->getValues()));
        }
    }

    public function testAnnotationsValueMultiline()
    {
        $elements = array($this->getTestClass(), $this->getMethodFromTestClass());

        foreach ($elements as $element) {

            $annotation = $element->getAnnotation("multiline");
            $this->assertNotNull($annotation, "Could not find annotation");

            $value = <<<EOD
multiple
lines
of text with        different * spacing
EOD;

            $this->assertEquals($value, implode("", $annotation->getValues()));
        }
    }

    public function testAnnotationsValueMultivalue()
    {
        $elements = array($this->getTestClass(), $this->getMethodFromTestClass());

        foreach ($elements as $element) {

            $annotation = $element->getAnnotation("multi-value");
            $this->assertNotNull($annotation, "Could not find annotation");

            $value = array("value1", "value2");

            $this->assertEquals($value, $annotation->getValues());
        }
    }

    public function testAnnotationsValueComplex()
    {
        $elements = array($this->getTestClass(), $this->getMethodFromTestClass());

        foreach ($elements as $element) {
            $annotation = $element->getAnnotation("complex");
            $this->assertNotNull($annotation, "Could not find annotation");

            $value = array(
                "value1",
                <<<EOD
value2 has multiple
lines that kinda stray into annotation territory, but it's
not...
EOD
            );

            $this->assertEquals($value, $annotation->getValues());
        }
    }

    public function testClassInstanceRetention()
    {
        $parser = new Parser();

        // test instance
        $test = $this->createTestClass();
        $parser->analyze($test);

        $class = $parser->getClass("TestClass");
        $this->assertEquals($test, $class->getInstance());
    }

    public function testClassNameNonRetention()
    {
        $parser = new Parser();

        // test classname string
        $parser->analyze('\\TestClass');

        $class = $parser->getClass("TestClass");
        $this->assertNull($class->getInstance());
    }

    //
    //      UTILITY FUNCTIONS
    //

    private function getTestClass()
    {
        $parser = new Parser();

        $test = $this->createTestClass();
        $parser->analyze($test);

        $class = $parser->getClass("TestClass");
        $this->assertNotNull($class, "Could not find class");
        return $class;
    }

    private function getMethodFromTestClass()
    {
        $class  = $this->getTestClass();
        $method = $class->getMethod("iAmPrivate");
        $this->assertNotNull($method, "Could not find method");

        return $method;
    }

    private function allowInheritance($allow = true)
    {
        $parser = new Parser();

        $test = $this->createTestClass(true);

        $parser->setAllowInherited($allow);
        // allow all access modifications - public, protected & private
        $parser->setMethodFilter(null);
        $parser->analyze($test);

        $class = $parser->getClass("DerivedClass");
        $this->assertNotNull($class, "Could not find class");
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

/**
 * Some description
 *
 * @simple                  annotation
 * @multiline               multiple
 *                          lines
 *                          of text with        different * spacing
 *
 * @multi-value             value1      value2
 *
 * @complex                 value1  value2 has multiple
 *                          lines that kinda stray into annotation territory, but it's
 *                          not...
 */
class TestClass
{
    /**
     * Some description
     *
     * @simple                  annotation
     * @multiline               multiple
     *                          lines
     *                          of text with        different * spacing
     *
     * @multi-value             value1      value2
     *
     * @complex                 value1  value2 has multiple
     *                          lines that kinda stray into annotation territory, but it's
     *                          not...
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
