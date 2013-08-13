<?php
/* ----------------------------------------------------------------------
 * themes/wwsf/views/ca_objects_browse_html.php : 
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
 
	$va_facets 				= $this->getVar('available_facets');
	$va_facets_with_content	= $this->getVar('facets_with_content');
	$va_facet_info 			= $this->getVar('facet_info');
	$va_criteria 			= is_array($this->getVar('criteria')) ? $this->getVar('criteria') : array();
	$va_results 			= $this->getVar('result');
	
	$vs_browse_target		= $this->getVar('target');

	if (!$this->request->isAjax()) {
?>
	<h1><?php print _t('Library'); ?></h1>
	<div id="browse"><div id="resultBox"> 
<?php
	}
	if ($this->getVar('browse_selector')) {
?>
		<div class="browseTargetSelect"><?php print _t('Browse for').' '.$this->getVar('browse_selector'); ?></div>
		<div style="clear: both;"><!-- empty --></div>
<?php
	}
?>
		<div style="position: relative;">
<?php
			if (sizeof($va_criteria)) {
				print "<div id='browseControls'>";
				if (sizeof($va_facets)) { 
?>
					<div id="refineBrowse"><span class='refineHeading'><?php print _t('Refine results by'); ?>:</span>
<?php
						$vn_i = 1;
						$va_available_facets = $this->getVar('available_facets');
						foreach($va_available_facets as $vs_facet_code => $va_facet_info) {
							print "<a href='#' onclick='caUIBrowsePanel.showBrowsePanel(\"{$vs_facet_code}\");' class='facetLink'>".$va_facet_info['label_plural']."</a>";
							if($vn_i < sizeof($va_available_facets)){
								print ", ";
							}
							$vn_i++;
						}
?>
					</div><!-- end refineBrowse -->
<?php
				}

				$vn_x = 0;
				print "<div id='browseCriteria'><span class='criteriaHeading'>"._t("You browsed for: ")."</span>";
				foreach($va_criteria as $vs_facet_name => $va_row_ids) {
					$vn_x++;
					$vn_row_c = 0;
					foreach($va_row_ids as $vn_row_id => $vs_label) {
						print "{$vs_label}".caNavLink($this->request, 'x', 'close', '', 'Browse', 'removeCriteria', array('facet' => $vs_facet_name, 'id' => $vn_row_id))."\n";
						$vn_row_c++;
					}
					
				}
				print caNavLink($this->request, _t('start new search')." &rsaquo;", 'startOver', '', 'Browse', 'clearCriteria', array());
				print "</div><!-- end browseCriteria -->\n";
				print "</div><!-- end browseControls -->";
				
			} else {
				print $this->render('Browse/browse_intro_text_html.php');
				# dislay 8 random objects in a slideshow
 				$t_object = new ca_objects();
 				$va_access_values = caGetUserAccessValues($this->request);
 				$va_random_item = $t_object->getRandomItems(8, array('checkAccess' => $va_access_values, 'hasRepresentations' => 1));
 				if(sizeof($va_random_item) > 0){
 					JavascriptLoadManager::register("cycle");
 					print "<div class='browseSlideshow'><div id='slideShow'>";
 					foreach($va_random_item as $vn_object_id => $va_object_info) {
 						$t_object = new ca_objects($vn_object_id);
 						$va_rep = $t_object->getPrimaryRepresentation(array('medium'), null, array('return_with_access' => $va_access_values));
 						# --- get the height of the image so can calculate padding needed to center vertically
						$vn_padding_top_bottom = 0;
						$vn_padding_top_bottom =  ((410 - $va_rep['info']['medium']['HEIGHT']) / 2);
						print "<div><div class='searchThumbBgBrowseLanding searchThumbnail".$vn_object_id."' style='padding: ".$vn_padding_top_bottom."px 0px ".$vn_padding_top_bottom."px 0px;'>";
						if($t_object->get('ca_objects.type_id') == 5){
						# --- video so print out icon
							print "<div class='videoIcon'><img src='".$this->request->getThemeUrlPath()."/graphics/video.gif' width='26' height='26' border='0'></div>";
						}
						print caNavLink($this->request, $va_rep["tags"]["medium"], '', 'Detail', 'Object', 'Show', array('object_id' => $vn_object_id));
						
						// Get thumbnail caption
						$vs_caption = $t_object->getLabelForDisplay();
						$this->setVar('object_id', $vn_object_id);
						$this->setVar('caption_title', $vs_caption);
						$this->setVar('caption_idno', $t_object->get("idno"));
						
						print "</div><div class='searchThumbCaption searchThumbnail".$vn_object_id."'>".$this->render('Results/ca_objects_result_caption_html.php')."</div>";
						print "</div>";
 					}
 					print "</div></div>";
?>
					<script type="text/javascript">
					$(document).ready(function() {
						$('#slideShow').cycle({
							fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
							speed:  1000,
							timeout: 2000
						});
					});
					</script>
<?php
 				}
				if (sizeof($va_facets)) { 
					print "<div class='startBrowsingBy'>"._t("Start browsing by:")."</div>";
					print "<div id='facetList'>";
					$va_available_facets = $this->getVar('available_facets');
					foreach($va_available_facets as $vs_facet_code => $va_facet_info) {
						print "<div class='facetHeadingLink'><a href='#' onclick='caUIBrowsePanel.showBrowsePanel(\"{$vs_facet_code}\");'>".$va_facet_info['label_plural']."</a></div>\n";
						print "<div class='facetDescription'>".$va_facet_info["description"]."</div>";
					}
					print "</div><!-- end facetList -->";
				}
			}
?>
		</div><!-- end position:relative -->
<?php
	if (sizeof($va_criteria) > 0) {
		# --- show results
		print $this->render('Results/paging_controls_html.php');
?>
		<a href='#' id='showOptions' onclick='$("#searchOptionsBox").slideDown(250); $("#showOptions").hide(); return false;'><?php print _t("Options"); ?> <img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/arrow_right_gray.gif" width="9" height="10" border="0"></a>
<?php		
		print $this->render('Search/search_controls_html.php');
		print "<div class='sectionBox'>";
		print $this->render('Results/'.$vs_browse_target.'_results_'.$this->getVar('current_view').'_html.php');
		print "</div>";
	}
	if (!$this->request->isAjax()) {
?>
	</div><!-- end resultbox --></div><!-- end browse -->

<div id="splashBrowsePanel" class="browseSelectPanel" style="z-index:1000;">
	<a href="#" onclick="caUIBrowsePanel.hideBrowsePanel()" class="browseSelectPanelButton">&nbsp;</a>
	<div id="splashBrowsePanelContent">
	
	</div>
</div>
<script type="text/javascript">
	var caUIBrowsePanel = caUI.initBrowsePanel({ 
		facetUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'getFacet'); ?>',
		addCriteriaUrl: '<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'addCriteria'); ?>',
		singleFacetValues: <?php print json_encode($this->getVar('single_facet_values')); ?>
	});

	//
	// Handle browse header scrolling
	//
	jQuery(document).ready(function() {
		jQuery("div.scrollableBrowseController").scrollable(); 
	});
</script>
<?php
	}
?>