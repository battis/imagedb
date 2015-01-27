<?php

/**
 * This should be used in preference to the implode function, to allow for
 * the possibility of differing delimiting schemes for canonical options
 *
 * @param ArrayObject $optionArray The option as an array: array("system", "path", "root")
 * @return A canonical option: "system:path:root"
 **/
function canonizeOption(array $optionArray)
{
	return implode(CANONICAL_OPTION_DELIMITER, $optionArray);
}

/**
 * This should be used in preference to the explode function, to allow for
 * the possibility of differing delimiting schemes for canonical options
 *
 * @param str $canonicalOption A canonical option: "system:path:root"
 * @return An array containing each component of the option: array ("system", "path", "root")
 **/
function uncanonizeOption($canonicalOption)
{
	return explode(CANONICAL_OPTION_DELIMITER, $canonicalOption);
}

/**
 * @param str $canonicalOption An option listed canonically: "system:path:root"
 * @return The value (or array of values) associated with the canonicalOption, with library options superseding system (database) options, which supersede default (hard-coded in includes/defaults.php) options.
 **/
function getOption($canonicalOption)
{
	/* TODO cache option lookups to increase efficiency */
	
	global $DEFAULTS, $IDB, $CONTAINER;

	$options = uncanonizeOption($canonicalOption);

	/* point each option finger at the root of an option tree (the local container, the system-wide container (loaded from the database), or the default container (hard-coded in defaults.inc.php)) */
	if (is_object($CONTAINER))
	{
		$localFinger =& $CONTAINER->getOptions();
	}
	if (is_object($IDB))
	{
		$systemFinger =& $IDB->getOptions();
	}
	$defaultFinger =& $DEFAULTS;

	/* walk through the option, element-by-element and move our option fingers down the option trees so long as the options are set, unsetting any fingers that run out of tree before we run out of option */
	foreach($options as $option)
	{
		if (isset ($localFinger) && isset ($localFinger[$option]))
		{
			/* advance the local finger down the local option tree */
			$localFinger =& $localFinger[$option];
		}
		else
		{
			/* option not found in the local container */
			unset ($localFinger);
		}

		if (isset($systemFinger) && isset ($systemFinger[$option]))
		{
			/* advance the system finger down the system option tree */
			$systemFinger =& $systemFinger[$option];
		}
		else
		{
			/* option not found in the system container */
			unset ($systemFinger);
		}

		if (isset ($defaultFinger) && isset ($defaultFinger[$option]))
		{
			/* advance the default finger down the default option tree */
			$defaultFinger =& $defaultFinger[$option];
		}
		else
		{
			/* option not found in the default container */
			unset ($defaultFinger);
		}
	}

	/* work through the containers, checking to see if any fingers are still set. If so, return the most relevant container that still has a set finger (local is more relevant than system, system is more relevant than default) */
	if (isset ($localFinger))
	{
		return $localFinger;
	}
	elseif (isset ($systemFinger))
	{
		return $systemFinger;
	}
	elseif (isset ($defaultFinger))
	{
		return $defaultFinger;
	}
	else
	{
		return null;
	}
}

?>