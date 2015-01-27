<?php

/**
 * @param str $list The list that will be appended to
 * @param str $item The item to be appended to the list
 * @param str $separator The text that separates the item from the preceding list (optional, defaults to ", ")
 * @return The list updated to include the new item, separated from any prior items byt the separator
 **/
function addToList($list, $item, $separator = ", ")
{
	if (strlen($list))
	{
		return $list . $separator . $item;
	}
	else
	{
		return $item;
	}
}

/**
 * @param str $text Any text value
 * @return A tokenized version of the text: all runs of non-alphanumeric characters stripped and replaced with a single "-"
 **/
function tokenize($text, $modifier = "")
{
	return md5(strtolower(preg_replace("|\W+(.*)\W+|","$1", preg_replace("|\W+|", "-", $text . $modifier))));
}

/**
 * @param str $left Any text value
 * @param str $right Any text value
 * @param str $seam Any text value (defaults to "/")
 * @return $left and $right concatenated with exactly one $seam in between them
 **/
function concatenateSeam($left, $right, $seam = "/")
{
	$left = preg_replace("|^(.*){$seam}$|", "$1", $left);
	$right = preg_replace("|^{$seam}(.*)$|", "$1", $right);
	return "{$left}{$seam}{$right}";
}

?>