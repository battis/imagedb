<?php

class Tag
{
	protected $tag;
	protected $item;
	protected $container;
	
	public function __constructor()
	{
		$args = func_get_args();
		$numArgs = func_num_args();
		switch ($numArgs)
		{
			case 2:
				if (is_string($args[0]) && (is_a($args[1], "Item")))
				{
					$this->parameterConstructor($args[0], $args[1]);
					break;
				}
			default:
				throw new ExceptionConstructor ("Unknown constructor: Tag(" . print_r($args, true) . ")");
		}
	}
	
	protected function parameterConstructor($tag, $item)
	{
		$this->tag = $tag;
		$this->item = $item;
		$this->container = $this->item->getContainer();
	}
}

?>