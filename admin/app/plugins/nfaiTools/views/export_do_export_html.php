<?php

$vs_file = $this->getVar('filename');

// http headers for zip downloads
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"nfai.zip\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($vs_file));
ob_end_flush();
@readfile($vs_file);

// cleanup after download
caRemoveDirectory($this->getVar('directory'));
