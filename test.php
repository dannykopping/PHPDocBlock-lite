<?php

	require_once "lib/DocBlockParser.php";

	$d = new DocBlockParser();
	$d->analyze("TestClass");

	$methods = $d->getMethods();

	foreach($methods as $method)
	{
		$annotations = $method->getAnnotations(array("ignore", "param"));
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
		 * This is a multiline descrription of something or
		 * another... and it should be maintained in one
		 * contiguous stream @dannykopping
		 *
		 * @route			/users/get
		 * @routeMethod		GET,POST	starsky hutch hooch!
		 *					 An extended comment
		 * @passBody
		 *
		 * @param $data  The data to be passed in
		 */
		public function test()
		{
			throw new Exception("something...");
			print_r(func_get_args());
		}

		/**
		 * @ignore $test  The data to be passed in to test
		 * @param something
		 */
		public function test2()
		{
			throw new Exception("something...");
			print_r(func_get_args());
		}
	}

?>