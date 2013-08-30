<div id="splashBrowsePanel" class="modal hide" style="z-index:1000;">
	<div id="splashBrowsePanelContent">
	</div>
</div>
<script type="text/javascript">
	var caUIBrowsePanel = caUI.initBrowsePanel({facetUrl: '<?php print caNavUrl($this->request, '', 'Browse', 'getFacet'); ?>'});
</script>

<div id="about_imanges" style="height: 410px;">
	<div style='margin-top:10px;'>
		<div id='featuredScroll' style="width: 930px;"> 
			<a><img src="<?php print $this->request->getThemeUrlPath() ?>/graphics/vermont-folklife-center.jpg" alt="" style="max-height: 330px;">
			</a>
		</div>
		<div id='featuredScrollCaption'>
			<a>Vermont Folklife Center front entrance.</a>
		</div>
	</div>
	<div style='margin-top:10px;'>
		<div id='featuredScroll' style="width: 930px;"> 
			<a><img src="<?php print $this->request->getThemeUrlPath() ?>/graphics/Western_Folklife_Center.jpg" alt="" style="max-height: 330px;">
			</a>
		</div>
		<div id='featuredScrollCaption' >
			<a>The Western Folklife Center, home of the annual National Cowboy Poetry Gathering, occupies an historic hotel building in Elko, Nevada. The renovated space features a modern exhibition gallery, performance theater, gift shop, archives and library, staff offices, and a functioning saloon. Photo by Steve Green.
			</a>
		</div>
	</div>
	<div style='margin-top:10px;'>
		<div id='featuredScroll' style="width: 930px;"> <a><img src="<?php print $this->request->getThemeUrlPath() ?>/graphics/no-alone.jpg" alt="" style="max-height: 330px;"></a></div>
		<div id='featuredScrollCaption'><a>I realize we are not alone in this necessity!</a></div>

	</div>

</div>





<h1 style="line-height: 1.8em;word-spacing: 4px;color: #3D3D3D;">The National Folklore Archives Initiative (NFAI) is an American Folklore Society- managed effort to document and provide access to information about folklore archival collections.</h1>
<div class="textContent aboutContent">
<!--	<p>The National Folklore Archives Initiative (NFAI) is an American
Folklore Society-managed effort to document and provide access to
information about folklore archival collections held by folklore
programs at academic institutions, community-based cultural and ethnic
organizations, non-profit organizations, and state government-based
arts and cultural agencies in the United States.</p>-->

	<p>The NFAI is made possible by generous support from the Humanities
		Collections and Reference Resources program of the <a href="http://www.neh.gov/grants/preservation/humanities-collections-and-reference-resources" target="_blank">Division of
			Preservation and Access of the National Endowment for the Humanities
		</a>.
	</p>

	<p>Folklore archival collections—unpublished multi-format collections of
		materials created in the field that document traditional cultural
		expressions and knowledge—comprise one of our nation’s most valuable
		cultural resources, but scholars, public humanists, teachers,
		students, and community members can access these materials only with
		difficulty. These collections exist in a wide variety of institutions.
		Many of them are not formal archives or libraries, and lack the means
		to build and maintain an infrastructure to coordinate this work.</p>

	<p>To begin to address these issues, NFAI has developed this open-access
		Web-based database, which provides information about folklore archival
		repositories and collections across the United States. It includes a
		cataloging template based on national descriptive standards that is
		designed for the needs of multi-format folklore archival collections.</p>

	<p>Archivists at 12 test sites are now testing this cataloging tool by
		entering information about their collections:</p>

	<p>Academic Programs<br/>
		<a href="http://umaine.edu/folklife/" target="_blank">Maine Folklife Center, University of Maine, Orono</a><br/>
		<a href="http://museum.msu.edu/" target="_blank">Michigan State University Museum, East Lansing</a><br/>
		<a href="http://csumc.wisc.edu/" target="_blank">Center for the Study of Upper Midwestern Cultures, University of
			Wisconsin, Madison</a><br/>
		<a href="http://www.oregonartscommission.org/programs/oregon-folklife-program" target="_blank">Oregon Folklife Program, University of Oregon, Eugene
		</a><br/></p>

	<p>Non-Profit Organizations<br/>
		<a href="http://www.actaonline.org/" target="_blank">Alliance for California Traditional Arts, Fresno</a><br/>
		<a href="http://citylore.org/" target="_blank">City Lore, New York, New York</a><br/>
		<a href="http://www.culturalpartnerships.org/" target="_blank">Institute for Cultural Partnerships, Harrisburg, Pennsylvania</a><br/>
		<a href="http://www.folkloreproject.org/" target= "_blank">Philadelphia Folklore Project</a><br/>
		<a href="http://www.vermontfolklifecenter.org/" target="_blank">Vermont Folklife Center, Middlebury</a><br/>
		<a href="http://www.westernfolklife.org/" target="_blank">Western Folklife Center, Elko, Nevada</a><br/></p>

	<p>State Arts Agencies<br/>
		<a href="http://www.louisianafolklife.org/" target="_blank">Louisiana Folklife Program, Louisiana Arts Council, Baton Rouge</a><br/>
		<a href="http://www.msac.org/mdfolklife" target="_blank">Maryland Folklife Program, Maryland Arts Council, Baltimore</a><br/></p>

	<p>In addition, the NFAI project team is carrying out a national survey
		to gather basic data about the folklore archival collections, many of
		them “hidden,” of a universe of several hundred folklore programs at
		academic institutions, community-based cultural and ethnic
		organizations, non-profit organizations, and state government-based
		cultural agencies across the US. We will include this information in
		the database as a first step toward improving the discoverability and
		preservation of these collections.</p>

	<p>Future plans for the NFAI project include:</p>
	<p>* Engaging and training more repositories to use the tool<br/>
		* Creating long-term scalable solutions for user training<br/>
		* Automating the ingest of existing archival records<br/>
		* Completing of the development of an organizational infrastructure to<br/>
		manage, govern, support, and sustain the NFAI for the long term<br/></p>

	<p>For more information on the NFAI project, please contact co-directors
		<a href="mailto:wfcarchives@earthlink.net" target="_blank">Steve Green</a> of the Western Folklife Center, 
		<a href="mailto:akolovos@vermontfolklifecenter.org" target="_blank">Andy Kolovos</a> of the Vermont Folklife
		Center, or <a href="mailto:lloyd.100@osu.edu" target="_blank">Tim Lloyd</a> of the
		American Folklore Society.</p>

</div>
<script>
	$(document).ready(function() {
		$('#about_imanges').cycle({
			fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
			speed: 4000,
			timeout: 4000
		});
	});

</script>