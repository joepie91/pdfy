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

/* This prevents file corruption if something unexpected happens... */
@error_reporting(0);

try
{
	$document = Document::CreateFromQuery("SELECT * FROM documents WHERE `SlugId` = :SlugId", array("SlugId" => $router->uParameters[1]), 60, true);
}
catch (NotFoundException $e)
{
	http_status_code(404);
	die("404 Not Found");
}

$block_size = (1024 * 1000); /* 1MB block size */

$source_file = "{$cphp_config->storage_path}/$document->sFilename";
$filesize = filesize($source_file);

/* Range request processing, based on https://stackoverflow.com/a/4451376/1332715 (with modifications) */
$range = false;

if(isset($_SERVER['HTTP_RANGE']))
{
	$range = $_SERVER['HTTP_RANGE'];
}
elseif(function_exists("apache_request_headers") && $apache_headers = apache_request_headers())
{
	/* This applies to some Apache servers */
	$headers = array();
	
	foreach($apache_headers as $header => $val)
	{
		$headers[strtolower($header)] = $val;
	}
	
	if(isset($headers['range']))
	{
		$range = $headers['range'];
	}
}

if($range !== false)
{
	/* Process range data provided */
	list($param,$range) = explode('=',$range);
	
	if(strtolower(trim($param)) != 'bytes')
	{
		/* Range must be provided in bytes */
		http_status_code(400);
		die();
	}
	
	$range = explode(',',$range);
	$range = explode('-',$range[0]); // We only deal with the first requested range
	
	if(count($range) != 2)
	{
		http_status_code(400);
		die();
	}
	
	if($range[0] === "")
	{
		$range_end = $filesize - 1;
		$range_start = $range_end - (int) $range[0];
	}
	elseif($range[1] === "")
	{
		$range_start = (int) $range[0];
		$range_end = $filesize - 1;
	}
	else
	{
		$range_start = (int) $range[0];
		$range_end = (int) $range[1];
		
		if ($range_end >= $filesize || (!$range_start && (!$range_end || $range_end == ($filesize - 1))))
		{
			$range = false;
		}
	}
	
	$range_length = $range_end - $range_start + 1;
	
	http_status_code(206);
	header("Content-Range: bytes $range_start-$range_end/$filesize");
}
/* End range request processing */

header("Content-Type: application/pdf");
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"{$document->uOriginalFilename}\""); 
header('Accept-Ranges: bytes');

if($range === false)
{
	header("Content-Length: {$filesize}");
	$offset = 0;
	readfile($source_file);
}
else
{
	$handle = fopen($source_file, "rb");

	$block = "";
	$length = $block_size;
	$block_count = ceil($filesize / $block_size);
	
	header("Content-Length: {$range_length}");
	
	$offset  = $range_start;
	
	if($range_length < $block_size)
	{
		$length = $range_length;
	}
	
	for($i = 0; $i < $block_count; $i++)
	{
		$block = stream_get_contents($handle, $length, $offset);
		echo($block);
		
		$next_offset = $offset + $length;
		
		if($next_offset > $range_end)
		{
			/* We're done here. */
			break;
		}
		
		if($next_offset + $length > $range_end)
		{
			/*The next block contains the range end. We don't want to serve beyond
			 * that point, so we change the length to end at the range end. */
			$length = $range_end - $next_offset + 1;
		}
		
		$offset = $next_offset;
		
		/* Ensure that script execution doesn't time out. */
		set_time_limit(0);
	}

	fclose($handle);
}

die();
