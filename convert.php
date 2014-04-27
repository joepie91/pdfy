<?php
$_CPHP = true;
$_CPHP_CONFIG = "../config.json";
require("cphp/base.php");

$_APP = true;

foreach(Document::CreateFromQuery("SELECT * FROM documents") as $document)
{
	$thumb_exists = file_exists("static/thumbs/{$document->sSlugId}.png");
	
	if(!$thumb_exists)
	{
		/* Make a thumbnail... */
		$magick = new imagick("{$cphp_config->storage_path}/{$document->sFilename}[0]");
		$magick->cropThumbnailImage(180, 261);
		$magick->setImageFormat("png");
		$magick->writeImage("static/thumbs/{$document->sSlugId}.png");
	}
}
