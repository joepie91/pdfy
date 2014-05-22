#!/usr/bin/env python

import json, internetarchive, oursql, os
from requests.exceptions import ConnectionError


###

import logging
import sys
log = logging.getLogger()
out_hdlr = logging.StreamHandler(sys.stdout)
out_hdlr.setFormatter(logging.Formatter('%(asctime)s %(message)s'))
out_hdlr.setLevel(logging.DEBUG)
log.addHandler(out_hdlr)
log.setLevel(logging.DEBUG)

###



template = """
<p>
	<strong>This public document was automatically mirrored from <a href="http://pdf.yt/">PDFy</a>.</strong>
</p>

<ul>
	<li><strong>Original filename:</strong> %(real_filename)s</li>
	<li><strong>URL:</strong> <a href="http://pdf.yt/d/%(slug)s">http://pdf.yt/d/%(slug)s</a></li>
	<li><strong>Upload date:</strong> %(upload_date)s</li>
</ul>
""".replace("\n", "")

with open("config.json", "r") as f:
	conf = json.loads(f.read())
	
dbconn = oursql.Connection(host=conf["database"]["hostname"], user=conf["database"]["username"], passwd=conf["database"]["password"], db=conf["database"]["database"], autoreconnect=True)
cur = dbconn.cursor()

cur.execute("SELECT `Id`, `SlugId`, `Filename`, `Uploaded`, `OriginalFilename` FROM documents WHERE `Mirrored` = 0 AND `Public` = 1")
items = cur.fetchall()

for doc in items:
	id_, slug, storage_filename, upload_date, real_filename = doc
	
	if upload_date is None:
		upload_date = "Before April 27, 2014"
	else:
		upload_date = upload_date.strftime("%B %d, %Y %H:%M:%S")
	
	source_file = "storage/%s" % storage_filename
	
	item = internetarchive.get_item("pdfy-%s" % slug)
	
	metadata = {
		"mediatype": "texts",
		"subject": ["mirror"],
		"collection": "test_collection",
		"title": "%s (PDFy mirror)" % real_filename,
		"description": template % {
			"real_filename": real_filename,
			"slug": slug,
			"upload_date": upload_date
		},
		"date": "2014-01-01"
	}
	
	if item.upload([(real_filename, source_file)], metadata=metadata, access_key=conf["internetarchive"]["accesskey"], secret_key=conf["internetarchive"]["secretkey"]):
		cur = dbconn.cursor()
		cur.execute("UPDATE documents SET `Mirrored` = 1 WHERE `Id` = ?", (id_,))

		print "Uploaded %s (%s)" % (slug, real_filename)
	else:
		print "FAILED upload of %s (%s)!" % (slug, title)