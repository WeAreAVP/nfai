<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Detail/ca_objects_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
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
	$t_object = 						$this->getVar('t_item');
	$vn_object_id = 				$t_object->get('object_id');
	$vs_title = 						$this->getVar('label');
	
	$t_rep = 							$this->getVar('t_primary_rep');
	$vs_display_version =		$this->getVar('primary_rep_display_version');
	$va_display_options =		$this->getVar('primary_rep_display_options');
	
	$va_access_values = 		$this->getVar('access_values');
	
	# -- get the various object types
	$o_lists = new ca_lists;
	$vn_fond_id = $o_lists->getItemIDFromList('object_types', 'fonds');
	$vn_sub_fond_id = $o_lists->getItemIDFromList('object_types', 'sub_fonds');
	$vn_series_id = $o_lists->getItemIDFromList('object_types', 'series');

?>	
	<div id="detailBody">		
		<div id="leftCol" >
			<!--img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/city/city-archives.jpg" alt="" title="" /-->
			<div class="maincol">
				
				<h1>City Archives</h1>
		
		<div id="pageNav">
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_objects', _t("Back to Results"), ''))) {
				if ($this->getVar('previous_id')) {
					print "<div class='searchboxesnav'><p class='searchbox-top-links'>";
					print "<div class='searchbox-buttonnav'>".caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
				}else{
					print "<div class='searchboxesnav'><p class='searchbox-top-links'>";
					print "<div class='searchbox-buttonnav'>&lsaquo; "._t("Previous")."</div>";
				}
				print "<div class='searchbox-button' style='margin-left:5px;'>&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;</div>";
				if ($this->getVar('next_id') > 0) {
					print "<div class='searchbox-buttonnav'>".caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('next_id')), array('id' => 'next'))."</div></p></div>";
				}else{
					print "<div class='searchbox-buttonnav'>"._t("Next")." &rsaquo;</div></p></div>";
				}
			} else {
				print "<div class='searchbox-button' style='float:right; margin: 10px 15px 20px 0px; '>".caNavLink($this->request, _t("Back to Archive"), '', '', '', '')."</div>";
			}
?>
		
		</div><!-- end nav -->
		<div class="searchboxitem">
<?php
		if ($t_rep && $t_rep->getPrimaryKey()) {
?>
			<div id="objDetailImage">
<?php
			if($va_display_options['no_overlay']){
				print $t_rep->getMediaTag('media', $vs_display_version, $this->getVar('primary_rep_display_options'));
			}else{
			//	print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >".$t_rep->getMediaTag('media', $vs_display_version, $this->getVar('primary_rep_display_options'))."</a>";
				
 				$va_opts = array('display' => 'detail', 'object_id' => $vn_object_id, 'containerID' => 'cont');
				print "<div id='cont' style='width: 100%; height: 100%;'>".$t_rep->getRepresentationViewerHTMLBundle($this->request, $va_opts)."</div>";
			}
?>
			</div><!-- end objDetailImage -->
			<div id="objDetailImageNav">
				<div style="float:right;">
<?php
					if (!$this->request->config->get('dont_allow_registration_and_login')) {
						if($this->request->isLoggedIn()){
							print caNavLink($this->request, _t("Add to Collection +"), '', '', 'Sets', 'addItem', array('object_id' => $vn_object_id), array('style' => 'margin-right:20px;'));
						}else{
							print caNavLink($this->request, _t("Add to Collection +"), '', '', 'LoginReg', 'form', array('site_last_page' => 'Sets', 'object_id' => $vn_object_id), array('style' => 'margin-right:20px;'));
						}
					}
					
					print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >"._t("Zoom/more media")." +</a>";
?>
				</div>			
			</div><!-- end objDetailImageNav -->
<?php
		} else {
			print "<div id='objDetailImage'><img src='".$this->request->getThemeUrlPath()."/graphics/city/absent.jpg' border='0'></div>";
		}
?>
			<h3 style="clear:right;"><?php print unicode_ucfirst($this->getVar('typename')).': '.$vs_title; ?></h3>
			<div class="searchboxitemtext">
