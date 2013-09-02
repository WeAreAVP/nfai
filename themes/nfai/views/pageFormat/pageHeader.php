<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<style type="text/css">
			@font-face {
				font-family: 'Conv_Aller';
				src: url('<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts/Aller_Bd.ttf');

				font-weight: normal;
				font-style: normal;
			}

			@font-face {
				font-family: 'HelveticaNeueLight';
				src: url('<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts/HelveticaNeueLight.ttf');
				font-weight: normal;
				font-style: normal;
			}
			@font-face {
				font-family: 'HN';
				src: url('<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts/HelveticaNeue.ttf');
			}
			@font-face {
				font-family: 'H-bold';
				src: url('<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts/helvatica-bold.ttf');
			}
			@font-face {
				font-family: 'H-regular';
				src: url('<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts/helvatica-reg.ttf');
			}

			@font-face {
				font-family: 'HelveticaNeueBold';
				src: url('<?php print $this->request->getThemeUrlPath(true); ?>/css/fonts/helvetica-neue-bold.ttf');
			}
		</style>
		<title><?php print $this->request->config->get('html_page_title'); ?></title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php print MetaTagManager::getHTML(); ?>
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/bootstrap.css?<?php echo time(); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/style.css" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/global.css" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/skin.css" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/sets.css" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/bookmarks.css" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/tablesorter/style.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/videojs/video-js.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-jplayer/jplayer.blue.monday.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-autocomplete/jquery.autocomplete.css" type="text/css" media="screen" />
		<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php print $this->request->getThemeUrlPath(true); ?>/css/iestyles.css" />
		<![endif]-->

		<!--[if (!IE)|(gte IE 8)]><!-->
		<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/viewer-datauri.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/plain-datauri.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/plain.css" media="screen" rel="stylesheet" type="text/css" />
		<!--<![endif]-->
		<!--[if lte IE 7]>
		<link href="<?php print $this->request->getBaseUrlPath(); ?>/viewer.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?php print $this->request->getBaseUrlPath(); ?>/plain.css" media="screen" rel="stylesheet" type="text/css" />
		<![endif]-->
		<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-tileviewer/jquery.tileviewer.css" type="text/css" media="screen" />
		<?php
		print JavascriptLoadManager::getLoadHTML($this->request->getBaseUrlPath());
		?>
				<!--<script type="text/javascript" src="<?php print $this->request->getBaseUrlPath(); ?>/js/bootstrap/bootstrap.js"></script>-->
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#quickSearch').searchlight('<?php print $this->request->getBaseUrlPath(); ?>/index.php/Search/lookup', {showIcons: false, searchDelay: 100, minimumCharacters: 3, limitPerCategory: 3});
			});
			// initialize CA Utils
			caUI.initUtils();
		</script>
	</head>
	<body>
		<div id="topBar">
			<?php
			if ( ! $this->request->config->get('dont_allow_registration_and_login'))
			{
				if ($this->request->isLoggedIn())
				{
					$o_client_services_config = caGetClientServicesConfiguration();
					if ((bool) $o_client_services_config->get('enable_user_communication'))
					{
						//
						// Unread client communications
						//
						$t_comm = new ca_commerce_communications();
						$va_unread_messages = $t_comm->getMessages(array('unreadOnly' => true, 'user_id' => $this->request->getUserID()));

						$va_message_set_ids = array();
						foreach ($va_unread_messages as $vn_transaction_id => $va_messages)
						{
							$va_message_set_ids[] = $va_messages[0]['set_id'];
						}
					}

					if ( ! $this->request->config->get('disable_my_collections'))
					{
						# --- get all sets for user
						$t_set = new ca_sets();
						$va_sets = caExtractValuesByUserLocale($t_set->getSets(array('table' => 'ca_objects', 'user_id' => $this->request->getUserID())));
						if (is_array($va_sets) && (sizeof($va_sets) > 1))
						{
							print "<div id='lightboxLink'>
										<a href='#' onclick='$(\"#lightboxList\").toggle(0, function(){
																								if($(\"#lightboxLink\").hasClass(\"lightboxLinkActive\")) {
																									$(\"#lightboxLink\").removeClass(\"lightboxLinkActive\");
																								} else {
																									$(\"#lightboxLink\").addClass(\"lightboxLinkActive\");
																								}
																								});')>Lightbox</a>";
							if (is_array($va_message_set_ids) && sizeof($va_message_set_ids))
							{
								print " <img src='" . $this->request->getThemeUrlPath() . "/graphics/icons/envelope.gif' border='0'>";
							}
							print "<div id='lightboxList'><b>" . _t("your lightboxes") . ":</b><br/>";
							foreach ($va_sets as $va_set)
							{
								print caNavLink($this->request, ((strlen($va_set["name"]) > 30) ? substr($va_set["name"], 0, 30) . "..." : $va_set["name"]), "", "", "Sets", "Index", array("set_id" => $va_set["set_id"]));
								if (is_array($va_message_set_ids) && in_array($va_set["set_id"], $va_message_set_ids))
								{
									print " <img src='" . $this->request->getThemeUrlPath() . "/graphics/icons/envelope.gif' border='0'>";
								}
								print "<br/>";
							}
							print "</div>";
							print "</div>";
						}
						else
						{
							print caNavLink($this->request, _t("Lightbox"), "", "", "Sets", "Index");
							if (is_array($va_message_set_ids) && sizeof($va_message_set_ids))
							{
								print " <img src='" . $this->request->getThemeUrlPath() . "/graphics/icons/envelope.gif' border='0'>";
							}
						}
					}

					if ((bool) $o_client_services_config->get('enable_my_account'))
					{
						$t_order = new ca_commerce_orders();
						if ($vn_num_open_orders = sizeof($va_orders = $t_order->getOrders(array('user_id' => $this->request->getUserID(), 'order_status' => array('OPEN', 'SUBMITTED', 'IN_PROCESSING', 'REOPENED')))))
						{
							print "<span style='color: #cc0000; font-weight: bold;'>" . caNavLink($this->request, _t("My Account (%1)", $vn_num_open_orders), "", "", "Account", "Index") . "</span>";
						}
						else
						{
							print caNavLink($this->request, _t("My Account"), "", "", "Account", "Index");
						}
					}

					if ($this->request->config->get('enable_bookmarks'))
					{
						print caNavLink($this->request, _t("My Bookmarks"), "", "", "Bookmarks", "Index");
					}
					print caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout");
				}
				else
				{
					print caNavLink($this->request, _t("Login/Register"), "", "", "LoginReg", "form");
				}
			}

			# Locale selection
			global $g_ui_locale;
			$vs_base_url = $this->request->getRequestUrl();
			$vs_base_url = ((substr($vs_base_url, 0, 1) == '/') ? $vs_base_url : '/' . $vs_base_url);
			$vs_base_url = str_replace("/lang/[A-Za-z_]+", "", $vs_base_url);

			if (is_array($va_ui_locales = $this->request->config->getList('ui_locales')) && (sizeof($va_ui_locales) > 1))
			{
				print caFormTag($this->request, $this->request->getAction(), 'caLocaleSelectorForm', null, 'get', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true));

				$va_locale_options = array();
				foreach ($va_ui_locales as $vs_locale)
				{
					$va_parts = explode('_', $vs_locale);
					$vs_lang_name = Zend_Locale::getTranslation(strtolower($va_parts[0]), 'language', strtolower($va_parts[0]));
					$va_locale_options[$vs_lang_name] = $vs_locale;
				}
				print caHTMLSelect('lang', $va_locale_options, array('id' => 'caLocaleSelectorSelect', 'onchange' => 'window.location = \'' . caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), array('lang' => '')) . '\' + jQuery(\'#caLocaleSelectorSelect\').val();'), array('value' => $g_ui_locale, 'dontConvertAttributeQuotesToEntities' => true));
				print "</form>\n";
			}
			?>
		</div><!-- end topbar -->
		<div style="width: 940px;margin: 0 auto;background-color: white;">
			<?php
			print caNavLink($this->request, "<img src='" . $this->request->getThemeUrlPath() . "/graphics/" . $this->request->config->get('header_img') . "' border='0'>", "", "", "", "");
			?>
		</div>
		<div class="navbar" style="width: 940px;margin: 0 auto;">
			<div class="navbar-inner">
				<!--<a class="brand" href="/">American Folklore Society</a>-->
				<div class="pull-left">
					<div id="">
						<?php
						$vs_base_url = $this->request->getRequestUrl();
						if ( ! strstr($vs_base_url, 'Browse/Search'))
						{
							?>
							<div>
								<div class="pull-left"><div class="btn-group">
										<button class="btn splash-css">Browse</button>
										<button class="btn dropdown-toggle" style="padding-bottom: 12px;" data-toggle="dropdown">
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li><a href="/index.php/Browse/Search" role="button" >General</a></li> 
											<li><a href="#collectionModal" role="button"  data-toggle="modal" data-backdrop="static" onclick="getAllCollections();">Collections</a></li> 
											<li><a href="#occuranceModal" role="button"  data-toggle="modal" data-backdrop="static" onclick="getAllRepository();">Repositories</a></li> 
											<!--<li><a href="#entitiesModal" role="button"  data-toggle="modal" data-backdrop="static" onclick="getAllEntities();">Individual, Organization<br/> or Meeting</a></li>--> 


											<!--<li><a href="javascript://" onclick='caUIBrowsePanel.showBrowsePanel("occurrence_facet_103");'>Repository</a></li>-->
											<!--<li><a href="javascript://" onclick='caUIBrowsePanel.showBrowsePanel("entity_facet");'>Individual, Organization<br/> or Meeting</a></li>-->

										</ul>
									</div>
								</div>
								<div class="pull-right">
									<!--<form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">-->
									<b class="custom-search splash-css" style="float: left;margin-top: 14px;">SEARCH:</b> 
									<!--<input type="text" class="span7" value="<?php print ($vs_search) ? $vs_search : ''; ?>" name="search"  id="quickSearch"  autocomplete="off"  style="margin: 5px 0 0 0;width: 570px;" onclick='jQuery("#quickSearch").select();' />-->
									<input type="text" class="span7" value="" name="search"  id="quickSearch"  autocomplete="off"  style="margin: 5px 0 0 0;width: 570px;" onkeyup="doSearch(event);" />


									<!--</form>-->

								</div>
							</div>
							<div class="clearfix"></div>
							<!--<hr style="margin: 16px 0 2px 0;"/>-->
							<?php
						}
