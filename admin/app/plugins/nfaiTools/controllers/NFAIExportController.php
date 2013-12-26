<?php

require_once(__CA_APP_DIR__."/plugins/nfaiTools/NFAIExporter.php");
require_once(__CA_LIB_DIR__."/core/Db/Transaction.php");
require_once(__CA_LIB_DIR__."/ca/Search/ObjectSearch.php");

# -------------------------------------------------------
class NFAIExportController extends ActionController {
	private $qr_top_level_objects;
	private $opn_collection_type_id;
	# -------------------------------------------------------
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 		parent::__construct($po_request, $po_response, $pa_view_paths);

 		$vo_db = new Db();

		$t_list = new ca_lists();

 		$this->opn_collection_type_id = $t_list->getItemIDFromList('object_types','collection');
 		$this->qr_top_level_objects = $vo_db->query('SELECT object_id,idno FROM ca_objects WHERE parent_id IS NULL ORDER BY idno_sort');
 	}
 	# -------------------------------------------------------
 	public function Index() {
 		JavascriptLoadManager::register('tableList');

 		$this->view->setVar('num_parents',$this->qr_top_level_objects->numRows());

 		if($vs_search = $this->request->getParameter('nfaiSearch', pString)){
 			$vo_search = new ObjectSearch();
 			$vo_search->addResultFilter('ca_objects.type_id','=',$this->opn_collection_type_id);
 			$vo_result = $vo_search->search($vs_search);
 			$this->view->setVar('result',$vo_result);
 			$this->view->setVar('search',$vs_search);
 		}

 		$this->render('export_setup_html.php');
 	}
 	# -------------------------------------------------------
 	public function Export() {
 		$vn_rand = rand(10000,99999);
 		$vs_dir = __CA_APP_DIR__.'/tmp/nfai_export_'.$vn_rand;

 		@mkdir($vs_dir);

 		$this->qr_top_level_objects->seek(0);
 		while($this->qr_top_level_objects->nextRow()){
 			$vo_export = new NFAIExporter($this->qr_top_level_objects->get('object_id'));
 			$vs_export = $vo_export->export();

 			$vs_filename = str_replace('.', '_', $this->qr_top_level_objects->get('idno')).'.xml';
 			file_put_contents($vs_dir."/".$vs_filename, $vs_export);
 		}

 		caZipDirectory($vs_dir,'',$vs_dir.'/nfai.zip');

 		$this->view->setVar('filename',$vs_dir.'/nfai.zip');
 		$this->view->setVar('directory',$vs_dir);

 		$this->render('export_do_export_html.php');
 	}
 	# -------------------------------------------------------
 	public function Download() {
 		if($vn_root_id = $this->request->getParameter('root_id', pInteger)){
 			$t_object = new ca_objects($vn_root_id);
 			$vo_export = new NFAIExporter($vn_root_id);
 			$vs_export = $vo_export->export();
 			$vs_filename = str_replace('.', '_', $t_object->get('idno')).'.xml';

 			$this->view->setVar('export',$vs_export);
 			$this->view->setVar('filename',$vs_filename);

 			return $this->render('export_download_xml.php');
 		}
 	}
 	# -------------------------------------------------------
}

?>