<?php
			# --- identifier
			if($t_object->get('idno')){
				print "<div class='unit'><b>"._t("Reference Code").":</b> ".$t_object->get('idno')."</div><!-- end unit -->";
			}
			# --- hirearchy breadcrumb trail
			$va_ancestors = array();
			$va_ancestors = $t_object->getHierarchyAncestors();
			if(sizeof($va_ancestors) > 0){
				$va_hier_path = array();
				$t_hier_object = new ca_objects();
				foreach($va_ancestors as $va_ancestor){
					$t_hier_object->load($va_ancestor['NODE']['object_id']);
					$va_hier_path[] = caNavLink($this->request, $t_hier_object->getLabelForDisplay(), '', 'Detail', 'Object', 'Show', array('object_id' => $va_ancestor['NODE']['object_id']));
				}
				print "<div class='unit'><b>"._t("Part Of").":</b> ".join(" > ", array_reverse($va_hier_path))."</div>";
			}
			# --- child hierarchy info
			$va_children = $t_object->get("ca_objects.children.preferred_labels", array('returnAsArray' => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_children) > 0){
				print "<div class='unit'><b>"._t("Part%1", ((sizeof($va_children) > 1) ? "s" : "")).":</b><br/>";
				$i = 0;
				foreach($va_children as $va_child){
					# only show the first 5 and have a more link
					if($i == 5){
						print "<div id='moreChildrenLink'><a href='#' onclick='$(\"#moreChildren\").slideDown(250); $(\"#moreChildrenLink\").hide(1); return false;'>["._t("More")."]</a></div><!-- end moreChildrenLink -->";
						print "<div id='moreChildren' style='display:none;'>";
					}
					print "<div>".caNavLink($this->request, $va_child['name'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_child['object_id']))."</div>";
					$i++;
					if(($i >= 5) && ($i == sizeof($va_children))){
						print "</div><!-- end moreChildren -->";
					}
				}
				print "</div><!-- end unit -->";
			}
			# --- creator
			$va_creators = $t_object->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, 'restrict_to_relationship_types' => array('creator')));
			if(sizeof($va_creators) > 0){	
?>
				<div class="unit"><b><?php print ((sizeof($va_creators) > 1) ? _t("Creators") : _t("Creator")); ?>: </b>
<?php
				$i = 0;
				foreach($va_creators as $va_creator) {
					print (($this->request->config->get('allow_detail_for_ca_entities')) ? caNavLink($this->request, $va_creator["label"], '', 'Detail', 'Entity', 'Show', array('entity_id' => $va_creator["entity_id"])) : $va_creator["label"]);
					$i++;
					if($i < sizeof($va_creators)){
						print ", ";
					}
				}
?>
				</div><!-- end unit -->
<?php
			}
			# --- attributes shared by fonds, subfonds, series and items
			$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes');
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($t_object->get("ca_objects.{$vs_attribute_code}")){
						print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> ".$t_object->get("ca_objects.{$vs_attribute_code}")."</div><!-- end unit -->";
					}
				}
			}
			
			# --- switch to display attributes specific to object type
			switch($t_object->get("type_id")){
				# --- fond
				case $vn_fond_id:
					$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes_fond');
					if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
						foreach($va_attributes as $vs_attribute_code){
							switch($vs_attribute_code){
								case "archival_finding_aids_link":
									if($t_object->get("ca_objects.{$vs_attribute_code}")){
										print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> <a href='".$t_object->get("ca_objects.{$vs_attribute_code}")."' target='_blank'>".$t_object->get("ca_objects.{$vs_attribute_code}")."</a></div><!-- end unit -->";
									}
								break;
								# --------------------------
								case "archival_finding_aids_file":
									if($t_object->get("ca_objects.{$vs_attribute_code}")){
										$vs_url = $t_object->get("ca_objects.".$vs_attribute_code, array("return" => "url"));
										print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> <a href='".$vs_url."'>Download file</a></div><!-- end unit -->";
									}								
								break;
								# --------------------------
								default:
									if($t_object->get("ca_objects.{$vs_attribute_code}")){
										print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> ".$t_object->get("ca_objects.{$vs_attribute_code}")."</div><!-- end unit -->";
									}
								break;
								# --------------------------
							}
						}
					}
					# --- related materials
					$va_related_objects = $t_object->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
					if (sizeof($va_related_objects)) {
						print "<div class='unit'><b>"._t("Related Materials").": </b>";
						$i= 0;
						foreach($va_related_objects as $vn_rel_id => $va_info){
							print caNavLink($this->request, $va_info['label'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
							$i++;
							if($i < sizeof($va_related_objects)){
								print ", ";
							}
						}
						print "</div>";
					}
			
				break;
				# ------------------
				# --- subfond
				case $vn_sub_fond_id:
					$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes_subfond');
					if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
						foreach($va_attributes as $vs_attribute_code){
							if($t_object->get("ca_objects.{$vs_attribute_code}")){
								print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> ".$t_object->get("ca_objects.{$vs_attribute_code}")."</div><!-- end unit -->";
							}
						}
					}				
				break;
				# ------------------
				# --- series
				case $vn_series_id:
					$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes_series');
					if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
						foreach($va_attributes as $vs_attribute_code){
							if($t_object->get("ca_objects.{$vs_attribute_code}")){
								print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> ".$t_object->get("ca_objects.{$vs_attribute_code}")."</div><!-- end unit -->";
							}
						}
					}				
				break;
				# ------------------
				# --- items
				default:
					$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes_item');
					if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
						foreach($va_attributes as $vs_attribute_code){
							if($t_object->get("ca_objects.{$vs_attribute_code}")){
								print "<div class='unit'><b>".$t_object->getAttributeLabel($vs_attribute_code).":</b> ".$t_object->get("ca_objects.{$vs_attribute_code}")."</div><!-- end unit -->";
							}
						}
					}				
				break;
				# ------------------
			}
			
			# --- vocabulary terms
			$va_terms = $t_object->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_terms) > 0){
				print "<div class='unit'><h2>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</h2>";
				foreach($va_terms as $va_term_info){
					print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label']))."</div>";
				}
				print "</div><!-- end unit -->";
			}
			
			
			
			
			
			
