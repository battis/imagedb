<?php

// FIXME there are efficiencies and improvements to be had throughout, I'm sure

include_once ("./imagedb.inc.php");

define ("RED_CHANNEL", 0);
define ("GREEN_CHANNEL", 1);
define ("BLUE_CHANNEL", 2);
define ("ALPHA_CHANNEL", 3);

function passthruImageFile($imagePath)
{
	$imageFile = fopen($imagePath, 'rb');
	header("Content-Type: image/jpeg");
	header("Content-Length: " . filesize($imagePath));
	fpassthru($imageFile);
	fclose($imageFile);
	exit;
}

/* no image, no point in running! */
if (!isset($ITEM))
{
	// TODO generate an error image?
	exit;
}

$filePath = concatenatePath($CONTAINER->getPath(), $ITEM->getPath());

/* should we be generating a thumbnail or a larger image? */
$generateThumbnail = isset($_REQUEST["thumbnail"]);

/* if the library has a cache path, check to see if we have a cached thumbnail -- generate one if none exists -- and load the thumbnail if that was requested */
$cachePath = replacePathVariables(getOption("cache:path"));
if ($cachePath)
{
	if (file_exists($cachePath) == false)
	{
		error_log("Creating cache directory {$cachePath} with permissions 0770");
		mkdir($cachePath, 0770, true);
		// FIXME create any parent directories that need to be created
		// TODO test to make sure the directory was really created
	}

	$imageCachePath = concatenatePath ($cachePath, $ITEM->getHash() . ".jpg");
	if ($generateThumbnail)
	{	if (file_exists($imageCachePath))
		{
			passthruImageFile($imageCachePath);
			exit;
		}
	}
}

preg_match("|.*\.(\w{3,4})|", $filePath, $matches);
$fileExtension = strToLower($matches[1]);

switch ($fileExtension)
{
	case "jpg":
	case "jpeg":
		$imageData = imagecreatefromjpeg($filePath);
		break;

	case "gif":
		$imageData = imagecreatefromgif($filePath);
		break;

	case "png":
		$imageData = imagecreatefrompng($filePath);
		break;

	default:
		/* TODO This needs some sort of "smart" handler to address different types of files that might be images -- but that is secure enough not to share files that are not images. */
		//addMessage(createMessage("unknown image type", $MTYPE["info"], "path", $_SESSION["image"]["path"]));
		exit;
}

$displayImageSize = false;
if ($generateThumbnail)
{
	$displayImageSize = getOption("thumbnail:size");
}
else
{
	$CONTAINERImageSize = getOption("image:size");
	if (isset($_REQUEST["size"]))
	{
		if ($CONTAINERImageSize)
		{
			$displayImageSize = min($_REQUEST["size"], $CONTAINERImageSize);
		}
		else
		{
			$displayImageSize = $_REQUEST["size"];
		}
	}
	else
	{
		if ($CONTAINERImageSize)
		{
			$displayImageSize = $CONTAINERImageSize;
		}
		else
		{
			$displayImageSize = false;
		}
	}
}

$imageDataSize = getimagesize($filePath);
$imageDataWidth = $imageDataSize[0];
$imageDataHeight = $imageDataSize[1];

if (($displayImageSize !== false) && (max($imageDataWidth, $imageDataHeight) > $displayImageSize))
{
	/*
	 * FIXME This seems to botch the calculations for the image size: there is a band
	 * of white at the bottom of every image, no matter what size. The band is
	 * always the same size. (I suppose it could be the CSS...)
	 */
	$displayImageWidth = $displayImageSize * ($imageDataWidth > $imageDataHeight ? 1 : $imageDataWidth / $imageDataHeight);
	$displayImageHeight = $displayImageSize * ($imageDataHeight > $imageDataWidth ? 1 : $imageDataHeight / $imageDataWidth);
	$displayImageData = imagecreatetruecolor($displayImageWidth, $displayImageHeight);
	imagecopyresampled($displayImageData, $imageData, 0, 0, 0, 0,
		$displayImageWidth, $displayImageHeight, $imageDataWidth, $imageDataHeight);
}
else
{
	$displayImageData = $imageData;
	$displayImageWidth = $imageDataWidth;
	$displayImageHeight = $imageDataHeight;
}

if (!$generateThumbnail && getOption("watermark"))
{
	$watermarkAngle = getOption("watermark:angle");
	$watermarkColorRGB = getOption("watermark:color");
	if (isset($watermarkColorRGB[ALPHA_CHANNEL]))
	{
		$watermarkColor = imagecolorallocatealpha($displayImageData, $watermarkColorRGB[RED_CHANNEL], $watermarkColorRGB[GREEN_CHANNEL], $watermarkColorRGB[BLUE_CHANNEL], $watermarkColorRGB[ALPHA_CHANNEL]);
	}
	else
	{
		$watermarkColor = imagecolorallocate($displayImageData, $watermarkColorRGB[RED_CHANNEL], $watermarkColorRGB[GREEN_CHANNEL], $watermarkColorRGB[BLUE_CHANNEL]);
	}

	$fontFile = getOption("watermark:font");
	$fontSize = $displayImageHeight * getOption("watermark:fontsize");

	$watermarkX = $displayImageWidth * getOption("watermark:x");
	$watermarkY = $displayImageHeight * getOption("watermark:y");

	imageTtfText($displayImageData, $fontSize, $watermarkAngle,
	$watermarkX, $watermarkY, $watermarkColor, $fontFile,
	getOption("watermark:text"));
}

if ($generateThumbnail && isset($imageCachePath))
{
	error_log("Creating thumbnail {$imageCachePath}");
	$result = imagejpeg($displayImageData, $imageCachePath, getOption("cache:compression"));
}
header('Content-Type: image/jpeg');
/* FIXME send a content size header as well */
imagejpeg($displayImageData);
imagedestroy($displayImageData);

?>