<?php

$vs_export = $this->getVar('export');
$vs_filename = $this->getVar('filename');

header('Content-Type: text/xml; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$vs_filename.'"');
header('Content-Transfer-Encoding: binary');

print $vs_export;
exit();