<?php

require_once ("../imagedb.inc.php");

header('Content-Type: text/javascript');

?>

function <?= AJAX_REPLACE ?> (action, object, token, key, data, item, container)
{
	new Ajax.Request('<?= getOption("system:url:api") ?>', {
		parameters: {
			action: action,
			object: object,
			key: key,
			data: data,
			item: item,
			container: container,
		},
		onFailure: function(response) {
			alert(response.responseText);
		},
		onSuccess: function(response) {
			$(token).update(response.responseText);
		}
	});
}

function <?= AJAX_APPEND ?> (action, object, token, key, data, item, container)
{
	new Ajax.Request('<?= getOption("system:url:api") ?>', {
		parameters: {
			action: action,
			object: object,
			key: key,
			data: data,
			item: item,
			container: container
		},
		onFailure: function(response) {
			alert(response.responseText);
		},
		onSuccess: function(response) {
			$(token).update($(token) + response.responseText);
		}
	});
}