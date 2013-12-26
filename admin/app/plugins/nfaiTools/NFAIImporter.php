<?php 

require_once(__CA_LIB_DIR__.'/core/Db.php');
require_once(__CA_LIB_DIR__.'/core/Parsers/TimeExpressionParser.php');
require_once(__CA_LIB_DIR__.'/ca/Utils/DataMigrationUtils.php');
// ----------------------------------------------------------------------
require_once(__CA_MODELS_DIR__.'/ca_objects.php');
require_once(__CA_MODELS_DIR__.'/ca_locales.php');
require_once(__CA_MODELS_DIR__.'/ca_occurrences.php');
require_once(__CA_MODELS_DIR__.'/ca_entities.php');
require_once(__CA_MODELS_DIR__.'/ca_relationship_types.php');
// ----------------------------------------------------------------------
define("__ENTITY_MODE_CORP__",1);
define("__ENTITY_MODE_PERSON__",2);
// ----------------------------------------------------------------------
class NFAIImporter {
	// ----------------------------------------------------------------------
	protected $opn_locale_id;
	protected $opa_c;
	protected $o_tx; // Transaction object
	// ----------------------------------------------------------------------
	public function __construct($po_tx){
		$t_locale = new ca_locales();
		$this->opn_locale_id = $t_locale->loadLocaleByCode('en_US');

		$this->o_tx = $po_tx;
		$this->opa_c = array("c","c01","c02","c03","c04","c05","c06","c07","c08","c09","c10","c11","c12");
	}
	// ----------------------------------------------------------------------
	public function import($ps_filename){
		if(!is_file($ps_filename)){
			print $ps_filename. "IS NO FILE\n"; return;
		}
		$o_xml = simplexml_load_file($ps_filename);

		$vn_object_id = $this->processItem($o_xml->archdesc, null);
		$t_object = new ca_objects();
		$t_object->setTransaction($this->o_tx);
		$t_object->load($vn_object_id);

		// create and/or relate repository
		$vs_repo = (string) $o_xml->archdesc->did->repository->corpname;
		$vs_subarea = (string) $o_xml->archdesc->did->repository->corpname->subarea;
		$vo_address = $o_xml->archdesc->did->repository->address->addressline;
		if(strlen($vs_repo)>0){
			$va_values = array(); $i=0;
			if($vo_address){
				foreach($vo_address as $vo_line){
					$i++;
					$vs_line = trim((string) $vo_line);
					if(preg_match("/^[0-9\(\)\-\.\s]+$/",$vs_line)){ // looks like a phone number, no?
						$va_values["telephone"] = $vs_line;
						continue;
					}
					if(preg_match("/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/",$vs_line)){ // email
						$va_values["email"] = $vs_line;
						continue;
					}

					if(preg_match("/^(http|https)\:\/\/([a-z0-9-]+\.){0,}[a-z0-9-]+\.[a-z]{2,4}/", $vs_line)){ // url
						$va_values["repository_url"] = $vs_line;
						continue;
					}
					if($i<=2){
						$va_values["address"]["address".$i] = trim((string) $vo_line);
					}
				}
			}
			if(strlen($vs_subarea)>0){
				$vs_repo = $this->clean_string($vs_repo." / ".$vs_subarea);
			}

			$t_occ = new ca_occurrences();
			$vs_occ_idno = "NFAI.R.00000000";
			$o_numbering_plugin = $t_occ->getIDNoPlugInInstance();
			if (!($vs_sep = $o_numbering_plugin->getSeparator())) { $vs_sep = '.'; }
			if (!is_array($va_idno_values = $o_numbering_plugin->htmlFormValuesAsArray('idno', $vs_occ_idno, false, false, true))) { 
				$va_idno_values = array(); 
			}
			$va_values["idno"] = join($vs_sep, $va_idno_values);

			$vn_repository_id = DataMigrationUtils::getOccurrenceID($vs_repo,null,"repository",$this->opn_locale_id,$va_values,array("transaction" => $this->o_tx));
			if($t_object->getPrimaryKey()>0){
				$t_object->addRelationship("ca_occurrences",$vn_repository_id,"repository");
			}
			DataMigrationUtils::postError($t_object,"While relating archive and repository");
		}

		foreach($this->opa_c as $vs_c){
			$this->processItem($o_xml->archdesc->dsc->{$vs_c},$t_object->getPrimaryKey());
		}
	}
	// ----------------------------------------------------------------------
	// content handlers
	// ----------------------------------------------------------------------
	protected function processItem($o_series, $vn_parent_id) {
		if(!$o_series) return;
		$o_tep = new TimeExpressionParser();

		foreach($o_series as $o_node) {
			$t_object = new ca_objects();
			$t_object->setTransaction($this->o_tx);
			$t_object->setMode(ACCESS_WRITE);

			$vs_object_idno = "NFAI.O.00000000";
			$o_numbering_plugin = $t_object->getIDNoPlugInInstance();
			if (!($vs_sep = $o_numbering_plugin->getSeparator())) { $vs_sep = '.'; }
			if (!is_array($va_idno_values = $o_numbering_plugin->htmlFormValuesAsArray('idno', $vs_object_idno, false, false, true))) { 
				$va_idno_values = array(); 
			}
			$t_object->set('idno', join($vs_sep, $va_idno_values));

			$o_attributes = $o_node->attributes();
			switch((string)$o_attributes->level) {
				case 'series':
					$t_object->set("type_id","series");
					break;
				case 'subseries':
				case 'subfonds':
				case 'otherlevel':
					$t_object->set("type_id","subseries");
					break;
				case 'collection':
				case 'fonds':
				case 'class':
				case 'recordgrp':
					$t_object->set("type_id","collection");
					break;
				case 'file':
				case 'item':
					$t_object->set("type_id","item");
				default:
					print "WARNING: Couldn't figure out type using 'level' attribute, falling back on 'item'\n";
					$t_object->set("type_id","item");
					break;
			}

			if(!$t_object->get("type_id")){
				continue;
			}
					
			$t_object->set('parent_id', $vn_parent_id);
			$t_object->set('access', 1);
			$t_object->set('status', 0);

			if($vs_unitid = (string) $o_node->did->unitid){
				$t_object->addAttribute(array(
					'id_value' => trim($vs_unitid),
					'id_source' => "local",
				),'alternate_idnos');
			}

			if(($vo_date = $o_node->did->unitdate) || ($vo_date = $o_node->did->unittitle->unitdate)){
				$vs_date = (string) $vo_date;
				$vs_date_type = $this->get_attribute($vo_date,"type");
				if(!in_array($vs_date_type, array("inclusive","bulk"))){
					$vs_date_type = "inclusive";
				}

				if($o_tep->parse($vs_date)){
					$t_object->addAttribute(array(
						'locale_id' => $this->opn_locale_id,
						'dates_value' => trim($vs_date),
						'dc_dates_types' => $vs_date_type,
					),'date');	
				}
			}

			foreach($o_node->did->physdesc as $o_physdesc){
				if($vs_extent = (string) $o_physdesc->extent){
					$va_matches = array();
					if(preg_match("/(ca.)?\ ?([0-9\.\,]+)(.*)/",$vs_extent,$va_matches)){
						$t_object->addAttribute(array(
							'extent_number' => str_replace(",","",trim($va_matches[2])),
							'extent_type' => trim($va_matches[3]).(strlen(trim($va_matches[1]))>0 ? " (".trim($va_matches[1]).")" : ""),
						),'extent_nfai');			
					} else {
						print "ERROR: Couldn't match extent '$vs_extent', check regular expression!\n";
					}
				}

				if($vs_dimensions = (string) $o_physdesc->dimensions){
					$t_object->addAttribute(array(
						'locale_id' => $this->opn_locale_id,
						'dimensions_text' => $vs_dimensions,
					),'dimensions_text');
				}
			}

			if($vs_abstract = (string) $o_node->did->abstract){
				$t_object->addAttribute(array(
					'locale_id' => $this->opn_locale_id,
					'summary' => trim($vs_abstract),
				),'summary');
			}

			if($o_node->did->container){
				$vs_summary = "";
				foreach($o_node->did->container as $o_container){
					$vs_type = $this->get_attribute($o_container,"type");
					$vs_text = trim((string) $o_container);
					if($vs_type || $vs_text){
						$vs_summary .= "Container: ".(strlen($vs_type)>0 ? $vs_type." " : "").$vs_text."<br />\n";
					}
				}
				if(strlen($vs_summary)>0){
					$t_object->addAttribute(array(
						'locale_id' => $this->opn_locale_id,
						'summary' => $vs_summary,
					),'summary');
				}
			}

			if($o_node->did->langmaterial && $o_node->did->langmaterial->language){
				foreach ($o_node->did->langmaterial->language as $vo_language) {
					$vs_language = trim((string) $vo_language);
					if(!strlen($vs_language)>0){
						$vs_language = $this->get_attribute($vo_language,"langcode");
					}
					$t_object->addAttribute(array(
						'locale_id' => $this->opn_locale_id,
						'language_text' => trim($vs_language),
						'language_type' => "language1",
					),'language');
				}
			}

			$this->importParagraphsAsNote($o_node->scopecontent,"scopecontent",$t_object);
			$this->importParagraphsAsNote($o_node->originalsloc,"existence_and_location_of_originals",$t_object);
			$this->importParagraphsAsNote($o_node->bioghist,"admin_bio",$t_object);
			$this->importParagraphsAsNote($o_node->arrangement,"system_of_arrangement",$t_object);
			$this->importParagraphsAsNote($o_node->accessrestrict,"conditions_governing_access",$t_object);
			$this->importParagraphsAsNote($o_node->userestrict,"conditions_governing_access",$t_object);
			$this->importParagraphsAsNote($o_node->custodhist,"custodial_history",$t_object);
			$this->importParagraphsAsNote($o_node->acqinfo,"source_of_acquisition",$t_object);
			//$this->importParagraphsAsNote($o_node->accruals,"accurals",$t_object);
			$this->importParagraphsAsNote($o_node->processinfo,"general_note",$t_object);
			$this->importParagraphsAsNote($o_node->note,"general_note",$t_object);
			$this->importParagraphsAsNote($o_node->appraisal,"appraisal",$t_object);
			$this->importParagraphsAsNote($o_node->relatedmaterial,"related_archival_materials",$t_object);
			$this->importParagraphsAsNote($o_node->bibliography,"bibliography",$t_object);

			$this->importParagraphsAsNote($o_node->descgrp->scopecontent,"scopecontent",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->originalsloc,"existence_and_location_of_originals",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->bioghist,"admin_bio",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->arrangement,"system_of_arrangement",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->accessrestrict,"conditions_governing_access",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->userestrict,"conditions_governing_access",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->custodhist,"custodial_history",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->acqinfo,"source_of_acquisition",$t_object);
			//$this->importParagraphsAsNote($o_node->descgrp->accruals,"accurals",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->processinfo,"general_note",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->note,"general_note",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->appraisal,"appraisal",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->relatedmaterial,"related_archival_materials",$t_object);
			$this->importParagraphsAsNote($o_node->descgrp->bibliography,"bibliography",$t_object);

			if($o_node->prefercite->p){
				foreach($o_node->prefercite->p as $o_p){
					$vs_citation = $this->clean_string(dom_import_simplexml($o_p)->textContent);
					$t_object->addAttribute(array(
						'locale_id' => $this->opn_locale_id,
						'preferred_citation' => $vs_citation,
					),'preferred_citation');
				}
			}

			if($o_node->descgrp->prefercite->p){
				foreach($o_node->descgrp->prefercite->p as $o_p){
					$vs_citation = $this->clean_string(dom_import_simplexml($o_p)->textContent);
					$t_object->addAttribute(array(
						'locale_id' => $this->opn_locale_id,
						'preferred_citation' => $vs_citation,
					),'preferred_citation');
				}
			}

			if($o_aid = $o_node->otherfindaid){
				
			}

			if($o_aid = $o_node->descgrp->otherfindaid){
				if($o_aid->extref){
					$vs_link = dom_import_simplexml($o_node->descgrp->otherfindaid->extref)->getAttribute("xlink:href");
					if(preg_match("/^(http|https)\:\/\/([a-z0-9-]+\.){0,}[a-z0-9-]+\.[a-z]{2,4}/", $vs_link)){
						$t_object->addAttribute(array(
							'finding_aid_url' => $vs_link,
						),'finding_aid_url');
					}	
				}
			}

			$t_object->insert();
					
			DataMigrationUtils::postError($t_object, "While adding object");
			if(!$t_object->getPrimaryKey()) continue;

			if($vs_title = $this->clean_string(dom_import_simplexml($o_node->did->unittitle)->textContent)){
				$t_object->addLabel(array(
					'name' => $vs_title
				), $this->opn_locale_id, null, true);
			}

			DataMigrationUtils::postError($t_object, "While adding object label");

			// create and/or relate originations
			foreach($o_node->did->origination as $o_org){
				foreach($o_org->corpname as $o_corpname){
					$this->importEntity($o_corpname,__ENTITY_MODE_CORP__,$t_object);
				}
				foreach($o_org->persname as $o_persname){
					$this->importEntity($o_persname,__ENTITY_MODE_PERSON__,$t_object);
				}
			}

			// handle controlled access headings
			// @see: http://www.loc.gov/ead/tglib/elements/controlaccess.html
			if($o_node->controlaccess){
				$this->importControlAccess($o_node->controlaccess,$t_object);
			}

			// we're relating from the "child side", i.e. right-to-left
			if($vn_parent_id){
				$t_object->addRelationship("ca_objects",$vn_parent_id,"hasPart",null,null,"rtol");
			}

			DataMigrationUtils::postError($t_object, "While relating object to other stuff");
			
			foreach($this->opa_c as $vs_c){
				$this->processItem($o_node->{$vs_c}, $t_object->getPrimaryKey());
			}
		}

		return $t_object->getPrimaryKey();
	}
	// ----------------------------------------------------------------------
	function importControlAccess($o_element,$t_object){
		foreach($o_element->corpname as $o_name){
		 	$this->importEntity($o_name,__ENTITY_MODE_CORP__,$t_object);
		}
		foreach($o_element->famname as $o_name){
		 	$this->importEntity($o_name,__ENTITY_MODE_CORP__,$t_object);
		}
		foreach($o_element->persname as $o_name){
		 	$this->importEntity($o_name,__ENTITY_MODE_PERSON__,$t_object);
		}
		foreach($o_element->genreform as $o_subject){
			$this->importElementAsSubject($o_subject,$t_object);
		}
		foreach($o_element->geogname as $o_subject){
			$this->importElementAsSubject($o_subject,$t_object);	
		}
		foreach($o_element->subject as $o_subject){
			$this->importElementAsSubject($o_subject,$t_object);
		}
		foreach($o_element->title as $o_subject){
			$this->importElementAsSubject($o_subject,$t_object);
		}

		// handle sub-elements
		if($o_element->controlaccess){
			foreach($o_element->controlaccess as $o_access){
				$this->importControlAccess($o_access,$t_object);
			}
		}
	}
	// ----------------------------------------------------------------------
	protected function importElementAsSubject($o_element,$t_object){

		$vs_text = $this->clean_string(dom_import_simplexml($o_element)->textContent);
		switch($this->get_attribute($o_element,"source")){
			case "lcsh":
				$vs_subject_source = "lcsh1";
				break;
			case "aat":
				$vs_subject_source = "aat";
				break;
			default:
				$vs_subject_source = "local";
				break;
		}

		$t_object->addAttribute(array(
			'locale_id' => $this->opn_locale_id,
			'subject_text' => $vs_text,
			'subject_source' => $vs_subject_source,
		),'subject');

		$t_object->update();
		DataMigrationUtils::postError($t_object,"While adding subject");
	}
	// ----------------------------------------------------------------------
	protected function importEntity($o_name_element,$pn_import_mode,$t_object){
		if(!$o_name_element) return;
		$t_rel_types = new ca_relationship_types();
		// get rid of all markup and get pure text
		$vs_name = dom_import_simplexml($o_name_element)->textContent;
		$vs_name = $this->clean_string($vs_name);

		$vs_role = null;

		if(!($vs_role = $this->get_attribute($o_name_element,"role"))){
			// try to fall back on role description in text
			// TODO: this is very specific to one of the example files I got, check again
			// as soon as more files come in!
			$va_matches = array();
			if(preg_match("/(.*)\,\ ([a-zA-Z]+)\.$/", $vs_name,$va_matches)){
				if(strlen(trim($va_matches[2]))>0){
					$vs_role = trim($va_matches[2]);
					$vs_name = trim($va_matches[1]);
				}
			}
		}

		switch($pn_import_mode){
			case __ENTITY_MODE_PERSON__:
				$va_entity_label = DataMigrationUtils::splitEntityName($vs_name);
				$vs_entity_type = "ind";
				break;
			case __ENTITY_MODE_CORP__:
			default:
				$va_entity_label = array(
					"surname" => $vs_name,
					"displayname" => $vs_name,
				);
				$vs_entity_type = "org";
				break;
		}

		if(strlen($vs_role)>0){
			$vn_type_id = $t_rel_types->getRelationshipTypeID("ca_objects_x_entities",$vs_role);
			if(!$vn_type_id) $vs_role = "creator";
		} else {
			$vs_role = "creator";
		}

		$va_values = array();
		$t_entity = new ca_entities();
		$vs_ent_idno = "NFAI.E.00000000";
		$o_numbering_plugin = $t_entity->getIDNoPlugInInstance();
		if (!($vs_sep = $o_numbering_plugin->getSeparator())) { $vs_sep = '.'; }
		if (!is_array($va_idno_values = $o_numbering_plugin->htmlFormValuesAsArray('idno', $vs_ent_idno, false, false, true))) { 
			$va_idno_values = array();
		}
		$va_values["idno"] = join($vs_sep, $va_idno_values);

		$vn_entity_id = DataMigrationUtils::getEntityID($va_entity_label,$vs_entity_type,$this->opn_locale_id,$va_values,array("transaction" => $this->o_tx));
		$t_object->addRelationship("ca_entities",$vn_entity_id,$vs_role);
	}
	// ----------------------------------------------------------------------
	protected function importParagraphsAsNote($o_nodes,$vs_note_type,$t_object){
		if(!$o_nodes) return;

		foreach($o_nodes as $o_node){
			$vs_text = "";
			if($o_node->head){
				$vs_text .= "<h3>".trim((string) $o_node->head)."</h3>";
			}
			foreach($o_node->p as $vo_p){
				$vs_text .= "<p>".$this->clean_string(dom_import_simplexml($vo_p)->textContent)."</p>\n";
			}
			$t_object->addAttribute(array(
				'Dacs_Detail' => $vs_text,
				'aacs_dropdown' => $vs_note_type,
			),'dacs_element_note');
		}
	}
	// ----------------------------------------------------------------------
	// Utilities
	// ----------------------------------------------------------------------
	protected function get_attribute($o_element,$ps_attr_name){
		if(!$o_element) return "";
		$o_attributes = $o_element->attributes();
		return trim((string)$o_attributes->$ps_attr_name);
	}
	// ----------------------------------------------------------------------
	protected function clean_string($ps_string){
		// clean multiple whitespaces and trim
		$vs_text = trim(preg_replace('/\s[\s]+/',' ',$ps_string));
		// clean trailing "." (happens a lot)
		if($vs_text[strlen($vs_text)-1]=="."){
			$vs_text = substr($vs_text,0,strlen($vs_text)-1);
		}
		return $vs_text;
	}
	// ----------------------------------------------------------------------
}

?>
