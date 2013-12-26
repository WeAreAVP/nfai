<h1>NFAI EAD importer</h1>


<?php

if($va_errors = $this->getVar("errors")){
	print "<h1>There were errors while processing the uploaded file: <br /></h1>";
	print join("<br />",$va_errors);
	return;
}

$vs_file_name = $this->getVar("file_name");

?>

<h2>Processing <?php print $vs_file_name; ?> finished.</h2>

<?php
	if($vs_importer_output = $this->getVar("importer_output")){
?>
	<h2>There were errors while processing the uploaded file. Raw importer output is:</h2>
	<div style="border:2px solid grey; padding: 10px 10px 10px 10px;">
		<?php print nl2br($vs_importer_output); ?>
	</div>
<?php
	}
?>