# --- commenting out generic code that I think can go away			
if($xxx){			
			# --- entities
			$va_entities = $t_object->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, 'exclude_relationship_types' => array('curator')));
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
			$va_occurrences = $t_object->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
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
			$va_places = $t_object->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_places) > 0){
				print "<div class='unit'><h2>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</h2>";
				foreach($va_places as $va_place_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- collections
			$va_collections = $t_object->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><h2>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</h2>";
				foreach($va_collections as $va_collection_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label'])." (".$va_collection_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- map
			if($this->request->config->get('ca_objects_map_attribute') && $t_object->get($this->request->config->get('ca_objects_map_attribute'))){
				$o_map = new GeographicMap(300, 200, 'map');
				$o_map->mapFrom($t_object, $this->request->config->get('ca_objects_map_attribute'));
				print "<div class='unit'>".$o_map->render('HTML')."</div>";
			}			
			# --- output related object images as links
			$va_related_objects = $t_object->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if (sizeof($va_related_objects)) {
				print "<div class='unit'><h2>"._t("Related Objects")."</h2>";
				print "<table border='0' cellspacing='0' cellpadding='0' width='100%' id='objDetailRelObjects'>";
				$col = 0;
				$vn_numCols = 4;
				foreach($va_related_objects as $vn_rel_id => $va_info){
					$t_rel_object = new ca_objects($va_info["object_id"]);
					$va_reps = $t_rel_object->getPrimaryRepresentation(array('icon', 'small'), null, array('return_with_access' => $va_access_values));
					if($col == 0){
						print "<tr>";
					}
					print "<td align='center' valign='middle' class='imageIcon icon".$va_info["object_id"]."'>";
					print caNavLink($this->request, $va_reps['tags']['icon'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
					
					// set view vars for tooltip
					$this->setVar('tooltip_representation', $va_reps['tags']['small']);
					$this->setVar('tooltip_title', $va_info['label']);
					$this->setVar('tooltip_idno', $va_info["idno"]);
					TooltipManager::add(
						".icon".$va_info["object_id"], $this->render('../Results/ca_objects_result_tooltip_html.php')
					);
					
					print "</td>";
					$col++;
					if($col < $vn_numCols){
						print "<td align='center'><!-- empty --></td>";
					}
					if($col == $vn_numCols){
						print "</tr>";
						$col = 0;
					}
				}
				if(($col != 0) && ($col < $vn_numCols)){
					while($col <= $vn_numCols){
						if($col < $vn_numCols){
							print "<td><!-- empty --></td>";
						}
						$col++;
						if($col < $vn_numCols){
							print "<td align='center'><!-- empty --></td>";
						}
					}
				}
				print "</table></div><!-- end unit -->";
			}
}
?>

<?php
			if($this->request->config->get('show_add_this')){
?>
				<!-- AddThis Button BEGIN -->
				<div class="unit"><a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4baa59d57fc36521"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0;"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4baa59d57fc36521"></script></div><!-- end unit -->
				<!-- AddThis Button END -->
<?php
			}
?>

<?php
		
if (!$this->request->config->get('dont_allow_registration_and_login')) {
		# --- user data --- comments - ranking - tagging
?>			
		<div id="objUserData">
<?php
			if($this->getVar("ranking")){
?>
				<h2 id="ranking"><?php print _t("Average User Ranking"); ?> <img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/user_ranking_<?php print $this->getVar("ranking"); ?>.gif" width="104" height="15" border="0" style="margin-left: 20px;"></h2>
<?php
			}
			$va_tags = $this->getVar("tags_array");
			if(is_array($va_tags) && sizeof($va_tags) > 0){
				$va_tag_links = array();
				foreach($va_tags as $vs_tag){
					$va_tag_links[] = caNavLink($this->request, $vs_tag, '', '', 'Search', 'Index', array('search' => $vs_tag));
				}
?>
				<h2><?php print _t("Tags"); ?></h2>
				<div id="tags">
					<?php print implode($va_tag_links, ", "); ?>
				</div>
<?php
			}
			$va_comments = $this->getVar("comments");
			if(is_array($va_comments) && (sizeof($va_comments) > 0)){
?>
				<h2><div id="numComments">(<?php print sizeof($va_comments)." ".((sizeof($va_comments) > 1) ? _t("comments") : _t("comment")); ?>)</div><?php print _t("User Comments"); ?></h2>
<?php
				foreach($va_comments as $va_comment){
?>
					<div class="comment">
						<?php print $va_comment["comment"]; ?>
					</div>
					<div class="byLine">
						<?php print $va_comment["author"].", ".$va_comment["date"]; ?>
					</div>
<?php
				}
			}else{
				if(!$vs_tags && !$this->getVar("ranking")){
					$vs_login_message = _t("Login/register to be the first to rank, tag and comment on this object!");
				}
			}
			if($this->getVar("ranking") || (is_array($va_tags) && (sizeof($va_tags) > 0)) || (is_array($va_comments) && (sizeof($va_comments) > 0))){
?>
				<div class="divide" style="margin:12px 0px 10px 0px;"><!-- empty --></div>
<?php			
			}
		if($this->request->isLoggedIn()){
?>
			<h2 style="margin-top:30px;"><?php print _t("Add your rank, tags and comment"); ?></h2>
			<form method="post" action="<?php print caNavUrl($this->request, 'Detail', 'Object', 'saveCommentRanking', array('object_id' => $vn_object_id)); ?>" name="comment">
				<div class="formLabel">Rank
					<select name="rank">
						<option value="">-</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</div>
				<div class="formLabel"><?php print _t("Tags (separated by commas)"); ?></div>
				<input type="text" name="tags">
				<div class="formLabel"><?php print _t("Comment"); ?></div>
				<textarea name="comment" rows="5"></textarea>
				<br><a href="#" name="commentSubmit" onclick="document.forms.comment.submit(); return false;"><?php print _t("Save"); ?></a>
			</form>
<?php
		}else{
			if (!$this->request->config->get('dont_allow_registration_and_login')) {
				print "<p>".caNavLink($this->request, (($vs_login_message) ? $vs_login_message : _t("Please login/register to rank, tag and comment on this item.")), "", "", "LoginReg", "form", array('site_last_page' => 'ObjectDetail', 'object_id' => $vn_object_id))."</p>";
			}
		}
?>		
		</div><!-- end objUserData-->
<?php
	}
?>
		</div><!-- end searchBoxItem-->
		</div><!-- end searchBoxItemText-->
		</div><!-- end searchboxes -->
		</div><!-- end maincol -->
		</div><!-- end rightCol -->
	</div><!-- end detailBody -->
