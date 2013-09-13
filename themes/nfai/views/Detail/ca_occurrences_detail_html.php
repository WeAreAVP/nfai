<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_occurrences_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
$t_occurrence = $this->getVar('t_item');
$vn_occurrence_id = $t_occurrence->getPrimaryKey();

$vs_title = $this->getVar('label');

$va_access_values = $this->getVar('access_values');

if ( ! $this->request->isAjax())
{
	?>
	<div id="detailBody">
		<div id="pageNav">
			<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_occurrences', _t("Back"), '')))
			{
				if ($this->getVar('previous_id'))
				{
					print caNavLink($this->request, "&lsaquo; " . _t("Previous"), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('previous_id')), array('id' => 'previous'));
				}
				else
				{
					print "&lsaquo; " . _t("Previous");
				}
				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";

				if ($this->getVar('next_id') > 0)
				{
					print caNavLink($this->request, _t("Next") . " &rsaquo;", '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('next_id')), array('id' => 'next'));
				}
				else
				{
					print _t("Next") . " &rsaquo;";
				}
			}
			?>
		</div><!-- end nav -->
		<div><b><?php print unicode_strtoupper($t_occurrence->getTypeName()); ?></b></div>
		<h1><?php print $vs_title; ?></h1>
		<div id="leftCol">	
			<?php
			if (( ! $this->request->config->get('dont_allow_registration_and_login')) && $this->request->config->get('enable_bookmarks'))
			{
				?>
				<!-- bookmark link BEGIN -->
				<div class="unit">
					<?php
					if ($this->request->isLoggedIn())
					{
						print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
					}
					else
					{
						print caNavLink($this->request, _t("Bookmark item +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
					}
					?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
				<?php
			}
			# --- identifier
			if ($t_occurrence->get('idno'))
			{
				print "<div class='unit'><b>" . _t("Identifier") . "</b><br/> " . $t_occurrence->get('idno') . "</div><!-- end unit -->";
			}
			if ($va_name = $t_occurrence->get('ca_occurrences.preferred_labels'))
			{
				print "<div class='unit'><b>" . _t("Repository Name") . "</b><br/> " . $va_name . "</div><!-- end unit -->";
			}
			if ($va_alt_name = $t_occurrence->get('ca_occurrences.nonpreferred_labels', array('delimiter' => '<br/>')))
			{
				print "<div class='unit'><b>" . _t("Alternate Name") . "</b><br/> " . $va_alt_name . "</div><!-- end unit -->";
			}
			if ($va_repository_type = $t_occurrence->get('ca_occurrences.repository_type', array('convertCodesToDisplayText' => true, 'returnAsArray' => true)))
			{
				print "<div class='unit'><b>" . _t("Type of Repository") . "</b><br/> ";
				foreach ($va_repository_type as $va_term => $va_metadata)
				{
					foreach ($va_metadata as $v_i => $va_thing)
					{
						print $va_thing;
//						print caNavLink($this->request, $va_thing, '', '', 'Search', 'Index', array('search' => urlencode($va_thing)))."<br/>";
					}
				}
				print "</div>";
			}
			if ($va_repository_state = $t_occurrence->get('ca_occurrences.repository_state', array('convertCodesToDisplayText' => true)))
			{
				print "<div class='unit'><b>" . _t("Repository State") . "</b><br/> " . $va_repository_state . "</div><!-- end unit -->";
			}
			if ($t_occurrence->get('ca_occurrences.repository_description.repository_description_text') && $t_occurrence->get('ca_occurrences.repository_description.repository_description_source'))
			{
				$va_repository_description = $t_occurrence->get('ca_occurrences.repository_description', array('template' => "<b>Repository Description</b><br/> ^repository_description_text <br/><br/><b>Repository Description Source</b><br/>^repository_description_source", 'delimiter' => '<br/><br/>'));
				print "<div class='unit'>" . $va_repository_description . "</div><!-- end unit -->";
			}
			if ($va_repository_contact = $t_occurrence->get('ca_occurrences.repository_contact', array('template' => "^rep_contact_name (^rep_contact_role)", 'delimiter' => '<br/>')))
			{
				print "<div class='unit'><b>" . _t("Repository Contact") . "</b><br/> " . $va_repository_contact . "</div><!-- end unit -->";
			}
			if ($va_repository_url = $t_occurrence->get('ca_occurrences.repository_url', array('delimiter' => '<br/>')))
			{
				print "<div class='unit'><b>URL </b><br/><a href='" . $va_repository_url . "' target='_blank'>" . $va_repository_url . "</a></div><!-- end unit -->";
			}
			if ($va_address = $t_occurrence->get('ca_occurrences.address', array('template' => "^address1 <br/> ^address2 <br/> ^city, ^stateprovince ^postalcode <br/> ^country", 'delimiter' => '<br/>')))
			{
				print "<div class='unit'><b>" . _t("Address") . "</b><br/> " . $va_address . "</div><!-- end unit -->";
			}
			if ($va_telephone = $t_occurrence->get('ca_occurrences.telephone'))
			{
				print "<div class='unit'><b>" . _t("Telephone") . "</b><br/> " . $va_telephone . "</div><!-- end unit -->";
			}
			if ($va_email = $t_occurrence->get('ca_occurrences.email'))
			{
				print "<div class='unit'><b>" . _t("Email") . "</b><br/> " . $va_email . "</div><!-- end unit -->";
			}
			# --- attributes
			$va_attributes = $this->request->config->get('ca_occurrences_detail_display_attributes');
			if (is_array($va_attributes) && (sizeof($va_attributes) > 0))
			{
				foreach ($va_attributes as $vs_attribute_code)
				{
					if ($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}"))
					{
						print "<div class='unit'><b>" . $t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}") . "</b><br/> {$vs_value}</div><!-- end unit -->";
					}
				}
			}
			# --- description
			if ($this->request->config->get('ca_occurrences_description_attribute'))
			{
				if ($vs_description_text = $t_occurrence->get("ca_occurrences." . $this->request->config->get('ca_occurrences_description_attribute')))
				{
					print "<div class='unit'><div id='description'><b>" . $t_occurrence->getDisplayLabel("ca_occurrences." . $this->request->config->get('ca_occurrences_description_attribute')) . ":</b> {$vs_description_text}</div></div><!-- end unit -->";
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
			$va_entities = $t_occurrence->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if (sizeof($va_entities) > 0)
			{
				?>
				<div class="unit"><h2><?php print _t("Related") . " " . ((sizeof($va_entities) > 1) ? _t("Entities") : _t("Entity")); ?></h2>
					<?php
					foreach ($va_entities as $va_entity)
					{
						print "<div>" . (($this->request->config->get('allow_detail_for_ca_entities')) ? caNavLink($this->request, $va_entity["label"], '', 'Detail', 'Entity', 'Show', array('entity_id' => $va_entity["entity_id"])) : $va_entity["label"]) . " (" . $va_entity['relationship_typename'] . ")</div>";
					}
					?>
				</div><!-- end unit -->
				<?php
			}

			# --- occurrences
			$va_occurrences = $t_occurrence->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			$va_sorted_occurrences = array();
			if (sizeof($va_occurrences) > 0)
			{
				$t_occ = new ca_occurrences();
				$va_item_types = $t_occ->getTypeList();
				foreach ($va_occurrences as $va_occurrence)
				{
					$t_occ->load($va_occurrence['occurrence_id']);
					$va_sorted_occurrences[$va_occurrence['item_type_id']][$va_occurrence['occurrence_id']] = $va_occurrence;
				}

				foreach ($va_sorted_occurrences as $vn_occurrence_type_id => $va_occurrence_list)
				{
					?>
					<div class="unit"><h2><?php print _t("Related") . " " . $va_item_types[$vn_occurrence_type_id]['name_singular'] . ((sizeof($va_occurrence_list) > 1) ? "s" : ""); ?></h2>
						<?php
						foreach ($va_occurrence_list as $vn_rel_occurrence_id => $va_info)
						{
							print "<div>" . (($this->request->config->get('allow_detail_for_ca_occurrences')) ? caNavLink($this->request, $va_info["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_rel_occurrence_id)) : $va_info["label"]) . " (" . $va_info['relationship_typename'] . ")</div>";
						}
						print "</div><!-- end unit -->";
					}
				}
				# --- places
				$va_places = $t_occurrence->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
				if (sizeof($va_places) > 0)
				{
					print "<div class='unit'><h2>" . _t("Related Place") . ((sizeof($va_places) > 1) ? "s" : "") . "</h2>";
					foreach ($va_places as $va_place_info)
					{
						print "<div>" . (($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label']) . " (" . $va_place_info['relationship_typename'] . ")</div>";
					}
					print "</div><!-- end unit -->";
				}
				# --- collections
				$va_collections = $t_occurrence->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
				if (sizeof($va_collections) > 0)
				{
					print "<div class='unit'><h2>" . _t("Related Collection") . ((sizeof($va_collections) > 1) ? "s" : "") . "</h2>";
					foreach ($va_collections as $va_collection_info)
					{
						print "<div>" . (($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label']) . " (" . $va_collection_info['relationship_typename'] . ")</div>";
					}
					print "</div><!-- end unit -->";
				}
				# --- vocabulary terms
				$va_terms = $t_occurrence->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
				if (sizeof($va_terms) > 0)
				{
					print "<div class='unit'><h2>" . _t("Subject") . ((sizeof($va_terms) > 1) ? "s" : "") . "</h2>";
					foreach ($va_terms as $va_term_info)
					{
//						print "<div>" . caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label'])) . "</div>";
						print "<div>" . $va_term_info['label'] . "</div>";
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
					'occurrence_id' => $vn_occurrence_id
				));
//		print $this->render('related_objects_grid.php');

				if ( ! $this->request->isAjax())
				{
					?>
				</div><!-- end resultBox -->


			</div><!-- end rightCol -->
		</div><!-- end detailBody -->
		<?php
	}
	?>
	<div class="clearfix"></div><div class="all-children">
		<?php
		$o_db = new Db();
		$access=implode(',',$va_access_values);
		$object_result = $o_db->query("SELECT o.object_id,ol.name,lt.item_value,orr.media
						FROM  `ca_objects` o
						INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
						INNER JOIN ca_list_items lt ON lt.item_id=o.type_id 
						LEFT JOIN  `ca_objects_x_object_representations` oor ON oor.object_id = o.object_id AND oor.is_primary=1
						LEFT JOIN  `ca_object_representations` orr ON orr.representation_id = oor.representation_id AND orr.deleted=0
						INNER JOIN  `ca_objects_x_occurrences` oe ON oe.object_id = o.object_id 
						WHERE o.deleted=0 AND o.access IN ({$access}) AND o.type_id=21 AND oe.occurrence_id={$vn_occurrence_id}
					    ORDER BY ol.name_sort");
		$i = 0;

		while ($object_result->nextRow())
		{
			if ($i == 0)
			{
				print "<div style='border-bottom: 2px solid #696969;'><h1 style='color:#3D3D3D;'>Objects in this " . unicode_ucfirst($this->getVar('typename')) . "</h1></div>";
				print "<table class='table hierarchy-table'>";
				print "<thead><tr><th style='text-align: center;'>Title</th><th>Type</th></tr></thead>";
			}
			$record = $object_result->getRow();
			print "<tr>";
			print "<td>";
			if ($object_result->getMediaUrl('media', 'thumbnail') != '')
				print "<a class='preview'  href='javascript://;' rel=" . $object_result->getMediaUrl('media','medium') . " title='{$record['name']}'><img src='" . $object_result->getMediaUrl('media', 'thumbnail') . "'  style='height:35px;padding-right:20px;float:left;' width='50' /></a>";
			else
				print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";
			print "<div style='padding-left:64px;'>" . caNavLink($this->request, $record['name'] . " ", '', 'Detail', 'Object', 'Show', array('object_id' => $record['object_id'])) . "</div></td>";
			print "<td>" . ucfirst($record['item_value']) . "</td>";
			print "</tr>";


			getAllChildrens($record['object_id'], $this->request, 25,$access);
			$i ++;
		}
		if ($i > 0)
		{
			print "</table><!-- end unit -->";
		}

		function getAllChildrens($t_object, $request_url, $padding,$access)
		{
			$o_db = new Db();
			$object_result = $o_db->query("SELECT o.object_id,ol.name,lt.item_value,orr.media
						FROM  `ca_objects` o
						INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
						INNER JOIN ca_list_items lt ON lt.item_id=o.type_id 
						LEFT JOIN  `ca_objects_x_object_representations` oor ON oor.object_id = o.object_id AND oor.is_primary=1
						LEFT JOIN  `ca_object_representations` orr ON orr.representation_id = oor.representation_id AND orr.deleted=0
						WHERE o.deleted=0 AND o.access IN ({$access}) AND o.parent_id={$t_object}
					    ORDER BY ol.name_sort");

			while ($object_result->nextRow())
			{

				$record = $object_result->getRow();
				print "<tr>";
				print "<td style='padding-left:" . $padding . "px;'>";
				if ($object_result->getMediaUrl('media', 'thumbnail') != '')
					print "<a class='preview'  href='javascript://;' rel=" . $object_result->getMediaUrl('media','medium') . " title='{$record['name']}'><img src='" . $object_result->getMediaUrl('media', 'thumbnail') . "'  style='height:35px;padding-right:20px;float:left;' width='50' /></a>";
				else
					print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";
				print "<div style='padding-left:64px;'>" . caNavLink($request_url, $record['name'] . " ", '', 'Detail', 'Object', 'Show', array('object_id' => $record['object_id'])) . "</div></td>";
				print "<td>" . ucfirst($record['item_value']) . "</td>";
				print "</tr>";


				getAllChildrens($record['object_id'], $request_url, $padding + 25,$access);
				$i ++;
			}
		}
		?>
	</div>

<script type="text/javascript">
	imagePreview = function() {
		/* CONFIG */

		xOffset = 10;
		yOffset = 30;

		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result

		/* END CONFIG */
		$("a.preview").hover(function(e) {
			this.t = this.title;
			this.title = "";
			var c = (this.t != "") ? "<br/>" + this.t : "";
			$("body").append("<p id='preview'><img src='" + this.rel + "' alt='Image preview' />" + c + "</p>");
			$("#preview")
			.css("top", (e.pageY - xOffset-200) + "px")
			.css("left", (e.pageX + yOffset) + "px")
			.fadeIn("fast");
		},
		function() {
			this.title = this.t;
			$("#preview").remove();
		});
		$("a.preview").mousemove(function(e) {
			$("#preview")
			.css("top", (e.pageY - xOffset-200) + "px")
			.css("left", (e.pageX + yOffset) + "px");
		});
	};


	// starting the script on page load
	$(document).ready(function() {
		imagePreview();

	});
</script>
<style type="text/css">
		#preview{
			position:absolute;
			border:1px solid #ccc;
			background:#333;
			padding:5px;
			display:none;
			color:#fff;
		}
		pre{
			display:block;
			font:100% "Courier New", Courier, monospace;
			padding:10px;
			border:1px solid #bae2f0;
			background:#e3f4f9;	
			margin:.5em 0;
			overflow:auto;
			width:800px;
		}
	</style>