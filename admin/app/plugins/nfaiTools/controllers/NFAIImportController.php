<?php

require_once(__CA_APP_DIR__."/plugins/nfaiTools/NFAIImporter.php");
require_once(__CA_LIB_DIR__."/core/Db/Transaction.php");

# -------------------------------------------------------
class NFAIImportController extends ActionController {
	# -------------------------------------------------------
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 		parent::__construct($po_request, $po_response, $pa_view_paths);
 	}
 	# -------------------------------------------------------
 	public function Index() {
 		$this->render('import_setup_html.php');
 	}
 	# -------------------------------------------------------
 	public function Import() {
 		$va_file = $_FILES["nfai_ead"];
 		$this->view->setVar("file_name",$va_file['name']);

 		if(is_array($va_file) && sizeof($va_file)>0){
	 		$o_tx = new Transaction();
	 		$o_tx->start();
	 		$o_importer = new NFAIImporter($o_tx);

	 		ob_start();
	 		$o_importer->import($va_file["tmp_name"]);
	 		$vs_importer_output = ob_get_clean();

	 		if(strlen($vs_importer_output)){
	 			$o_tx->rollback();
	 			$this->view->setVar("importer_output",$vs_importer_output);
	 		} else {
	 			$o_tx->commit();
	 		}
 		} else {
 			$this->view->setVar("errors",array("There is no file to process"));
 		}

 		$this->render('import_do_import_html.php');
 	}
 	# -------------------------------------------------------
}

?>
