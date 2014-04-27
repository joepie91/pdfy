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

class Document extends CPHPDatabaseRecordClass
{
	public $table_name = "documents";
	public $fill_query = "SELECT * FROM documents WHERE `Id` = :Id";
	public $verify_query = "SELECT * FROM documents WHERE `Id` = :Id";

	public $prototype = array(
		'string' => array(
			"SlugId" => "SlugId",
			"Filename" => "Filename",
			"OriginalFilename" => "OriginalFilename",
			"DeleteKey" => "DeleteKey"
		),
		"numeric" => array(
			"Views" => "Views"
		),
		"boolean" => array(
			"IsPublic" => "Public"
		),
		"timestamp" => array(
			"UploadDate" => "Uploaded"
		)
	);

	public function Delete($ip, $key)
	{
		/* Do things */
	}
}
