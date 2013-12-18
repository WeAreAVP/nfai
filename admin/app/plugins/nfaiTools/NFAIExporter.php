<?php
// ----------------------------------------------------------------------
require_once(__CA_LIB_DIR__.'/core/Db.php');
require_once(__CA_MODELS_DIR__.'/ca_objects.php');
require_once(__CA_MODELS_DIR__.'/ca_occurrences.php');
require_once(__CA_MODELS_DIR__.'/ca_entities.php');
require_once(__CA_MODELS_DIR__.'/ca_relationship_types.php');
require_once(__CA_APP_DIR__.'/helpers/utilityHelpers.php');
// ----------------------------------------------------------------------
class NFAIExporter {
	// ----------------------------------------------------------------------
	protected $opn_root_id;
	protected $t_object;
	protected $opo_dom;
	protected $opo_root;
	protected $opa_containers = array(
		'archdesc' => 'c01',
		'c01' => 'c02',
		'c02' => 'c03',
		'c03' => 'c04',
		'c04' => 'c05',
		'c05' => 'c06',
		'c06' => 'c07',
		'c07' => 'c08',
		'c08' => 'c09',
	);

	protected $opn_object_entity_subject_rel;
	// ----------------------------------------------------------------------
	public function __construct($pn_root_id){
		$this->opn_root_id = $pn_root_id;
		$this->t_object = new ca_objects($pn_root_id);

		$this->opo_dom = new DOMDocument('1.0', 'utf-8');
		//$this->opo_dom->preserveWhiteSpace = false;
		$this->opo_dom->formatOutput = true;
		$this->opo_root = $this->opo_dom->createElement('ead');
		$this->opo_root->setAttribute('xmlns','urn:isbn:1-931666-22-9');
		$this->opo_root->setAttribute('xmlns:xlink','http://www.w3.org/1999/xlink');
		$this->opo_root->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$this->opo_root->setAttribute('xsi:schemaLocation','urn:isbn:1-931666-22-9 http://www.loc.gov/ead/ead.xsd');
		$this->opo_dom->appendChild($this->opo_root);

		$t_rel_types = new ca_relationship_types();
		$this->opn_object_entity_subject_rel = $t_rel_types->getRelationshipTypeID('ca_objects_x_entities','subject');
	}
	// ----------------------------------------------------------------------
	public function export(){
		// EAD header
		if($vo_header = $this->getEADHeader()){
			$this->opo_root->appendChild($vo_header);
		}

		// archival description
		$vo_archdesc = $this->opo_dom->createElement('archdesc');
		$this->opo_root->appendChild($vo_archdesc);

		$this->appendItemContent($vo_archdesc,$this->t_object);

		return $this->opo_dom->saveXML();
	}
	// ----------------------------------------------------------------------
	private function getEADHeader(){
		$t_object = $this->t_object;
		$vo_header = $this->opo_dom->createElement('eadheader');

		// idno
		$vo_eadid = $this->opo_dom->createElement('eadid',caEscapeForXML($t_object->get('idno')));
		$vo_eadid->setAttribute('countrycode','US');
		$vo_header->appendChild($vo_eadid);	

		// title/label
		$vo_filedesc = $this->opo_dom->createElement('filedesc');
		$vo_header->appendChild($vo_filedesc);
		$vo_titlesmt = $this->opo_dom->createElement('titlestmt');
		$vo_filedesc->appendChild($vo_titlesmt);
		$vo_titleproper = $this->opo_dom->createElement('titleproper',caEscapeForXML($t_object->getLabelForDisplay()));
		$vo_titlesmt->appendChild($vo_titleproper);

		// date
		if($vs_date = $t_object->get('ca_objects.date.dates_value')){
			$vo_date = $this->opo_dom->createElement('date',caEscapeForXML($vs_date));
			$vo_titleproper->appendChild($vo_date);
		}

		// user
		$va_log = $t_object->getChangeLog($this->opn_root_id,array('changeType' => 'I'));
		$va_log_entry = array_pop($va_log);

		$vo_author = $this->opo_dom->createElement('author',caEscapeForXML($va_log_entry['fname']." ".$va_log_entry['lname']));
		$vo_titlesmt->appendChild($vo_author);

		// publisher (repository)
		$vo_publicationstmt = $this->opo_dom->createElement('publicationstmt');
		$vo_filedesc->appendChild($vo_publicationstmt);
		$vo_publisher = $this->opo_dom->createElement('publisher',$t_object->get('ca_occurrences'));
		$vo_publicationstmt->appendChild($vo_publisher);

		// creation date from log
		$vo_date_pub = $this->opo_dom->createElement('date',date('c',$va_log_entry['log_datetime']));
		$vo_publicationstmt->appendChild($vo_date_pub);

		// address, split into lines
		$va_tmp = $t_object->get('ca_occurrences.address',array('returnAsArray' => true));
		$va_address = array_pop($va_tmp);
		if(is_array($va_address)){
			$vo_address = $this->opo_dom->createElement('address');
			$vo_publicationstmt->appendChild($vo_address);

			if(strlen($va_address['address1'])){
				$vo_address->appendChild($this->opo_dom->createElement('addressline',caEscapeForXML($va_address['address1'])));
			}
			if(strlen($va_address['address2'])){
				$vo_address->appendChild($this->opo_dom->createElement('addressline',caEscapeForXML($va_address['address2'])));
			}

			$va_line3 = array();
			if(strlen($va_address['city'])) $va_line3[] = $va_address['city'];
			if(strlen($va_address['stateprovince'])) $va_line3[] = $va_address['stateprovince'];
			if(strlen($va_address['country'])) $va_line3[] = $va_address['country'];
			if(strlen($va_address['postalcode'])) $va_line3[] = $va_address['postalcode'];
			
			$vo_address->appendChild($this->opo_dom->createElement('addressline',caEscapeForXML(join(', ',$va_line3))));
		}

		return $vo_header;
	}
	// ----------------------------------------------------------------------
	private function appendItemContent($po_element,$t_item){
		$t_list = new ca_lists();
		$t_list_item = new ca_list_items();

		// level attribute
		$po_element->setAttribute('level',$this->getLevelFromObjectType($t_item));

		// did
		$vo_did = $this->opo_dom->createElement('did');
		$po_element->appendChild($vo_did);

		// did/unitid
		if(!($vs_idno = $t_item->get('ca_objects.alternate_idnos.id_value',array('delimiter' => '; ')))){
			$vs_idno = $t_item->get('idno');
		}
		$vo_did->appendChild($this->opo_dom->createElement('unitid',caEscapeForXML($vs_idno)));

		// repository
		if($vs_repository = $t_item->get('ca_occurrences',array('delimiter' => ', '))){
			$vo_repository = $this->opo_dom->createElement('repository');
			$vo_did->appendChild($vo_repository);

			$vo_repository->appendChild($this->opo_dom->createElement('corpname',caEscapeForXML($vs_repository)));

			// address, split into lines
			$va_tmp = $t_item->get('ca_occurrences.address',array('returnAsArray' => true));
			$va_address = array_pop($va_tmp);
			if(is_array($va_address)){
				$vo_address = $this->opo_dom->createElement('address');
				$vo_repository->appendChild($vo_address);

				if(strlen($va_address['address1'])){
					$vo_address->appendChild($this->opo_dom->createElement('addressline',caEscapeForXML($va_address['address1'])));
				}
				if(strlen($va_address['address2'])){
					$vo_address->appendChild($this->opo_dom->createElement('addressline',caEscapeForXML($va_address['address2'])));
				}

				$va_line3 = array();
				if(strlen($va_address['city'])) $va_line3[] = $va_address['city'];
				if(strlen($va_address['stateprovince'])) $va_line3[] = $va_address['stateprovince'];
				if(strlen($va_address['country'])) $va_line3[] = $va_address['country'];
				if(strlen($va_address['postalcode'])) $va_line3[] = $va_address['postalcode'];
				
				$vo_address->appendChild($this->opo_dom->createElement('addressline',caEscapeForXML(join(', ',$va_line3))));
			}

		}

		// origination (entities)
		$va_entities = $t_item->get('ca_entities',array('returnAsArray' => true));
		foreach($va_entities as $va_entity){
			if($va_entity['relationship_type_id'] == $this->opn_object_entity_subject_rel){
				continue;
			}

			$vo_origination = $this->opo_dom->createElement('origination');
			$vo_did->appendChild($vo_origination);
			$vo_name = null;

			switch($va_entity['item_type_id']){
				case $t_list->getItemIDFromList('entity_types', 'ind'):
					$vo_name = $this->opo_dom->createElement('persname',caEscapeForXML($va_entity['displayname']));
					$vo_origination->appendChild($vo_name);
					break;
				case $t_list->getItemIDFromList('entity_types', 'org'):
					$vo_name = $this->opo_dom->createElement('corpname',caEscapeForXML($va_entity['displayname']));
					$vo_origination->appendChild($vo_name);
					break;
				default:
					break;
			}
			if($vo_name){
				$vo_name->setAttribute('role',$va_entity['relationship_typename']);
			}
		}

		// label (unittitle)
		if($vs_label = $t_item->getLabelForDisplay()){
			$vo_did->appendChild($this->opo_dom->createElement('unittitle',caEscapeForXML($vs_label)));
		}

		// date
		if($vs_date = $t_item->get('ca_objects.date.dates_value')){
			$vo_unitdate = $this->opo_dom->createElement('unitdate',caEscapeForXML($vs_date));
			$vo_did->appendChild($vo_unitdate);
		}

		// physdesc
		$vo_physdesc = $this->opo_dom->createElement('physdesc');
		$vo_did->appendChild($vo_physdesc);

		// extent
		$va_extent = $t_item->get('ca_objects.extent_nfai',array('returnAsArray' => true));
		if(is_array($va_extent)){
			foreach($va_extent as $va_ext){
				$vs_ext = $va_ext['extent_number']." ".$va_ext['extent_type'];
				if(strlen(trim($vs_ext))){
					$vo_extent = $this->opo_dom->createElement('extent',caEscapeForXML($vs_ext));
					$vo_physdesc->appendChild($vo_extent);
				}
			}
		}
		
		// dimensions
		$va_dimensions = $t_item->get('ca_objects.dimensions',array('returnAsArray' => true));
		if(is_array($va_dimensions)){
			foreach($va_dimensions as $va_dim){
				$vs_dim = $t_item->get('ca_objects.dimensions',array('convertCodesToDisplayText' => true));
				if(strlen(trim($vs_dim))){
					$vo_dimensions = $this->opo_dom->createElement('dimensions', caEscapeForXML($vs_dim));
					$vo_physdesc->appendChild($vo_dimensions);
				}
			}
		}

		// summary
		$va_summary = $t_item->get('ca_objects.summary',array('returnAsArray' => true));
		if(is_array($va_summary)){
			foreach($va_summary as $va_sum){
				if(strlen(trim($va_sum['summary']))){
					$vo_abstract = $this->opo_dom->createElement('abstract');
					$vo_cdata = $this->opo_dom->createCDATASection($va_sum['summary']);
					$vo_abstract->appendChild($vo_cdata);
					$vo_did->appendChild($vo_abstract);
				}
			}
		}

		// languages
		if($va_lang = $t_item->get('ca_objects.language',array('returnAsArray' => true))){
			$vo_langmaterial = $this->opo_dom->createElement('langmaterial');
			$vo_did->appendChild($vo_langmaterial);
			foreach($va_lang as $va_l){
				if(strlen(trim($va_l['language_text']))==0) continue;
				$vo_language = $this->opo_dom->createElement('language',caEscapeForXML($va_l['language_text']));
				$vo_langmaterial->appendChild($vo_language);
			}
		}

		// different note types
		$va_notes = $t_item->get('ca_objects.dacs_element_note',array('returnAsArray' => true));
		if(is_array($va_notes)){
			foreach($va_notes as $va_note){
				if(strlen(trim($va_note['Dacs_Detail']))>0){
					$vo_element = $this->getDacsElementNoteElement($va_note['aacs_dropdown'],$va_note['Dacs_Detail']);
					if($vo_element){
						$po_element->appendChild($vo_element);
					}
				}
			}
		}

		// preferred citation
		if($vs_cite = $t_item->get('ca_objects.preferred_citation')){
			$vo_prefercite = $this->opo_dom->createElement('prefercite');
			$vo_prefercite->appendChild($this->opo_dom->createElement('p',caEscapeForXML($vs_cite)));
			$po_element->appendChild($vo_prefercite);
		}

		// controlaccess
		$vo_controlaccess = $this->opo_dom->createElement('controlaccess');
		$vo_name = null;

		$va_entities = $t_item->get('ca_entities',array('returnAsArray' => true));
		foreach($va_entities as $va_entity){
			switch($va_entity['item_type_id']){
				case $t_list->getItemIDFromList('entity_types', 'ind'):
					$vo_name = $this->opo_dom->createElement('persname',caEscapeForXML($va_entity['displayname']));
					$vo_controlaccess->appendChild($vo_name);
					break;
				case $t_list->getItemIDFromList('entity_types', 'org'):
					$vo_name = $this->opo_dom->createElement('corpname',caEscapeForXML($va_entity['displayname']));
					$vo_controlaccess->appendChild($vo_name);
					break;
				default:
					break;
			}

			if($vo_name){
				$vo_name->setAttribute('role',$va_entity['relationship_typename']);
			}
		}

		if($vs_georef = $t_item->get('ca_objects.georeference')){
			$vo_controlaccess->appendChild($this->opo_dom->createElement('geogname',caEscapeForXML($vs_georef)));
		}

		if($va_subjects = $t_item->get('ca_objects.subject',array('returnAsArray' => true))){
			foreach($va_subjects as $va_subject){
				if(strlen(trim($va_subject['subject_text']))==0) continue;
				
				if($t_list_item->load($va_subject['subject_source'])){
					$vo_subject = $this->opo_dom->createElement('subject', caEscapeForXML($va_subject['subject_text']));
					$vo_subject->setAttribute('source',$t_list_item->get('idno'));
					$vo_controlaccess->appendChild($vo_subject);
				}
			}
		}
		// controlaccess can't be empty
		if($vo_controlaccess->childNodes->length > 0){
			$po_element->appendChild($vo_controlaccess);
		}

		// direct hierarchy children
		if(is_array($va_children = $t_item->getHierarchyChildren())){
			if($po_element->tagName=='archdesc'){
				$vo_dsc = $this->opo_dom->createElement('dsc');
				$po_element->appendChild($vo_dsc);
			}
			foreach($va_children as $va_child){
				$vo_container = $this->opo_dom->createElement($this->opa_containers[$po_element->tagName]);
				if($po_element->tagName=='archdesc') {
					$vo_dsc->appendChild($vo_container);
				} else {
					$po_element->appendChild($vo_container);
				}

				$this->appendItemContent($vo_container,new ca_objects($va_child['object_id']));
			}
		}
	}
	// ----------------------------------------------------------------------
	private function getDacsElementNoteElement($pn_type,$ps_note){
		$t_list = new ca_lists();
		$vs_element_name = null;

		switch($pn_type){
			case $t_list->getItemIDFromList('dacs_types','existence_and_location_of_originals'):
				$vs_element_name = 'originalsloc';
				break;
			case $t_list->getItemIDFromList('dacs_types','admin_bio'):
				$vs_element_name = 'bioghist';
				break;
			case $t_list->getItemIDFromList('dacs_types','scopecontent'):
				$vs_element_name = 'scopecontent';
				break;
			case $t_list->getItemIDFromList('dacs_types','system_of_arrangement'):
				$vs_element_name = 'arrangement';
				break;
			case $t_list->getItemIDFromList('dacs_types','conditions_governing_access'):
				$vs_element_name = 'accessrestrict';
				break;
			case $t_list->getItemIDFromList('dacs_types','custodial_history'):
				$vs_element_name = 'custodhist';
				break;
			case $t_list->getItemIDFromList('dacs_types','source_of_acquisition'):
				$vs_element_name = 'acqinfo';
				break;
			case $t_list->getItemIDFromList('dacs_types','general_note'):
				$vs_element_name = 'processinfo';
				break;
			case $t_list->getItemIDFromList('dacs_types','appraisal'):
				$vs_element_name = 'appraisal';
				break;
			default:
				break;
		}
		if($vs_element_name){
			$vo_element = $this->opo_dom->createElement($vs_element_name);
			$vo_p = $this->opo_dom->createElement('p');
			$vo_element->appendChild($vo_p);
			$vo_cdata = $this->opo_dom->createCDATASection($ps_note);
			$vo_p->appendChild($vo_cdata);
			return $vo_element;
		} else {
			return false;
		}
	}
	// ----------------------------------------------------------------------
	private function getLevelFromObjectType($t_object){
		$t_list = new ca_lists();
		switch($t_object->get('type_id')){
			case $t_list->getItemIDFromList('object_types', 'collection'):
				return 'collection';
			case $t_list->getItemIDFromList('object_types', 'series'):
				return 'series';
			case $t_list->getItemIDFromList('object_types', 'subseries'):
				return 'subseries';
			case $t_list->getItemIDFromList('object_types', 'box'):
			case $t_list->getItemIDFromList('object_types', 'folder'):
				return 'otherlevel';
			case $t_list->getItemIDFromList('object_types', 'item'):
				return 'item';
		}
	}
	// ----------------------------------------------------------------------
}