//				print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/".$this->request->config->get('header_img')."' border='0'>", "", "", "", "");
						?>				
					</div>
				</div>
				<ul class="nav pull-right">
					<?php
					$class = '';
					$vs_base_url = $this->request->getRequestUrl();
					if (strstr($vs_base_url, 'About/Index'))
						$class = 'active';
					?>
					<li class="<?php echo $class; ?>"><?php
						print join(" ", $this->getVar('nav')->getHTMLMenuBarAsLinkArray());
						print $this->getVar('about');
						?></li>
				</ul>
			</div>
		</div>
		<div id="pageArea">
			<!-- end header -->
			<?php
// get last search ('basic_search' is the find type used by the SearchController)
			$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
			$vs_search = $o_result_context->getSearchExpression();
			?>
			<!--			<div id="nav">
							<div id="search"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
									<a href="#" style="position: absolute; z-index:1500; margin: 4px 0px 0px 132px;" name="searchButtonSubmit" onclick="document.forms.header_search.submit();
							return false;"><?php print "<img src='" . $this->request->getThemeUrlPath() . "/graphics/searchglass.gif' border='0'>"; ?></a>
									<input type="text" name="search" value=" " onclick='jQuery("#quickSearch").select();' id="quickSearch"  autocomplete="off" size="100"/>
								</form></div>
			<?php
			print join(" ", $this->getVar('nav')->getHTMLMenuBarAsLinkArray());
			?>
						</div> end nav -->


			<div id="collectionModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h1 id="myModalLabel">Browse Collections</h1>
				</div>
				<div class="modal-body" style="height: 300px;overflow: hidden;">
					<div id="collection_states_list" class="hide">
						<div style="margin-left: 10px;">
							<div style="float:left;margin: 10px 10px 0 0;color:#424242;"><b>Filter By:</b></div>
							<div class="btn-group">
								<button class="btn" style="color:#595959;">State</button>
								<button class="btn dropdown-toggle" style="padding-bottom: 12px;" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<div class="dropdown-menu" >
									<div id="collection_states_records" style="height: 100px;overflow: scroll;">
										<?php
										$states = caGetStateList();
										foreach ($states['US'] as $key => $value)
										{
											print '<div style="color:#424242;line-height:2.0em;font-size:12px;"><input type="checkbox" style="padding-top: 0;margin-top: -5px;margin-left: 8px;" value="' . $value . '" onclick="filterRecords(\'collection\');"/><span style="display:inline;padding: 3px 12px;" href="javascript://;">' . $key . '</span></div>';
										}
										?>
									</div>
									<div class="divider" style="margin: 0px 0px 4px 0px;"></div>
									<div><a onclick="filterRecords('collection', 1);" href="javascript://;" style="padding: 0px 17px;font-size: 13px;line-height: 2em;color:#0088cc;">Clear</a></div>
								</div>
							</div>
						</div>
					</div>
					<div id="collection_append" style="max-height: 270px;overflow-y: auto;margin-top: 10px;">Loading...</div>
					<div id="collection_no_result" class="hide no-result">No Result</div>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

				</div>
			</div>
			<div id="occuranceModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h1>Browse Repositories</h1>
				</div>
				<div class="modal-body" style="height: 300px;overflow: hidden;">
					<div id="occurance_states_list" class="hide">
						<div style="margin-left: 10px;">
							<div style="float:left;margin: 10px 10px 0 0;color:#424242;"><b>Filter By:</b></div>
							<div class="btn-group">
								<button class="btn"  style="color:#595959;">State</button>
								<button class="btn dropdown-toggle" style="padding-bottom: 12px;" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<div class="dropdown-menu" >
									<div id="occurance_states_records" style="height: 100px;overflow: scroll;">
										<?php
										$states = caGetStateList();
										foreach ($states['US'] as $key => $value)
										{
											print '<div style="color:#424242;line-height:2.0em;font-size:12px;"><input type="checkbox" style="padding-top: 0;margin-top: -5px;margin-left: 8px;" value="' . $value . '" onclick="filterRecords(\'occurance\');"/><span style="display:inline;padding: 3px 12px;" href="javascript://;">' . $key . '</span></div>';
										}
										?>
									</div>
									<div class="divider" style="margin: 0px 0px 4px 0px;"></div>
									<div><a onclick="filterRecords('occurance', 1);" href="javascript://;" style="padding: 0px 17px;font-size: 13px;line-height: 2em;color:#0088cc;">Clear</a></div>
								</div>
							</div>
						</div>
					</div>
					<div id="occurance_append" style="max-height: 270px;overflow-y: auto;margin-top: 10px;">Loading...</div>
					<div id="occurance_no_result" class="hide no-result">No Result</div>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

				</div>
			</div>
			<div id="entitiesModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h1>Browse Individuals, Organizations or Meetings</h1>
				</div>
				<div class="modal-body" style="height: 300px;">
					<div id="alphatbets_list" class="hide">
						<div style="margin-left: 10px;">
							<div style="float:left;margin: 10px 10px 0 0;color:#424242;"><b>Filter By:</b></div>
							<div class="btn-group">
								<button class="btn" style="color:#595959;">Alphabet</button>
								<button class="btn dropdown-toggle" style="padding-bottom: 12px;" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<div class="dropdown-menu" >
									<div id="alphabets_records" style="height: 100px;overflow: scroll;">

									</div>
									<div class="divider" style="margin: 0px 0px 4px 0px;"></div>
									<div><a onclick="alphabetFilter(1);" href="javascript://;" style="padding: 0px 17px;font-size: 13px;line-height: 2em;color:#0088cc;">Clear</a></div>
								</div>
							</div>
						</div>
					</div>
					<div id="entities_append">Loading...</div>
					<div id="entities_no_result" class="hide no-result">No Result</div>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

				</div>
			</div>
			<script type="text/javascript">
			var isOpenCollectionModal = 0;
			var isOpenRepositoryModal = 0;
			var isOpenEntitiesModal = 0;
			function getAllCollections() {
				if (isOpenCollectionModal == 0) {
					$.ajax({
						type: 'GET',
						url: '/index.php/Browse/getAllCollections',
//					data: $('#form_search').serialize(),
						dataType: 'json',
						success: function(result, textStatus, request)
						{
							$('#collection_append').html('');
							for (cnt in result) {
//							console.log(result[cnt].id);
								$('#collection_append').append('<div style="padding:10px;font-size: 15px;" class="' + result[cnt].place + '"><a href="/index.php/Browse/facet?c=' + result[cnt].id + '&name=' + encodeURIComponent(result[cnt].name) + '">' + result[cnt].name + '</a></div>');
								$('#collection_states_list').show();
							}
							isOpenCollectionModal = 1;
//					console.log(result);
						}

					});
				}
			}
			function getAllRepository() {
				if (isOpenRepositoryModal == 0) {
					$.ajax({
						type: 'GET',
						url: '/index.php/Browse/getAllRepository',
//					data: $('#form_search').serialize(),
						dataType: 'json',
						success: function(result, textStatus, request)
						{
							$('#occurance_append').html('');
							for (cnt in result) {
								$('#occurance_append').append('<div style="padding:10px;font-size: 15px;" class="' + result[cnt].place + '"><a href="/index.php/Detail/Occurrence/Show/occurrence_id/' + result[cnt].id + '">' + result[cnt].name + '</a></div>');
								$('#occurance_states_list').show();
							}
							isOpenRepositoryModal = 1;

						}

					});
				}
			}
			function getAllEntities() {
				if (isOpenEntitiesModal == 0) {
					$.ajax({
						type: 'GET',
						url: '/index.php/Browse/getAllEntities',
//					data: $('#form_search').serialize(),
						dataType: 'json',
						success: function(result, textStatus, request)
						{
							$('#entities_append').html('');

							var list = {letters: []};    //object to collect the li elements and a list of initial letters
							for (cnt in result) {
								var itmLetter = result[cnt].name.substring(0, 1).toUpperCase();
								if (!(itmLetter in list)) {
									list[itmLetter] = [];
									list.letters.push(itmLetter);
									$('#alphabets_records').append('<div style="color:#424242;line-height:2.0em;font-size:12px;"><input type="checkbox" style="padding-top: 0;margin-top: -5px;margin-left: 8px;" value="alpha_' + itmLetter + '" onclick="alphabetFilter();"/><span style="display:inline;padding: 3px 12px;" href="javascript://;">' + itmLetter + '</span></div>');
								}
								$('#entities_append').append('<div style="padding:10px;font-size: 15px;" class="alpha_' + itmLetter + '"><a  href="/index.php/Browse/facet?e=' + result[cnt].id + '&name=' + encodeURIComponent(result[cnt].name) + '">' + result[cnt].name + '</a></div>');
//								list[itmLetter].push($(this));    //add li element to the letter's array in the list object
							}
							$('#alphatbets_list').show();
							console.log(list);
							isOpenEntitiesModal = 1;

						}

					});
				}
			}
			function filterRecords(type, clear) {
				if (clear == 1) {
					$("#" + type + "_states_list input").prop("checked", false);
				}
				if ($("#" + type + "_states_list input:checked").length == 0) {
					$('#' + type + '_append div').removeClass('hide');
				}
				else {
					$('#' + type + '_append div').removeClass('hide');
					$('.NaN').addClass('hide');
					$("#" + type + "_states_list input:checkbox:not(:checked)").each(function() {
						$('.' + $(this).val()).addClass('hide');

					});

				}
				if ($('#' + type + '_append div:not(.hide)').length == 0)
					$('#' + type + '_no_result').show();
				else
					$('#' + type + '_no_result').hide();

			}
			function alphabetFilter(clear) {
				if (clear == 1) {
					$("#alphatbets_list input").prop("checked", false);
				}
				if ($("#alphatbets_list input:checked").length == 0) {
					$('#entities_append div').removeClass('hide');
				}
				else {
					$('#entities_append div').removeClass('hide');
//					$('.NaN').addClass('hide');
					$("#alphatbets_list input:checkbox:not(:checked)").each(function() {
						$('.' + $(this).val()).addClass('hide');
						console.log($(this).val());
					});

				}
				if ($('#entities_append div:not(.hide)').length == 0)
					$('#entities_no_result').show();
				else
					$('#entities_no_result').hide();
			}
			function doSearch(e) {
				if (e.keyCode == 13) {
					window.location.href = '/index.php/Browse/facet?keyword=' + encodeURIComponent($('#quickSearch').val());
				}
			}
			$('.dropdown-menu div').click(function(e) {
				e.stopPropagation();
			});
			</script>
