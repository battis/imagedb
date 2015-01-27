<?php

class FieldEmail extends FieldText
{
	public function show ($object, $data = NULL)
	{
		if (isset($data))
		{
			return parent::show($object, $data);
		}
		else
		{
			$email = $this->get($object);
			if (isset($email) && strlen($email) > 0)
			{
				return parent::show($object, "<p><a target=\"_blank\" href=\"mailto:{$email}\">{$email}</a></p>");
			}
			else
			{
				return parent::show($object);
			}
		}
	}
}

?>