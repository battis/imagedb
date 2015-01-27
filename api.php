<?php

require_once ("imagedb.inc.php");

$error =  "API REQUEST NOT UNDERSTOOD\n" . print_r($_REQUEST, true);

/* FIXME load $_REQUEST["item"], $_REQUEST["container"] and use them! */
try
{
	$containerRequest = new Container((int) $_REQUEST["container"], $IDB);
	$itemRequest = new Item((int) $_REQUEST["item"], $containerRequest);
}
catch (Exception $e)
{
	echo $error;
	exit;
}

switch (sanitizeApiTerm($_REQUEST["action"]))
{
	case API_APPEND_ACTION:
	{
		switch (sanitizeApiTerm($_REQUEST["object"]))
		{
			default:
			{
				echo $error;
				exit;
			}
		}
	}
	case API_SAVE_ACTION:
	{
		switch(sanitizeApiTerm($_REQUEST["object"]))
		{
			case API_METADATA_OBJECT:
			{
				$field = getOption("fields:{$_REQUEST["key"]}");
				$field->save($itemRequest, $_REQUEST["data"]);
				echo $field->show($itemRequest);
				exit;
			}
			default:
			{
				echo $error;
				exit;
			}
		}
	}
	case API_GET_ACTION:
	{
		switch(sanitizeApiTerm($_REQUEST["object"]))
		{
			case API_METADATA_OBJECT:
			{
				$field = getOption("fields:{$_REQUEST["key"]}");
				echo $field->show($itemRequest);
				exit;
			}
			default:
			{
				echo $error;
				exit;
			}
		}
	}
	case API_EDIT_ACTION:
	{
		switch (sanitizeApiTerm($_REQUEST["object"]))
		{
			case API_METADATA_OBJECT:
			{
				$field = getOption("fields:{$_REQUEST["key"]}");
				echo $field->edit($itemRequest);
				exit;
			}
			default:
			{
				echo $error;
				exit;
			}
		}
	}
	case API_DELETE_ACTION:
	{
		switch (sanitizeApiTerm($_REQUEST["object"]))
		{
			case API_METADATA_OBJECT:
			{
				$field = getOption("fields:{$_REQUEST["key"]}");
				$field->delete($itemRequest);
				echo $field->show($itemRequest);
				exit;
			}
			default:
			{
				echo $error;
				exit;
			}
		}
	}
	default:
	{
		echo $error;
		exit;
	}
}

echo $response;

?>