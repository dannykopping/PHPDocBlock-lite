<?php

	//	require_once dirname(__FILE__) . "/element/MethodElement.php";
	//	require_once dirname(__FILE__) . "/element/AnnotationElement.php";

	//This tells PHP to auto-load classes using Slim's autoloader; this will
	//only auto-load a class file located in the same directory as Slim.php
	//whose file name (excluding the final dot and extension) is the same
	//as its class name (case-sensitive). For example, "View.php" will be
	//loaded when Slim uses the "View" class for the first time.
	spl_autoload_register(array('DocBlockParser', 'autoload'));

	/**
	 *	A simple PHP DocBlock parser
	 *
	 * @author Danny Kopping <dannykopping@gmail.com>
	 */
	class DocBlockParser
	{
		private $validBlockRegex = "/\/\*{2}(.+)\*\//sm";
		private $allDocBlockLinesRegex = "%^(\s+)?\*{1}.+[^/]$%m";
		private $annotationRegex = "/^(@[\w]+)(.+)?$/m";
		private $splitByWhitespaceRegex = "/^(@[\w]+)(.+)?$/m";

		private $currentMethod;
		private $currentAnnotation;

		private $methods;
		private $annotations;

		public function __construct()
		{
			// check for the existence of the Reflection API
			$this->checkCompatibility();
		}

		/**
		 * DocBlockParser autoloader
		 *
		 * Lazy-loads class files when a given class is first referenced.
		 *
		 * @param $class
		 * @return void
		 */
		public static function autoload($class)
		{
			// check same directory
			$file = realpath(dirname(__FILE__)."/".$class.".php");

			// if none found, check the element directory
			if(!$file)
				$file = realpath(dirname(__FILE__)."/element/".$class.".php");

			// if found, require_once the sucker!
			if($file)
				require_once $file;
		}

		/**
		 * Check to see if all dependencies are satisfied
		 *
		 * @throws Exception
		 */
		protected function checkCompatibility()
		{
			if (!class_exists("Reflection"))
				throw new Exception("Fatal error: Dependency 'Reflection API' not met. PHP5 is required.");
		}

		public function analyze($className)
		{
			$reflector = new ReflectionClass($className);

			$this->methods = array();
			$this->annotations = array();

			foreach ($reflector->getMethods() as $method)
			{
				$m = new MethodElement();
				$m->name = $method->getName();

				preg_match_all($this->validBlockRegex, $method->getDocComment(), $matches, PREG_PATTERN_ORDER);
				array_shift($matches);

				preg_match_all($this->allDocBlockLinesRegex, $method->getDocComment(), $result, PREG_PATTERN_ORDER);
				for ($i = 0; $i < count($result[0]); $i++)
				{
					$this->currentMethod =& $m;
					$this->parse($result[0][$i]);
				}

				$this->methods[] = $m;
			}
		}

		/**
		 * Parses
		 *
		 * @param $string
		 */
		protected function parse($string)
		{
			$an = new AnnotationElement($this->currentMethod);

			// strip first instance of asterisk
			$string = substr($string, strpos($string, "*") + 1);
			$string = trim($string);

			preg_match_all($this->annotationRegex, $string, $result, PREG_PATTERN_ORDER);

			if (!empty($result[1]))
			{
				for ($i = 0; $i < count($result[2]); $i++)
				{
					if (!empty($result[2]))
					{
						$an->name = $result[1][0];
						$an->values = preg_split($this->splitByWhitespaceRegex, trim($result[2][$i]), null);
					}
				}

				$this->currentMethod->annotations[] = $an;
				$this->annotations[] = $this->currentAnnotation = $an;
			}
			else
			{
				if(!$this->currentAnnotation)
					$this->currentMethod->description .= $string . "\n";
				else
				{
					if(!empty($this->currentAnnotation->values))
						$this->currentAnnotation->values[count($this->currentAnnotation->values) - 1] .= "\n".$string;
				}
			}
		}

		public function getMethods()
		{
			return $this->methods;
		}

		public function getAnnotations()
		{
			return $this->annotations;
		}
	}

?>