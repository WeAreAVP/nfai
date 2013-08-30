<?php
$isCollection = $this->getVar('isCollection');
$isOccurrence = $this->getVar('isOccurrence');
$isEntity = $this->getVar('isEntity');
$collection = $this->getVar('collection');

$occurrence = $this->getVar('occurrence');
$entity = $this->getVar('entity');
$collection_list = $this->getVar('collection_list');
$collection_show = $this->getVar('show_more_collection');
$occurence_show = $this->getVar('show_more_occurrence');
$entity_show = $this->getVar('show_more_entity');
$isAjax = $this->getVar('isAjax');
$parent_facet = $this->getVar('parent_facet');
if ( ! $isAjax)
{
	?>

	<div id="append_facet_result">
	<?php } ?>
	<div class="span3 search-bar">
		<div class="searchbar-inner">
			<div class="searchbar-heading">FILTER OBJECTS</div> 
			<div>
				<?php
				if (isset($_SESSION['keyword']) && !empty($_SESSION['keyword']) && count($_SESSION['keyword']) > 0)
				{
					?>	
					<div id="entity_main">
						<div class="filter-fileds"><b>KEYWORD</b></div>
						<?php
						
						foreach ($_SESSION['keyword'] as $key=>$value)
						{
							
						?>
							<div class="btn-img" id="facet_type_<?php echo $value->value ?>" ><span class="search_keys"><?php echo ucfirst($value->value); ?></span><i class="icon-remove-sign" style="float: right;cursor: pointer;" onclick="removeKeywordFilter('<?php print $key; ?>');"></i></div>
						<?php }  ?>
					</div>
				<?php } ?>
				<?php
				if (isset($_SESSION['type']) && count($_SESSION['type']) > 0)
				{
					?>	
					<div id="entity_main">
						<div class="filter-fileds"><b>TYPE</b></div>
						<?php
						foreach ($_SESSION['type'] as $value)
						{
							?>
							<div class="btn-img" id="facet_type_<?php echo $value ?>" ><span class="search_keys"><?php echo ucfirst($value); ?></span><i class="icon-remove-sign" style="float: right;cursor: pointer;" onclick="removeFilter('type_<?php print $value; ?>', 'type');"></i></div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php
				if (isset($_SESSION['entity']) && count($_SESSION['entity']) > 0)
				{
					?>	
					<div id="entity_main">
						<div class="filter-fileds"><b>Individual, Organization, Meeting</b></div>
						<?php
						foreach ($_SESSION['entity'] as $value)
						{
							?>
							<div class="btn-img" id="facet_entity_<?php echo $value['id'] ?>" ><span class="search_keys"><?php echo $value['name']; ?></span><i class="icon-remove-sign" style="float: right;cursor: pointer;" onclick="removeFilter('entity_<?php print $value['id']; ?>', 'entity');"></i></div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php
				if (isset($_SESSION['occurence']) && count($_SESSION['occurence']) > 0)
				{
					?>	
					<div id="occurence_main">
						<div class="filter-fileds"><b>Repository</b></div>
						<?php
						foreach ($_SESSION['occurence'] as $value)
						{
							?>
							<div class="btn-img" id="facet_occurence_<?php echo $value['id'] ?>" ><span class="search_keys"><?php echo $value['name']; ?></span><i class="icon-remove-sign" style="float: right;cursor: pointer;" onclick="removeFilter('occurrence_<?php print $value['id']; ?>', 'occurrence');"></i></div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php
				if (isset($_SESSION['collection']) && count($_SESSION['collection']) > 0)
				{
					?>	
					<div id="collection_main">
						<div class="filter-fileds"><b>Collection</b></div>
						<?php
						foreach ($_SESSION['collection'] as $value)
						{
							?>
							<div class="btn-img" id="facet_object_<?php echo $value['id'] ?>" ><span class="search_keys"><?php echo $value['name']; ?></span><i class="icon-remove-sign" style="float: right;cursor: pointer;" onclick="removeFilter('object_<?php print $value['id']; ?>', 'collection');"></i></div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<form id="facet_form" method="POST" action="" onsubmit="return false;">
				<div class="searchbar-content">
					<div><label class="control-label searchbar-title" for="created_at">Keyword</label></div>
					<div class="input-append">
						<input style="width: 160px; border-right: none;" id="keyword_search" name="keyword_search" type="text" value="" onkeypress="onPressEnter(event);">
						<span class="add-on" style="background: white;" onclick="$('#keyword_search').focus();">
							<i class="icon-search"></i>
						</span>
					</div>
				</div>
				<?php
				if (count($collection_list) > 0)
				{
					?>
					<div class="searchbar-content">
						<div><label class="control-label searchbar-title" for="created_at">Object Type</label></div>
						<div class="searchbar-items">
							<div><input type="checkbox" id="type_collection" name="type[]" <?php
								if ( ! empty($_SESSION['type']) && in_array('collection', $_SESSION['type']))
								{
									print 'checked="checked"';
								}
								?> value="collection" /><span>Collection</span></div>
							<div><input type="checkbox" id="type_item" name="type[]" <?php
								if ( ! empty($_SESSION['type']) && in_array('item', $_SESSION['type']))
								{
									print 'checked="checked"';
								}
								?> value="item" /><span>Item</span></div>
						</div>

					</div>
				<?php } ?>
				<?php
				if ($isEntity)
				{
					?>
					<div class="searchbar-content">
						<div><label class="control-label searchbar-title" for="created_at">Individual, Organization, Meeting</label></div>
						<div class="searchbar-items">
							<?php
							print $entity;
							?>


						</div>
						<?php
						if ($entity_show)
						{
							?>
							<div class="searchbar-item-more"><a href="#entitySearchModal" role="button"  data-toggle="modal">MORE</a></div> 
						<?php } ?>

					</div>
				<?php } ?>
				<?php
				if ($isOccurrence)
				{
					?>
					<div class="searchbar-content">
						<div><label class="control-label searchbar-title" for="created_at">Repository</label></div>
						<div class="searchbar-items">
							<?php
							print $occurrence;
							?>
						</div>
						<?php
						if ($occurence_show)
						{
							?>
							<div class="searchbar-item-more"><a href="#occurenceSearchModal" role="button"  data-toggle="modal">MORE</a></div> 
						<?php } ?>

					</div>
				<?php } ?>
				<?php
				if ($isCollection)
				{
					?>
					<div class="searchbar-content">
						<div><label class="control-label searchbar-title" for="created_at">Collection</label></div>
						<div class="searchbar-items">
							<?php
							print $collection;
							?>
						</div>
						<?php
						if ($collection_show)
						{
							?>
							<div class="searchbar-item-more"><a href="#collectionSearchModal" role="button"  data-toggle="modal">MORE</a></div> 
						<?php } ?>
					</div>
				<?php } ?>
				<input type="hidden" value="<?php print $_SESSION['parent_facet']; ?>" name="parent_facet" id="parent_facet"/>
				<input type="hidden" value="<?php print $_SESSION['total_count']; ?>" name="total_checked" id="total_checked"/>
				<input type="hidden" value="<?php print htmlentities(json_encode($_SESSION['keyword'])); ?>" name="facet_keyword_search" id="facet_keyword_search"/>
			</form>
		</div>
	</div>
	<div class="span9 object-list">
		<?php
		if (count($collection_list) > 0)
		{
			?>
			<table class="table hierarchy-table">
				<thead>
					<tr>
						<th></th>
						<th>Title</th>
						<th>Type</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($collection_list as $object_id)
					{
						if (empty($_SESSION['type']) || in_array('collection', $_SESSION['type']))
						{
							print "<tr>";
							print "<td>";
							if ($object_id['thumbnail'] != '')
								print "<a href='javascript://;' rel=" . $object_id['medium'] . " class='preview' title='" . $object_id['name'] . "'><img src='" . $object_id['thumbnail'] . "'  style='height:35px;padding-right:20px;float:left;' width='50' /></a>";
							else
								print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";

							print "</td>";
							print "<td>";
							print caNavLink($this->request, $object_id['name'], '', 'Detail', 'Object', 'Show', array('object_id' => $object_id['id']));
							print "</td>";
							print "<td>{$object_id['type']}</td>";
							print "</tr>";
						}
						if (empty($_SESSION['type']) || in_array('item', $_SESSION['type']))
						{
							if (count($object_id['items']) > 0)
							{
								foreach ($object_id['items'] as $key => $value)
								{
									print "<tr>";
									print "<td>";
									if ($value['thumbnail'] != '')
										print "<a href='javascript://;' rel=" . $value['medium'] . " class='preview' title='" . $value['name'] . "'><img src='" . $value['thumbnail'] . "'  style='height:35px;padding-right:20px;float:left;' width='50' /></a>";
									else
										print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";

									print "</td>";
									print "<td>";
									print caNavLink($this->request, $value['name'], '', 'Detail', 'Object', 'Show', array('object_id' => $value['id']));
									print "<div style='color:#999999;'><i>{$object_id['type']}: {$object_id['name']}</i></div>";
									print "</td>";
									print "<td>{$value['type']}</td>";
									print "</tr>";
								}
							}
						}
					}
					?>



				</tbody>
			</table>
			<?php
		}
		else
		{
			print '<div class="no-result">No Result.</div>';
		}
		?>
	</div>
	<?php
	if ( ! $isAjax)
	{
		?>
	</div>
	<?php
} if ( ! $isAjax)
{
	?>
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
	<script type="text/javascript">
								function bindEvents() {
									$('input[name="occurrence[]"]').click(function() {
										checkParentFacet('occurrence', $(this).attr('checked'));
									});
									$('input[name="entity[]"]').click(function() {
										checkParentFacet('entity', $(this).attr('checked'));
									});
									$('input[name="collection[]"]').click(function() {
										checkParentFacet('collection', $(this).attr('checked'));
									});
									$('input[name="type[]"]').click(function() {
										checkParentFacet('type', $(this).attr('checked'));
									});
								}

								function checkParentFacet(type, isChecked) {
									totalChecked = $('#total_checked').val();
									total = $('input:checked').length;
									if (total == 0)
										$totalChecked = 0;
									if (isChecked == 'checked')
										totalChecked++;
									else
										totalChecked--;

									$('#total_checked').val(totalChecked);

									if ($('#parent_facet').val() == '' && totalChecked == 1)
										$('#parent_facet').val(type);
									else if (totalChecked == 0)
										$('#parent_facet').val('');
									search_facet();
								}


								function onPressEnter(e) {
									if (e.keyCode == 13) {
										if ($('#facet_keyword_search').val() != '' && $('#facet_keyword_search').val()!='""') {
											Filters = JSON.parse($('#facet_keyword_search').val());
										}
										else {
											Filters = new Array();
										}
										for (x in Filters) {
											if (Filters[x].value == $('#keyword_search').val()) {
												alert($('#keyword_search').val() + " filter is already applied.");
												return false;
											}
										}
										var temp = {};
										temp.value = $('#keyword_search').val();
										Filters.push(temp);
										$('#facet_keyword_search').val(JSON.stringify(Filters));

										search_facet();
									}
								}
								function removeKeywordFilter(index){
									Filters = JSON.parse($('#facet_keyword_search').val());
									delete (Filters[index]);
									Filters.splice(index, 1);
									$('#facet_keyword_search').val(JSON.stringify(Filters));
									search_facet();
								}
								function removeFilter(elementID, type) {
									$('#' + elementID).prop('checked', false);
									checkParentFacet(type);
								}
								function search_facet() {
									$.blockUI({
										css: {
											border: 'none',
											padding: '15px',
											backgroundColor: '#000',
											'-webkit-border-radius': '10px',
											'-moz-border-radius': '10px',
											opacity: .5,
											color: '#fff',
											zIndex: 999999,
										},
										message: '<div style="color:white;font-size:20px;">Please Wait...</div>'

									});

									$.ajax({
										type: 'POST',
										url: '/index.php/Browse/Search',
										data: $('#facet_form').serialize(),
										dataType: 'html',
										success: function(result, textStatus, request)
										{
											$('#append_facet_result').html(result);
											imagePreview();
											bindEvents();
											$.unblockUI();

										}

									});
								}
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
										.css("top", (e.pageY - xOffset) + "px")
										.css("left", (e.pageX + yOffset) + "px")
										.fadeIn("fast");
									},
									function() {
										this.title = this.t;
										$("#preview").remove();
									});
									$("a.preview").mousemove(function(e) {
										$("#preview")
										.css("top", (e.pageY - xOffset) + "px")
										.css("left", (e.pageX + yOffset) + "px");
									});
								};


								// starting the script on page load
								$(document).ready(function() {
									imagePreview();
									bindEvents();
								});
	</script>
<?php } ?>