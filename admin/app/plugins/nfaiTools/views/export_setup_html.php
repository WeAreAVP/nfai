<h1>NFAI EAD exporter</h1>

<?php
$vn_num = $this->getVar("num_parents");
$o_result = $this->getVar("result");
$vs_search = ($this->getVar('search') ? $this->getVar('search') : '');

print caNavButton($this->request, __CA_NAV_BUTTON_SAVE__, _t("Download full export"), $this->request->getModulePath(), $this->request->getController(), 'Export', array());

?>

<p>
Note that there are <?php print $vn_num; ?> hierarchy roots in the current data set.
Depending on the load of the system, creating your export may take a couple of minutes.
Your browser should automatically download the zip-compressed archive afterwards.
</p>
<p>
The search field below can be used to find objects for a single collection level export. Note that the search will only return collection records!
</p>

<?php

print caFormTag($this->request,'Index','nfaiSearchForm', null, 'POST', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
print caHTMLTextInput('nfaiSearch',array('width' => 40, 'height' => 1, 'value' => $vs_search));
print caFormSubmitButton($this->request,__CA_NAV_BUTTON_SEARCH__,'Search','nfaiSearchForm');

?>

</form>

<?php if($o_result): ?>

<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#nfaiRootList').caFormatListTable();
	});
/* ]]> */
</script>

<div class="sectionBox">

	<table id="nfaiRootList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
		<thead>
		<tr>
			<th>
				<?php _p('Label'); ?>
			</th>
			<th>
				<?php _p('Identifier'); ?>
			</th>
			<th>
				<?php _p('Alternate identifier(s)'); ?>
			</th>
			<th>
				<?php _p('Repository'); ?>
			</th>
			<th class="{sorter: false} list-header-nosort" style="width: 20px">&nbsp;</th>
		</tr>
		</thead>
		<tbody>

<?php

	while($o_result->nextHit()){
?>
			<tr>
				<td><?php print $o_result->get('ca_objects.preferred_labels'); ?></td>
				<td>
					<?php print caEditorLink($this->request,$o_result->get('ca_objects.idno'),'','ca_objects',$o_result->get('ca_objects.object_id')); ?>
				</td>
				<td><?php print $o_result->get('ca_objects.alternate_idnos',array('template' => '^id_value')); ?></td>
				<td><?php print $o_result->get('ca_occurrences.preferred_labels'); ?></td>
				<td>
					<?php print caNavButton($this->request, __CA_NAV_BUTTON_DOWNLOAD__, _t("Edit"), $this->request->getModulePath(), $this->request->getController(), 'Download', array('root_id' => $o_result->get('ca_objects.object_id')), array(), array('icon_position' => __CA_NAV_BUTTON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true)); ?>
				</td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>

<br />
<br />
<br />

</div>

<?php endif; ?>