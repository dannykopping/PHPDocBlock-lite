<?php
	/**
	 *	Defines a method element relating to a DocBlock
	 */
	class MethodElement extends AbstractElement
	{
		public $name;

		private $annotations = array();

		public $description;

		private $class;

		public function __construct(ClassElement $class)
		{
			$this->class = $class;
		}

		/**
		 * Add an annotation
		 *
		 * @param AnnotationElement $annotation
		 */
		public function addAnnotation(AnnotationElement $annotation)
		{
			if(empty($this->annotations))
				$this->annotations = array();

			$this->annotations[] = $annotation;
		}

		/**
		 * Get an array of all the parsed annotations
		 *
		 * @param array	$filter	(optional) Filter by annotation name
		 * @return array[AnnotationElement]
		 */
		public function getAnnotations($filter = null)
		{
			if (!$this->annotations || empty($this->annotations))
				return null;

			if (!$filter)
				return $this->annotations;

			$annotations = array();
			if($filter)
			{
				foreach ($this->annotations as $annotation)
				{
					// chop off the @ at the beginning of the attribute name
					$withoutAnnotationMarker = substr($annotation->name, 1);
					if (in_array($annotation->name, $filter) || in_array($withoutAnnotationMarker, $filter))
						$annotations[] = $annotation;
				}
			}
			else
			{
				array_merge($annotations, $this->annotations);
			}

			return $annotations;
		}

		/**
		 * Determines whether this MethodElement instance contains an annotation of a certain type
		 *
		 * @param array $filter	An annotation name
		 * @return bool
		 */
		public function hasAnnotation($filter)
		{
			$annotations = $this->getAnnotations($filter);
			return !empty($annotations);
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
?>