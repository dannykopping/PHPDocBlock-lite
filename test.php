<?php

	require_once "lib/DocBlockParser.php";

	$d = new DocBlockParser();
	$d->analyze("TestClass");

	$annotations = $d->getAnnotations();

	foreach($annotations as $annotation)
	{
		echo $annotation->getMethod()->name."\n";
		echo $annotation->name."\n";
		echo print_r($annotation->values, true)."\n";
	}

	class TestClass
	{
		/**
		 * This is a multiline description of something or
		 * another... and it should be maintained in one
		 * contiguous stream @dannykopping
		 *
		 * @route			/users/get
		 * @routeMethod		GET,POST	starsky hutch hooch!
		 * 					An extended comment
		 * @passBody
		 * @ignore
		 *
		 * @param $data  The data to be passed in
		 */
		public function test()
		{
			throw new Exception("something...");
			print_r(func_get_args());
		}
	}

?>