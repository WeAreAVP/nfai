<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_entities_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010-2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */

	$t_entity 			= $this->getVar('t_item');
	$vn_entity_id 		= $t_entity->getPrimaryKey();
	
	$vs_title 			= $this->getVar('label');
	
	$va_access_values	= $this->getVar('access_values');

if (!$this->request->isAjax()) {		
?>
	<div id="detailBody">
		<div id="pageNav">
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_entities', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					print caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Entity', 'Show', array('entity_id' => $this->getVar('previous_id')), array('id' => 'previous'));
				}else{
					print "&lsaquo; "._t("Previous");
				}
				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				if ($this->getVar('next_id') > 0) {
					print caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Entity', 'Show', array('entity_id' => $this->getVar('next_id')), array('id' => 'next'));
				}else{
					print _t("Next")." &rsaquo;";
				}
			}
?>
		</div><!-- end nav -->
		<div><b><?php print unicode_strtoupper($this->getVar('typename')); ?></b></div>
		<h1><?php print $vs_title; ?></h1>
		<div id="leftCol">		
<?php
			if((!$this->request->config->get('dont_allow_registration_and_login')) && $this->request->config->get('enable_bookmarks')){
?>
				<!-- bookmark link BEGIN -->
				<div class="unit">
<?php
				if($this->request->isLoggedIn()){
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_entity_id, 'tablename' => 'ca_entities'));
				}else{
					print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_entity_id, 'tablename' => 'ca_entities'));
				}
?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
<?php
			}
			# --- identifier
			if($t_entity->get('idno')){
				print "<div class='unit'><b>"._t("Identifier")."</b><br/> ".$t_entity->get('idno')."</div><!-- end unit -->";
			}
			if($va_name = $t_entity->get('ca_entities.preferred_labels')){
				print "<div class='unit'><b>"._t("Preferred Name")."</b><br/> ".$va_name."</div><!-- end unit -->";
			}
			if($va_lcsh_naming = $t_entity->get('ca_entities.lcsh_naming')){
				print "<div class='unit'><b>"._t("Library of Congress Naming Authority")."</b><br/> ".$va_lcsh_naming."</div><!-- end unit -->";
			}			
			if($va_entity_date = $t_entity->get('ca_entities.entity_date')){
				print "<div class='unit'><b>"._t("Entity Date")."</b><br/> ".$va_entity_date."</div><!-- end unit -->";
			}
			if($va_biography_description = $t_entity->get('ca_entities.biography.biography_description')){
				print "<div class='unit'><b>"._t("Biography/History")."</b><br/> ".$va_biography_description."</div><!-- end unit -->";
			}
			if($va_external_link = $t_entity->get('ca_entities.external_link')){
				print "<div class='unit'><b>"._t("URL")."</b><br/> <a href='{$va_external_link}' target='_blank'>".$va_external_link."</a></div><!-- end unit -->";
			}			
			# --- attributes
			$va_attributes = $this->request->config->get('ca_entities_detail_display_attributes');
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_entity->get("ca_entities.{$vs_attribute_code}")){
						print "<div class='unit'><b>".$t_entity->getDisplayLabel("ca_entities.{$vs_attribute_code}")."</b><br/> {$vs_value}</div><!-- end unit -->";
					}
				}
			}
			# --- description
			if($this->request->config->get('ca_entities_description_attribute')){
				if($vs_description_text = $t_entity->get("ca_entities.".$this->request->config->get('ca_entities_description_attribute'))){
					print "<div class='unit'><div id='description'><b>".$t_entity->getDisplayLabel('ca_entities.'.$this->request->config->get('ca_entities_description_attribute'))."</b><br/> {$vs_description_text}</div></div><!-- end unit -->";				
?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#description').expander({
								slicePoint: 300,
								expandText: '<?php print _t('[more]'); ?>',
								userCollapse: false
							});
						});
					</script>
