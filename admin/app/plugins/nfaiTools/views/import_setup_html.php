<?php
	print caFormControlBox(
		caFormSubmitButton($this->request, __CA_NAV_BUTTON_SAVE__, _t("Start import"), 'nfaiImporterForm').' '.
		caNavButton($this->request, __CA_NAV_BUTTON_CANCEL__, _t("Cancel"), $this->request->getModulePath(), $this->request->getController(), 'Index', array()), 
		'', 
		''
	);

	print caFormTag($this->request, 'Import', 'nfaiImporterForm', null, 'POST', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));												

?>
	<h1>NFAI EAD importer</h1>
	<div class="formLabel">
		Select a file to import ...<br /><br />
		<input name="nfai_ead" type="file">
	</div>

</form>