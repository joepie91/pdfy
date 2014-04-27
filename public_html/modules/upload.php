<?php
/*
 * PDFy is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if(!isset($_APP)) { die("Unauthorized."); }

if($router->uMethod == "post")
{
	if(empty($_FILES["file"]))
	{
		die("error/99");
	}
	
	if(empty($_POST["visibility"]))
	{
		die("error/97");
	}
	
	$file = $_FILES["file"];
	
	if($file["size"] > (50 * 1024 * 1024) ||  $file["error"] !== 0)
	{
		die("error/{$file['error']}"); /* Intentionally short-circuit. */
	}
	
	$fhandle = fopen($file["tmp_name"], "r");
	$first_bytes = fread($fhandle, 4);
	fclose($fhandle);
	
	if($first_bytes !== "%PDF")
	{
		die("error/98");
	}
	
	$slug = random_string(16);
	$new_filename = random_string(16);
	
	/* Make a thumbnail... */
	$magick = new imagick("{$file['tmp_name']}[0]");
	$magick->cropThumbnailImage(180, 261);
	$magick->setImageFormat("png");
	$magick->writeImage("static/thumbs/{$slug}.png");
	
	move_uploaded_file($file["tmp_name"], "{$cphp_config->storage_path}/{$new_filename}");
	
	$document = new Document();
	$document->uIsPublic = ($_POST["visibility"] === "public");
	$document->uFilename = $new_filename;
	$document->uOriginalFilename = $file["name"];
	$document->uSlugId = $slug;
	$document->uDeleteKey = random_string(32);
	$document->uUploadDate = time();
	$document->InsertIntoDatabase();
	
	die("document/{$slug}");
}
