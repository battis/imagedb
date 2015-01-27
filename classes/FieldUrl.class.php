<?php

class FieldUrl extends FieldText
{
	public function show ($item, $data = NULL)
	{
		if (isset($data))
		{
			return parent::show($item, $data);
		}
		else
		{
			$href = $this->get($item);
			if (isset($href) && strlen($href) > 0)
			{
				return parent::show($item, "<p><a target=\"_blank\" href=\"{$href}\">{$href}</a></p>");
			}
			else
			{
				return parent::show($item);
			}
		}
	}
}

?>