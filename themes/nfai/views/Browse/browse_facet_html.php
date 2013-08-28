<?php
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
				if (isset($_SESSION['type']) && count($_SESSION['type']) > 0)
				{
					?>	
					<div id="entity_main">
						<div class="filter-fileds"><b>TYPE</b></div>
						<?php
						foreach ($_SESSION['type'] as $value)
						{
							?>
							<div class="btn-img" id="type_<?php echo $value ?>" ><span class="search_keys"><?php echo ucfirst($value); ?></span><i class="icon-remove-sign" style="float: right;" onclick=""></i></div>
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
							<div class="btn-img" id="entity_<?php echo $value['id'] ?>" ><span class="search_keys"><?php echo $value['name']; ?></span><i class="icon-remove-sign" style="float: right;" onclick=""></i></div>
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
							<div class="btn-img" id="occurence_<?php echo $value['id'] ?>" ><span class="search_keys"><?php echo $value['name']; ?></span><i class="icon-remove-sign" style="float: right;" onclick=""></i></div>
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
							<div class="btn-img" id="collection_<?php echo $value['id'] ?>" ><span class="search_keys"><?php echo $value['name']; ?></span><i class="icon-remove-sign" style="float: right;" onclick=""></i></div>
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
				<div class="searchbar-content">
					<div><label class="control-label searchbar-title" for="created_at">Object Type</label></div>
					<div class="searchbar-items">
						<div><input type="checkbox" name="type[]" value="collection" /><span>Collection</span></div>
						<div><input type="checkbox" name="type[]" value="item" /><span>Item</span></div>
					</div>

				</div>
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
				<input type="hidden" value="" name="parent_facet" id="parent_facet"/>
			</form>
		</div>
	</div>
	<div class="span9 object-list">
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
				if (count($collection_list) > 0)
				{
					foreach ($collection_list as $object_id)
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
						if (count($object_id['items']) > 0)
						{
							foreach ($object_id['items'] as $key => $value)
							{
								print "<tr>";
								print "<td>";
								if ($object_id['thumbnail'] != '')
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
				else
				{
					print '<tr><td colspan="3">No Result.</td></tr>';
				}
				?>



			</tbody>
		</table>
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
				totalChecked = 0;
				function bindEvents() {
					$('input[name="occurrence[]"]').click(function() {
						checkParentFacet('occurrence');
					});
					$('input[name="entity[]"]').click(function() {
						checkParentFacet('entity');
					});
					$('input[name="collection[]"]').click(function() {
						checkParentFacet('collection');
					});
					$('input[name="type[]"]').click(function() {
						checkParentFacet('type');
					});
				}

				function checkParentFacet(type) {
					total = $('input:checked').length;
					if (total == 0)
						$totalChecked = -1;
					if (totalChecked == 0)
						$('#parent_facet').val(type);
					else if (totalChecked == -1)
						$('#parent_facet').val('');
					totalChecked++;
					search_facet();
				}


				function onPressEnter(e) {
					if (e.keyCode == 13) {
						// send request again 
					}
				}
				function search_facet() {
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