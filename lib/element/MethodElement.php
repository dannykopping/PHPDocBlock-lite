<?php
	/**
	 *	Defines a method element relating to a DocBlock
	 */
	class MethodElement
	{
		public $name;

		public $annotations = array();

		public $description;

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
	}
?>