<?php
				}
			}
			# --- entities
			$va_entities = $t_entity->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_entities) > 0){	
?>
				<div class="unit"><h2><?php print _t("Related")." ".((sizeof($va_entities) > 1) ? _t("Entities") : _t("Entity")); ?></h2>
<?php
				foreach($va_entities as $va_entity) {
					print "<div>".(($this->request->config->get('allow_detail_for_ca_entities')) ? caNavLink($this->request, $va_entity["label"], '', 'Detail', 'Entity', 'Show', array('entity_id' => $va_entity["entity_id"])) : $va_entity["label"])." (".$va_entity['relationship_typename'].")</div>";		
				}
?>
				</div><!-- end unit -->
<?php
			}
			
			# --- occurrences
			$va_occurrences = $t_entity->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			$va_sorted_occurrences = array();
			if(sizeof($va_occurrences) > 0){
				$t_occ = new ca_occurrences();
				$va_item_types = $t_occ->getTypeList();
				foreach($va_occurrences as $va_occurrence) {
					$t_occ->load($va_occurrence['occurrence_id']);
					$va_sorted_occurrences[$va_occurrence['item_type_id']][$va_occurrence['occurrence_id']] = $va_occurrence;
				}
				
				foreach($va_sorted_occurrences as $vn_occurrence_type_id => $va_occurrence_list) {
?>
						<div class="unit"><h2><?php print _t("Related")." ".$va_item_types[$vn_occurrence_type_id]['name_singular'].((sizeof($va_occurrence_list) > 1) ? "s" : ""); ?></h2>
<?php
					foreach($va_occurrence_list as $vn_rel_occurrence_id => $va_info) {
						print "<div>".(($this->request->config->get('allow_detail_for_ca_occurrences')) ? caNavLink($this->request, $va_info["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_rel_occurrence_id)) : $va_info["label"])." (".$va_info['relationship_typename'].")</div>";
					}
					print "</div><!-- end unit -->";
				}
			}
			# --- places
			$va_places = $t_entity->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_places) > 0){
				print "<div class='unit'><h2>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</h2>";
				foreach($va_places as $va_place_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- collections
			$va_collections = $t_entity->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><h2>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</h2>";
				foreach($va_collections as $va_collection_info){
					print "<div>";
					print (($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label'])." (".$va_collection_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- vocabulary terms
			$va_terms = $t_entity->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_terms) > 0){
				print "<div class='unit'><h2>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</h2>";
				foreach($va_terms as $va_term_info){
					print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label']))."</div>";
				}
				print "</div><!-- end unit -->";
			}			

?>
	</div><!-- end leftCol -->
	<div id="rightCol">
		<div id="resultBox">
<?php
}
	// set parameters for paging controls view
	$this->setVar('other_paging_parameters', array(
		'entity_id' => $vn_entity_id
	));
//	print $this->render('related_objects_grid.php');
	
if (!$this->request->isAjax()) {
?>
		</div><!-- end resultBox -->
	</div><!-- end rightCol -->
</div><!-- end detailBody -->
<?php
}
?>
<div class="clearfix"></div><div class="all-children">
	<?php 
	$t_object = new ca_objects();
	$t_occ	= $this->getVar('t_item');
//	$va_objects = $t_occ->get("ca_objects", array( "returnAsArray" => 1, 'checkAccess' => $va_access_values));
	$va_objects = $t_occ->get("ca_objects", array("restrictToTypes" => array("collection"), "returnAsArray" => 1, 'checkAccess' => $va_access_values));
	if(sizeof($va_objects)>0)
	{
		print "<div style='border-bottom: 2px solid #696969;'><h1 style='color:#3D3D3D;'>Collections & Objects in this ".  unicode_ucfirst($this->getVar('typename')) ."</h1></div>";
		print "<table class='table hierarchy-table'>";
		print "<thead><tr><th style='text-align: center;'>Title</th><th>Type</th></tr></thead>";
		foreach ($va_objects as $va_child){
			print "<tr>";
		$child_idno = $va_child['object_id'];
		$the_child = new ca_objects($child_idno);
		$child_type = $the_child->get('ca_objects.type_id', array('convertCodesToDisplayText' => true));

		# only show the first 5 and have a more link

		$va_rep = $the_child->getPrimaryRepresentation(array('thumbnail', 'medium'), null, array('return_with_access' => $va_access_values));
		print "<td>";
		if ($va_rep['urls']['thumbnail'] != '')
			print "<img src='" . $va_rep['urls']['thumbnail'] . "'  style='height:35px;padding-right:20px;float:left;' width='50' />";
		else
			print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";

		# only show the first 5 and have a more link

		print "<div style='padding-left:64px;'>". caNavLink($this->request, $va_child['name'] . " ", '', 'Detail', 'Object', 'Show', array('object_id' => $va_child['object_id'])) . "</div></td>";
		print "<td>" . $child_type . "</td>";
		print "</tr>";
		getAllChildrens($the_child, $this->request,25);
		}
		print "</table><!-- end unit -->";
	}
	function getAllChildrens($t_object, $request_url,$padding)
{
	$va_children = $t_object->get("ca_objects.children.preferred_labels", array('returnAsArray' => 1, 'checkAccess' => $va_access_values));
	if (sizeof($va_children) > 0)
	{
//				print "<div class='unit'><b>"._t("Part%1", ((sizeof($va_children) > 1) ? "s" : ""))."</b> ";
		$i = 0;

		foreach ($va_children as $va_child)
		{
			print "<tr>"; //
			$child_idno = $va_child['object_id'];
			$the_child = new ca_objects($child_idno);
			$child_type = $the_child->get('ca_objects.type_id', array('convertCodesToDisplayText' => true));

			$va_rep = $the_child->getPrimaryRepresentation(array('thumbnail', 'medium'), null, array('return_with_access' => $va_access_values));
			print "<td style='padding-left:".$padding."px;'>";
			if ($va_rep['urls']['thumbnail'] != '')
				print "<img src='" . $va_rep['urls']['thumbnail'] . "' style='height:35px;padding-right:20px;float:left;' width='50'/>";
			else
				print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";

			# only show the first 5 and have a more link

			print  "<div style='padding-left:64px;'>".caNavLink($request_url, $va_child['name'] . " ", '', 'Detail', 'Object', 'Show', array('object_id' => $va_child['object_id'])) . "</div></td>";
			print "<td>" . $child_type . "</td>";
			print "</tr>";
			getAllChildrens($the_child, $request_url,$padding+25);
			$i ++;

//					exit;
		}
	}
}
	?>
</div>
