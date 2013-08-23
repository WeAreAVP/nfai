<?php
$collection = $this->getVar('collection');
$occurrence = $this->getVar('occurrence');
$entity = $this->getVar('entity');
$collection_list = $this->getVar('collection_list');
?>

<div class="span3 search-bar">
	<div class="searchbar-inner">
		<div class="searchbar-heading">FILTER OBJECTS</div> 
		<div class="searchbar-content">
			<div><label class="control-label searchbar-title" for="created_at">Keyword</label></div>
			<div class="input-append">
				<input style="width: 160px; border-right: none;" id="created_at" name="created_at" type="text" value="" onkeypress="onPressEnter(event);">
				<span class="add-on" style="background: white;" onclick="$('#created_at').focus();">
					<i class="icon-search"></i>
				</span>
			</div>
		</div>
		<div class="searchbar-content">
			<div><label class="control-label searchbar-title" for="created_at">Object Type</label></div>
			<div class="searchbar-items">
				<div><input type="checkbox" /><span>Collection</span></div>
				<div><input type="checkbox" /><span>Item</span></div>
			</div>
			
		</div>
		<div class="searchbar-content">
			<div><label class="control-label searchbar-title" for="created_at">Individual, Organization, Meeting</label></div>
			<div class="searchbar-items">
				<?php
				print $entity;
				?>


			</div>
			<div class="searchbar-item-more"><a>MORE</a></div>
		</div>
		<div class="searchbar-content">
			<div><label class="control-label searchbar-title" for="created_at">Repository</label></div>
			<div class="searchbar-items">
				<?php
				print $occurrence;
				?>
			</div>
			<div class="searchbar-item-more"><a>MORE</a></div>
		</div>
		<div class="searchbar-content">
			<div><label class="control-label searchbar-title" for="created_at">Collection</label></div>
			<div class="searchbar-items">
				<?php
				print $collection;
				?>
			</div>
			<div class="searchbar-item-more"><a>MORE</a></div> 
		</div>

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
			foreach ($collection_list as $object_id)
			{
				$object = new ca_objects($object_id);
				$va_rep = $object->getPrimaryRepresentation(array('thumbnail','medium'), null);


				print "<tr>";
				print "<td>";
				if ($va_rep['urls']['thumbnail'] != '')
					print "<a href='javascript://;' rel=".$va_rep['urls']['medium']." class='preview' title='".$object->get('ca_objects.preferred_labels.name')."'><img src='" . $va_rep['urls']['thumbnail'] . "'  style='height:35px;padding-right:20px;float:left;' width='50' /></a>";
				else
					print "<div style='height:35px;width:50px;padding-left:5px;padding-right:20px;float:left;' ></div>";
				print "</td>";
//				print "<td>{$object->get('ca_objects.preferred_labels.name')}</td>";
				print "<td>";
				print caNavLink($this->request, $object->get('ca_objects.preferred_labels.name'), '', 'Detail', 'Object', 'Show', array('object_id' => $object_id));
				print "</td>";
				print "<td>{$object->getTypeName()}</td>";
				print "</tr>";
			}
			?>



		</tbody>
	</table>
</div>
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
					});
</script>