<?php

	require_once "lib/DocBlockParser.php";

	$d = new DocBlockParser();
	$d->analyze("TestClass");
	print_r($d->getAnnotations());


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