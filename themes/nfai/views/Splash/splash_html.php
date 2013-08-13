<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Splash/splash_html.php : 
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

$t_object = new ca_objects();

$va_item_ids = $this->getVar('featured_content_slideshow_id_list');
$va_item_media = $t_object->getPrimaryMediaForIDs($va_item_ids, array("medium", "thumbnail", "small"));
$va_item_labels = $t_object->getPreferredDisplayLabelsForIDs($va_item_ids);
?>
<div id="splashBrowsePanel" class="modal hide" style="z-index:1000;">
	<div id="splashBrowsePanelContent">
	</div>
</div>
<script type="text/javascript">
	var caUIBrowsePanel = caUI.initBrowsePanel({facetUrl: '<?php print caNavUrl($this->request, '', 'Browse', 'getFacet'); ?>'});
</script>
<div style="margin: 0px 20px 20px 250px;">
	<h3>
		<?php print $this->render('Splash/splash_intro_text_html.php'); ?> 
	</h3>
	<div class="hpRss"><?php print caNavLink($this->request, '<img src="' . $this->request->getThemeUrlPath(true) . '/graphics/feed.gif" border="0" title="' . _t('Get alerted to newly added items by RSS') . '" width="14" height="14"/> ' . _t('Get alerted to newly added items by RSS'), 'caption', '', 'Feed', 'recentlyAdded'); ?></div>
</div>
<div class="clearfix"></div>
<div>
	<h1 class="splash-feature">Featured Objects from the Archive</h1>
	<ul id="mycarousel" class="jcarousel-skin-tango">
		<?php
		foreach ($va_item_media as $vn_object_id => $va_media)
		{
			$vs_image_tag = '<img src="' . $va_media["urls"]["medium"] . '" style="height:170px;"/><div style="padding-top:15px;"><b>' . $va_item_labels[$vn_object_id] . '</b></div>';
			print '<li>' . caNavLink($this->request, $vs_image_tag, '', 'Detail', 'Object', 'Show', array('object_id' => $vn_object_id)) . '</li>';
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		jQuery('#mycarousel').jcarousel({
			auto: 5,
			wrap: 'last',
			initCallback: mycarousel_initCallback,
			visible: 3,
			animation: 'slow'
		});
	});
	function mycarousel_initCallback(carousel)
	{
		// Disable autoscrolling if the user clicks the prev or next button.
		carousel.buttonNext.bind('click', function() {
			carousel.startAuto(0);
		});

		carousel.buttonPrev.bind('click', function() {
			carousel.startAuto(0);
		});

		// Pause autoscrolling if the user moves with the cursor over the clip.
		carousel.clip.hover(function() {
			carousel.stopAuto();
		}, function() {
			carousel.startAuto();
		});
	}
	;

</script>
<style type="text/css">

	.jcarousel-skin-tango .jcarousel-container-horizontal {
		width: 90%;
	}

	.jcarousel-skin-tango .jcarousel-clip-horizontal {
		width: 100%;
	}
</style>
<div class="clearfix"></div>
