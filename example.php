<?php

	require_once "lib/DocBlockParser.php";

	$d = new DocBlockParser();
	$d->analyze("TestClass");

	$methods = $d->getMethods();

	foreach($methods as $method)
	{
		$annotations = $method->getAnnotations(array("param"));
		foreach ($annotations as $annotation)
		{
			echo $annotation->getMethod()->name . "\n";
			echo $annotation->name . "\n";
			echo print_r($annotation->values, true) . "\n";
		}
	}

	class TestClass
	{
		/**
		 * This is the DocBlock description
		 * @param $data  The data to be passed in
		 */
		public function test($data)
		{
		}
	}

?>