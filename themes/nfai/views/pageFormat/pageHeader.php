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
		<div class="navbar" style="width: 920px;margin: 0 auto;">
			<div class="navbar-inner">
				<a class="brand" href="/">American Folklore Society</a>
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
			<div id="header">
				<div>
					<div class="pull-left"><div class="btn-group">
							<button class="btn splash-css">Browse By</button>
							<button class="btn dropdown-toggle" style="padding-bottom: 12px;" data-toggle="dropdown">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">

								<!--<li><a href="#myModal" role="button"  data-toggle="modal" data-backdrop="static" onclick="getAllCollections();">Collection</a></li>--> 
								<li><a href="javascript://" onclick='caUIBrowsePanel.showBrowsePanel("collection_facet");'>Collection</a></li> 
								<li><a href="javascript://" onclick='caUIBrowsePanel.showBrowsePanel("occurrence_facet_103");'>Repository</a></li>

								<li><a href="javascript://" onclick='caUIBrowsePanel.showBrowsePanel("entity_facet");'>Individual, Organization<br/> or Meeting</a></li>
								<!--<li><a href="javascript://" onclick='caUIBrowsePanel.showBrowsePanel("lchs_facet");'>Subject</a></li>--> 
							</ul>
						</div>
					</div>
					<div>
						<form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
							<b class="custom-search splash-css">SEARCH:</b> <input type="text" class="span8" value="<?php print ($vs_search) ? $vs_search : ''; ?>" name="search"  id="quickSearch"  autocomplete="off"  onclick='jQuery("#quickSearch").select();' />


						</form>

					</div>
				</div>
				<div class="clearfix"></div>
				<?php
//				print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/".$this->request->config->get('header_img')."' border='0'>", "", "", "", "");
				?>				
			</div><!-- end header -->
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
			<hr style="margin: 16px 0 2px 0;"/>
			<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel">Modal header</h3>
				</div>
				<div class="modal-body">
					<p>One fine body…</p>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

				</div>
			</div>
			<script type="text/javascript">
			function getAllCollections() {
				$.ajax({
					type: 'GET',
					url: '/index.php/Browse' + page,
					data: $('#form_search').serialize(),
					success: function(result, textStatus, request)
					{


					}

				});
			}
			</script>