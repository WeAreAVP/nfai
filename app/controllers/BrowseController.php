<?php

/* ----------------------------------------------------------------------
 * includes/BrowseController.php
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

require_once(__CA_LIB_DIR__ . "/ca/BaseBrowseController.php");
require_once(__CA_LIB_DIR__ . "/ca/Browse/ObjectBrowse.php");
require_once(__CA_LIB_DIR__ . "/ca/Browse/EntityBrowse.php");
require_once(__CA_LIB_DIR__ . "/ca/Browse/PlaceBrowse.php");
require_once(__CA_LIB_DIR__ . "/ca/Browse/CollectionBrowse.php");
require_once(__CA_LIB_DIR__ . "/ca/Browse/OccurrenceBrowse.php");
require_once(__CA_LIB_DIR__ . '/core/GeographicMap.php');
require_once(__CA_MODELS_DIR__ . "/ca_entities.php");

class BrowseController extends BaseBrowseController
{
	# -------------------------------------------------------
	/**
	 * Name of table for which this browse returns items
	 */

	protected $ops_tablename = null;

	/**
	 * Number of items per results page
	 */
	protected $opa_items_per_page = array(12, 24, 36);

	/**
	 * Default number of items per search results page
	 */
	protected $opn_items_per_page_default = 12;

	/**
	 * List of result views supported for this browse
	 * Is associative array: keys are view labels, values are view specifier to be incorporated into view name
	 */
	protected $opa_views;

	/**
	 * List of search-result view options
	 * Is associative array: keys are view labels, arrays for each view contain description and icon graphic name for use in view
	 */
	protected $opa_views_options;

	/**
	 * List of available result sorting fields
	 * Is associative array: values are display names for fields, keys are full fields names (table.field) to be used as sort
	 */
	protected $opa_sorts;
	protected $ops_find_type = 'basic_browse';

	# -------------------------------------------------------

	public function __construct(&$po_request, &$po_response, $pa_view_paths = null)
	{
		parent::__construct($po_request, $po_response, $pa_view_paths);

		//
		// Get browse target
		//
 			$va_browse_targets = $this->request->config->getList('browse_targets');
		if ( ! ($vs_browse_target = $po_request->getParameter('target', pString)) || ($vs_browse_target == 'null'))
		{
			if ( ! ($vs_browse_target = $po_request->session->getVar('pawtucket2_browse_target')))
			{
				if (is_array($va_browse_targets))
				{
					$vs_browse_target = array_shift($va_browse_targets);
				}
			}
		}

		$va_target_list = array();
		foreach ($this->request->config->getList('browse_targets') as $vs_target)
		{
			$va_target_list[$vs_target] = caGetBrowseInstance($vs_target);
		}
		$this->view->setVar('targets', $va_target_list);

		// redirect user if not logged in
		if (($this->request->config->get('pawtucket_requires_login') && ! ($this->request->isLoggedIn())) || ($this->request->config->get('show_bristol_only') && ! ($this->request->isLoggedIn())))
		{
			$this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
		}
		elseif (($this->request->config->get('show_bristol_only')) && ($this->request->isLoggedIn()))
		{
			$this->response->setRedirect(caNavUrl($this->request, "bristol", "Show", "Index"));
		}

		//
		// Minimal view list (all targets have a "full" results view)
		//
 			$this->opa_views = array(
			'full' => _t('List')
		);
		$this->opa_views_options = array(
			'full' => array("description" => _t("View results in a list"), "icon" => "icon_list.gif")
		);
		if ($this->request->config->get("dont_enforce_access_settings"))
		{
			$va_access_values = array();
		}
		else
		{
			$va_access_values = caGetUserAccessValues($this->request);
		}
		$this->view->setVar('access_values', $va_access_values);
		//
		// Set up for browse target
		//
 			switch ($vs_browse_target)
		{
			case 'ca_entities':
				$this->ops_tablename = 'ca_entities';
				$this->opo_result_context = new ResultContext($po_request, $this->ops_tablename, $this->ops_find_type);
				$this->opo_browse = new EntityBrowse($this->opo_result_context->getSearchExpression(), 'pawtucket2');

				// get configured result views, if specified
				if ($va_result_views_for_ca_entities = $po_request->config->getAssoc('result_views_for_ca_entities'))
				{
					$this->opa_views = $va_result_views_for_ca_entities;
				}
				// get configured result views options, if specified
				if ($va_result_views_options_for_ca_entities = $po_request->config->getAssoc('result_views_options_for_ca_entities'))
				{
					$this->opa_views_options = $va_result_views_options_for_ca_entities;
				}
				// get configured result sort options, if specified
				if ($va_sort_options_for_ca_entities = $po_request->config->getAssoc('result_sort_options_for_ca_entities'))
				{
					$this->opa_sorts = $va_sort_options_for_ca_entities;
				}
				else
				{
					$this->opa_sorts = array(
						'ca_entity_labels.displayname' => _t('Name'),
						'ca_entities.type_id' => _t('Type'),
						'ca_entities.idno_sort' => _t('Idno')
					);
				}
				break;
			case 'ca_places':
				$this->ops_tablename = 'ca_places';
				$this->opo_result_context = new ResultContext($po_request, $this->ops_tablename, $this->ops_find_type);
				$this->opo_browse = new PlaceBrowse($this->opo_result_context->getSearchExpression(), 'pawtucket2');

				// get configured result views, if specified
				if ($va_result_views_for_ca_places = $po_request->config->getAssoc('result_views_for_ca_places'))
				{
					$this->opa_views = $va_result_views_for_ca_places;
				}
				// get configured result views options, if specified
				if ($va_result_views_options_for_ca_places = $po_request->config->getAssoc('result_views_options_for_ca_places'))
				{
					$this->opa_views_options = $va_result_views_options_for_ca_places;
				}
				// get configured result sort options, if specified
				if ($va_sort_options_for_ca_places = $po_request->config->getAssoc('result_sort_options_for_ca_places'))
				{
					$this->opa_sorts = $va_sort_options_for_ca_places;
				}
				else
				{
					$this->opa_sorts = array(
						'ca_place_labels.name' => _t('Name'),
						'ca_places.type_id' => _t('Type'),
						'ca_places.idno_sort' => _t('Idno')
					);
				}
				break;
			case 'ca_occurrences':
				$this->ops_tablename = 'ca_occurrences';
				$this->opo_result_context = new ResultContext($po_request, $this->ops_tablename, $this->ops_find_type);
				$this->opo_browse = new OccurrenceBrowse($this->opo_result_context->getSearchExpression(), 'pawtucket2');

				// get configured result views, if specified
				if ($va_result_views_for_ca_occurrences = $po_request->config->getAssoc('result_views_for_ca_occurrences'))
				{
					$this->opa_views = $va_result_views_for_ca_occurrences;
				}
				// get configured result views options, if specified
				if ($va_result_views_options_for_ca_occurrences = $po_request->config->getAssoc('result_views_options_for_ca_occurrences'))
				{
					$this->opa_views_options = $va_result_views_options_for_ca_occurrences;
				}
				// get configured result sort options, if specified
				if ($va_sort_options_for_ca_occurrences = $po_request->config->getAssoc('result_sort_options_for_ca_occurrences'))
				{
					$this->opa_sorts = $va_sort_options_for_ca_occurrences;
				}
				else
				{
					$this->opa_sorts = array(
						'ca_occurrence_labels.name' => _t('Title'),
						'ca_occurrences.idno_sort' => _t('Idno')
					);
				}
				break;
			case 'ca_collections':
				$this->ops_tablename = 'ca_collections';
				$this->opo_result_context = new ResultContext($po_request, $this->ops_tablename, $this->ops_find_type);
				$this->opo_browse = new CollectionBrowse($this->opo_result_context->getSearchExpression(), 'pawtucket2');

				// get configured result views, if specified
				if ($va_result_views_for_ca_collections = $po_request->config->getAssoc('result_views_for_ca_collections'))
				{
					$this->opa_views = $va_result_views_for_ca_collections;
				}
				// get configured result views options, if specified
				if ($va_result_views_options_for_ca_collections = $po_request->config->getAssoc('result_views_options_for_ca_collections'))
				{
					$this->opa_views_options = $va_result_views_options_for_ca_collections;
				}
				// get configured result sort options, if specified
				if ($va_sort_options_for_ca_collections = $po_request->config->getAssoc('result_sort_options_for_ca_collections'))
				{
					$this->opa_sorts = $va_sort_options_for_ca_collections;
				}
				else
				{
					$this->opa_sorts = array(
						'ca_collection_labels.name' => _t('Name'),
						'ca_collections.type_id' => _t('Type'),
						'ca_collections.idno_sort' => _t('Idno')
					);
				}
				break;
			default:
				$this->ops_tablename = 'ca_objects';
				$this->opo_result_context = new ResultContext($po_request, $this->ops_tablename, $this->ops_find_type);
				$this->opo_browse = new ObjectBrowse($this->opo_result_context->getSearchExpression(), 'pawtucket2');

				// get configured result views, if specified
				if ($va_result_views_for_ca_objects = $po_request->config->getAssoc('result_views_for_ca_objects'))
				{
					$this->opa_views = $va_result_views_for_ca_objects;
				}
				else
				{
					$this->opa_views = array(
						'full' => _t('List'),
						'thumbnail' => _t('Thumbnails')
					);
				}
				// get configured result views options, if specified
				if ($va_result_views_options_for_ca_objects = $po_request->config->getAssoc('result_views_options_for_ca_objects'))
				{
					$this->opa_views_options = $va_result_views_options_for_ca_objects;
				}
				else
				{
					$this->opa_views_options = array(
						'thumbnail' => array("description" => _t("View thumbnails with brief captions"), "icon" => "icon_thumbnail.gif"),
						'full' => array("description" => _t("View images with full captions"), "icon" => "icon_full.gif")
					);
				}
				// get configured result sort options, if specified
				if ($va_sort_options_for_ca_objects = $po_request->config->getAssoc('result_sort_options_for_ca_objects'))
				{
					$this->opa_sorts = $va_sort_options_for_ca_objects;
				}
				else
				{
					$this->opa_sorts = array(
						'ca_object_labels.name' => _t('Title'),
						'ca_objects.type_id' => _t('Type'),
						'ca_objects.idno_sort' => _t('Idno')
					);
				}

				if ($po_request->config->get("show_map_object_search_results"))
				{
					JavascriptLoadManager::register('maps');
					$this->opa_views['map'] = _t('Map');
					if ( ! $this->opa_views_options['map'])
					{
						$this->opa_views_options['map'] = array("description" => _t("View results plotted on a map"), "icon" => "icon_map.gif");
					}
				}
				break;
		}

		// if target changes we need clear out all browse criteria as they are no longer valid
		if ($vs_browse_target != $po_request->session->getVar('pawtucket2_browse_target'))
		{
			$this->opo_browse->removeAllCriteria();
		}

		// Set up target vars and controls
		$po_request->session->setVar('pawtucket2_browse_target', $vs_browse_target);

		if (sizeof($va_browse_targets = $this->request->config->getList('browse_targets')) > 1)
		{
			$va_browse_options = array();
			foreach ($va_browse_targets as $vs_possible_browse_target)
			{
				if ($vs_browse_target_name = $this->opo_browse->getBrowseSubjectName($vs_possible_browse_target))
				{
					$va_browse_options[$vs_browse_target_name] = $vs_possible_browse_target;
				}
			}
			$this->view->setVar('browse_selector', "<form action='#'>" . caHTMLSelect('target', $va_browse_options, array('id' => 'caBrowseTargetSelectorSelect', 'onchange' => 'window.location = \'' . caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Index', array('target' => '')) . '\' + jQuery(\'#caBrowseTargetSelectorSelect\').val();'), array('value' => $vs_browse_target, 'dontConvertAttributeQuotesToEntities' => true)) . "</form>\n");
		}

		// get configured items per page options, if specified
		if ($va_items_per_page = $po_request->config->getList('items_per_page_options_for_' . $vs_browse_target . '_browse'))
		{
			$this->opa_items_per_page = $va_items_per_page;
		}
		if (($vn_items_per_page_default = (int) $po_request->config->get('items_per_page_default_for_' . $this->ops_tablename . '_browse')) > 0)
		{
			$this->opn_items_per_page_default = $vn_items_per_page_default;
		}
		else
		{
			$this->opn_items_per_page_default = $this->opa_items_per_page[0];
		}

		// set current result view options so can check we are including a configured result view
		$this->view->setVar('result_views', $this->opa_views);
		$this->view->setVar('result_views_options', $this->opa_views_options);

		if ($this->opn_type_restriction_id = $this->opo_result_context->getTypeRestriction($pb_type_restriction_has_changed))
		{
			$_GET['type_id'] = $this->opn_type_restriction_id;  // push type_id into globals so breadcrumb trail can pick it up
			$this->opb_type_restriction_has_changed = $pb_type_restriction_has_changed; // get change status
		}
	}

	# -------------------------------------------------------
	/**
	 * Override browse index to check if we need to honor the "use_splash_page_for_start_of_browse" setting and  redirect user to the splash page
	 */

	public function Index()
	{
		if ($this->request->config->get('use_splash_page_for_start_of_browse') && ! $this->opo_browse->numCriteria())
		{
			$this->response->setRedirect(caNavUrl($this->request, '', 'Splash', 'Index'));
			return;
		}
		parent::Index();
	}

	# -------------------------------------------------------
	/**
	 * Ajax action that returns info on a mapped location based upon the 'id' request parameter.
	 * 'id' is a list of object_ids to display information before. Each integer id is separated by a semicolon (";")
	 * The "ca_objects_results_map_balloon_html" view in Results/ is used to render the content.
	 */

	public function getMapItemInfo()
	{
		$pa_object_ids = explode(';', $this->request->getParameter('id', pString));

		$va_access_values = caGetUserAccessValues($this->request);

		$this->view->setVar('ids', $pa_object_ids);
		$this->view->setVar('access_values', $va_access_values);

		$this->render("Results/ca_objects_results_map_balloon_html.php");
	}

	# -------------------------------------------------------

	public function browseName($ps_mode = 'singular')
	{
		return ($ps_mode == 'singular') ? _t('browse') : _t('browses');
	}

	# -------------------------------------------------------
	/**
	 * Looks for 'view' parameter and sets browse facet view to alternate based upon parameter value if specified.
	 * This lets you set a custom browse facet view from a link.
	 * Note that the view parameter is NOT a full view name. Rather it is a simple text string (letters, numbers and underscores only)
	 * that is inserted between "ajax_browse_facet_" and "_html.php" to construct a view name in themes/<theme_name>/views/Browse.
	 * If a view with this name exists it will be used, otherwise the default view in Browse/ajax_browse_facet_html.php.
	 *
	 */

	public function getFacet($pa_options = null)
	{
		if ( ! is_array($pa_options))
		{
			$pa_options = array();
		}
		if ($ps_view = preg_replace('![^A-Za-z0-9_]+!', '', $this->request->getParameter('view', pString)))
		{
			$vs_relative_path = 'Browse/ajax_browse_facet_' . $ps_view . '_html.php';

			if (file_exists($this->request->getThemeDirectoryPath() . '/views/' . $vs_relative_path))
			{
				$pa_options['view'] = $vs_relative_path;
			}
		}
		parent::getFacet($pa_options);
	}

	function make_array($value)
	{
		return $value['id'];
	}

	public function facet()
	{
		$_SESSION['collection'] = array();
		$_SESSION['entity'] = array();
		$_SESSION['occurence'] = array();
		$_SESSION['type'] = array();
		$_SESSION['parent_facet'] = '';
		$_SESSION['total_count'] = 0;
		$_SESSION['keyword'] = '';
		if (isset($_GET['c']) && ! empty($_GET['c']))
		{
			$_SESSION['collection'] = array(0 => array('id' => $_GET['c'], 'name' => $_GET['name']));
		}
		if (isset($_GET['e']) && ! empty($_GET['e']))
		{
			$_SESSION['entity'] = array(0 => array('id' => $_GET['e'], 'name' => $_GET['name']));
		}
		if (isset($_GET['o']) && ! empty($_GET['o']))
		{
			$_SESSION['occurence'] = array(0 => array('id' => $_GET['o'], 'name' => $_GET['name']));
		}
		if (isset($_GET['keyword']) && ! empty($_GET['keyword']))
		{
			$search_facet = new stdClass;
			$search_facet->value = $_GET['keyword'];
			$_SESSION['keyword'] = array(0 => $search_facet);
		}
		$this->redirect('/index.php/Browse/Search');
	}

	public function getItems($object_id, $data = array())
	{
		$o_db = new Db();
		$object_result = $o_db->query("SELECT o.object_id,ol.name,lt.item_value,orr.media
						FROM  `ca_objects` o
						INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
						INNER JOIN ca_list_items lt ON lt.item_id=o.type_id 
						LEFT JOIN  `ca_objects_x_object_representations` oor ON oor.object_id = o.object_id AND oor.is_primary=1
						LEFT JOIN  `ca_object_representations` orr ON orr.representation_id = oor.representation_id AND orr.deleted=0
						WHERE o.deleted=0 AND o.status=0 AND o.access !=0 AND o.parent_id={$object_id}
					    ORDER BY ol.name_sort");

		while ($object_result->nextRow())
		{

			$record = $object_result->getRow();
			$data[] = array('id' => $record['object_id'],
				'id' => $record['object_id'],
				'name' => $record['name'],
				'type' => ucfirst($record['item_value']),
				'thumbnail' => $object_result->getMediaUrl('media', 'thumbnail'),
				'medium' => $object_result->getMediaUrl('media', 'medium'),
			);

			$data = $this->getItems($record['object_id'], $data);
		}
		return $data;
	}

	public function destroyFacet()
	{
		$_SESSION['collection'] = array();
		$_SESSION['entity'] = array();
		$_SESSION['occurence'] = array();
		$_SESSION['type'] = array();
		$_SESSION['parent_facet'] = '';
		$_SESSION['total_count'] = 0;
		$_SESSION['keyword'] = '';
		$this->redirect('/index.php/Browse/Search');
	}

	public function Search()
	{
		$this->view->setVar('isAjax', FALSE);
		if ( ! isset($_SESSION['collection']))
			$_SESSION['collection'] = array();
		if ( ! isset($_SESSION['entity']))
			$_SESSION['entity'] = array();
		if ( ! isset($_SESSION['occurence']))
			$_SESSION['occurence'] = array();
		if ( ! isset($_SESSION['type']))
			$_SESSION['type'] = array();
		if ( ! isset($_SESSION['parent_facet']))
			$_SESSION['parent_facet'] = '';
		if ( ! isset($_SESSION['total_count']))
			$_SESSION['total_count'] = 0;
		if ( ! isset($_SESSION['keyword']))
			$_SESSION['keyword'] = '';
		if ($_POST)
		{
			$this->view->setVar('isAjax', TRUE);
			$_SESSION['total_count'] = $_POST['total_checked'];
			$_SESSION['parent_facet'] = $_POST['parent_facet'];
			$_SESSION['keyword'] = json_decode($_POST['facet_keyword_search']);

			if (isset($_POST['type']))
			{
				$facet_type = array();
				foreach ($_POST['type'] as $value)
				{
					$facet_type[] = $value;
				}
				$_SESSION['type'] = $facet_type;
			}
			else
				$_SESSION['type'] = array();
			if (isset($_POST['collection']))
			{
				$facet_collection = array();
				foreach ($_POST['collection'] as $value)
				{
					$explode = explode('|||', $value);
					$facet_collection[] = array('id' => $explode[0], 'name' => $explode[1]);
				}
				$_SESSION['collection'] = $facet_collection;
			}
			else
				$_SESSION['collection'] = array();
			if (isset($_POST['entity']))
			{
				$facet_entity = array();
				foreach ($_POST['entity'] as $value)
				{
					$explode = explode('|||', $value);
					$facet_entity[] = array('id' => $explode[0], 'name' => $explode[1]);
				}
				$_SESSION['entity'] = $facet_entity;
			}
			else
				$_SESSION['entity'] = array();
			if (isset($_POST['occurrence']))
			{
				$facet_occurence = array();
				foreach ($_POST['occurrence'] as $value)
				{
					$explode = explode('|||', $value);
					$facet_occurence[] = array('id' => $explode[0], 'name' => $explode[1]);
				}
				$_SESSION['occurence'] = $facet_occurence;
			}
			else
				$_SESSION['occurence'] = array();
		}
		$o_db = new Db();
		$keyword = $where = $occurence_join = $entity_join = '';
		$selectedCollection = $selectedEntity = $selectedOccurrence = array();
		if (isset($_SESSION['collection']) && count($_SESSION['collection']) > 0)
		{
			$search = implode(',', array_map(array($this, 'make_array'), $_SESSION['collection']));
			$selectedCollection = array_map(array($this, 'make_array'), $_SESSION['collection']);
			$where = " AND o.object_id IN ({$search})";
		}
		if (isset($_SESSION['entity']) && count($_SESSION['entity']) > 0)
		{
			$search = implode(',', array_map(array($this, 'make_array'), $_SESSION['entity']));
			$selectedEntity = array_map(array($this, 'make_array'), $_SESSION['entity']);
			$entity_join = " AND oe.entity_id IN ({$search})";
		}
		if (isset($_SESSION['occurence']) && count($_SESSION['occurence']) > 0)
		{
			$search = implode(',', array_map(array($this, 'make_array'), $_SESSION['occurence']));
			$selectedOccurrence = array_map(array($this, 'make_array'), $_SESSION['occurence']);
			$occurence_join = " AND oo.occurrence_id IN ({$search})";
		}
		if (isset($_SESSION['keyword']) && ! empty($_SESSION['keyword']) && count($_SESSION['keyword']) > 0)
		{
			$keyword = 'AND (';
			foreach ($_SESSION['keyword'] as $key => $value)
			{
				if ($key != 0)
					$keyword .=' OR ';
				$keyword .=" ol.name LIKE '{$value->value}%'";
			}
			$keyword .=')';
		}
		$object_result = $o_db->query("SELECT o.object_id,ol.name,lt.item_value,orr.media
						FROM  `ca_objects` o
						INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
						INNER JOIN ca_list_items lt ON lt.item_id=o.type_id 
						LEFT JOIN  `ca_objects_x_entities` oe ON oe.object_id = o.object_id 
						LEFT JOIN  `ca_objects_x_occurrences` oo ON oo.object_id = o.object_id 
						LEFT JOIN  `ca_objects_x_object_representations` oor ON oor.object_id = o.object_id AND oor.is_primary=1
						LEFT JOIN  `ca_object_representations` orr ON orr.representation_id = oor.representation_id AND orr.deleted=0
						LEFT JOIN  `ca_object_representation_multifiles` orm ON orm.representation_id = oor.representation_id 
						WHERE o.deleted=0 AND o.status=0 AND o.access !=0 AND o.type_id=21  {$where} {$entity_join} {$occurence_join} {$keyword}
					    GROUP BY o.object_id
						ORDER BY ol.name_sort,o.type_id");
		$object_array = array();

		while ($object_result->nextRow())
		{

			$record = $object_result->getRow();
			$object_array[] = array('id' => $record['object_id'],
				'id' => $record['object_id'],
				'name' => $record['name'],
				'type' => ucfirst($record['item_value']),
				'thumbnail' => $object_result->getMediaUrl('media', 'thumbnail'),
				'medium' => $object_result->getMediaUrl('media', 'medium'),
				'items' => $this->getItems($record['object_id'])
			);
		}

		$this->view->setVar('collection_list', $object_array);
		$collection_where = '';
		if ($_SESSION['parent_facet'] != 'collection')
			$collection_where = "{$entity_join} {$occurence_join} {$where} {$keyword}";
		$collection_result = $o_db->query("SELECT o.object_id,ol.name
					FROM ca_objects o
					INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
					LEFT JOIN  `ca_objects_x_entities` oe ON oe.object_id = o.object_id 
					LEFT JOIN  `ca_objects_x_occurrences` oo ON oo.object_id = o.object_id 
					WHERE o.type_id=21
					AND o.deleted=0 AND o.status=0 AND o.access !=0 $collection_where
					GROUP BY o.object_id
					ORDER BY ol.name_sort");
		$i = 0;
		$collection = '';
		$collectionModal = '';
		$moreCollection = FALSE;
		$isCollection = FALSE;
		while ($collection_result->nextRow())
		{
			$isCollection = TRUE;
			$record = $collection_result->getRow();
			$val = htmlentities($record['name']);
			$checked = '';
			if (count($selectedCollection) > 0 && in_array($record['object_id'], $selectedCollection))
				$checked = 'checked="checked"';
			if ($i < 6)
			{

				$collection .= "<div><div style='float:left;'><input type='checkbox' {$checked} id='object_{$record['object_id']}' name='collection[]' value='{$record['object_id']}|||" . html_entity_decode($val) . "'/></div><div style='margin-left: 20px;'>{$record['name']}</div></div>";
			}
			else
			{
				$moreCollection = TRUE;
				$collectionModal .="<div><div style='float:left;'><input {$checked} id='object_{$record['object_id']}' onclick=\"$('#collectionSearchModal').modal('toggle');\" type='checkbox' name='collection[]' value='{$record['object_id']}|||" . html_entity_decode($val) . "'/></div><div style='margin-left: 20px;'>{$record['name']}</div></div>";
			}

			$i ++;
		}
		$collection .='<div id="collectionSearchModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h1 id="myModalLabel">More Collection</h1>
						</div>
						<div class="modal-body">' .
		$collectionModal
		. '</div>
						<div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
						</div>
					</div>';
		$this->view->setVar('collection', $collection);
		$this->view->setVar('show_more_collection', $moreCollection);
		$this->view->setVar('isCollection', $isCollection);
		$occurrence_where = '';
		if ($_SESSION['parent_facet'] != 'occurrence')
			$occurrence_where = "{$entity_join} {$occurence_join} {$where} {$keyword}";
		$occurrences_result = $o_db->query("SELECT oo.occurrence_id,olo.name 
								FROM ca_occurrences oo 
								INNER JOIN ca_occurrence_labels olo ON olo.occurrence_id =oo.occurrence_id AND olo.is_preferred =1 
								
								LEFT JOIN `ca_objects_x_occurrences` oxo ON oxo.occurrence_id = oo.occurrence_id 
								LEFT JOIN `ca_objects` o ON o.object_id = oxo.object_id 
								LEFT JOIN `ca_objects_x_entities` oe ON oe.object_id = o.object_id 
								INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
								WHERE oo.deleted=0  AND oo.access !=0 {$occurrence_where} 
								GROUP BY oo.occurrence_id
								ORDER BY olo.name_sort");
//		$occurrences_result = $o_db->query("SELECT oo.occurrence_id,ol.name
//								FROM ca_occurrences oo
//								INNER JOIN ca_occurrence_labels ol ON ol.occurrence_id =oo.occurrence_id AND ol.is_preferred =1
//								INNER JOIN  `ca_objects` o ON o.object_id = oo.object_id {$where}
//								INNER JOIN  `ca_objects_x_entities` oe ON oe.object_id = o.object_id {$entity_join}
//								WHERE oo.deleted=0 AND oo.status=0 AND oo.access !=0
//								ORDER BY ol.name_sort");
		$i = 0;
		$occurrence = '';
		$occurrenceModal = '';
		$moreOccurrence = FALSE;
		$isOccurrence = FALSE;
//		$selectedOccurrence = array_map(array($this, 'make_array'), $_SESSION['occurence']);
		while ($occurrences_result->nextRow())
		{
			$isOccurrence = TRUE;
			$record = $occurrences_result->getRow();
			$val = htmlentities($record['name']);
			$checked = '';
			if (count($selectedOccurrence) > 0 && in_array($record['occurrence_id'], $selectedOccurrence))
				$checked = 'checked="checked"';
			if ($i < 6)
			{

				$occurrence .= "<div><div style='float:left;'><input {$checked} id='occurrence_{$record['occurrence_id']}' type='checkbox' name='occurrence[]' value='{$record['occurrence_id']}|||" . html_entity_decode($val) . "'/></div><div style='margin-left: 20px;'>{$record['name']}</div></div>";
			}
			else
			{
				$moreOccurrence = TRUE;
				$occurrenceModal .="<div><div style='float:left;'><input {$checked} id='occurrence_{$record['occurrence_id']}' onclick=\"$('#occurenceSearchModal').modal('toggle');\" type='checkbox' name='occurrence[]' value='{$record['occurrence_id']}|||" . html_entity_decode($val) . "'/></div><div style='margin-left: 20px;'>{$record['name']}</div></div>";
			}

			$i ++;
		}
		$occurrence .='<div id="occurenceSearchModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h1 id="myModalLabel">More Repository</h1>
						</div>
						<div class="modal-body">' .
		$occurrenceModal
		. '</div>
						<div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
						</div>
					</div>';
		$this->view->setVar('occurrence', $occurrence);
		$this->view->setVar('show_more_occurrence', $moreOccurrence);
		$this->view->setVar('isOccurrence', $isOccurrence);
		$entity_where = '';
		if ($_SESSION['parent_facet'] != 'entity')
			$entity_where = "{$entity_join} {$occurence_join} {$where} {$keyword}";
		$entity_result = $o_db->query("
				SELECT oe.`entity_id` , ole.displayname
				FROM ca_entities oe
				INNER JOIN ca_entity_labels ole ON ole.entity_id = oe.entity_id AND ole.is_preferred =1
				LEFT JOIN  `ca_objects_x_entities` oxo ON oxo.entity_id = oe.entity_id
				LEFT JOIN  `ca_objects` o ON o.object_id = oxo.object_id
				LEFT JOIN  `ca_objects_x_occurrences` oo ON oo.object_id = o.object_id
				INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
				WHERE oe.deleted =0
				AND oe.status =0
				AND oe.access !=0 {$entity_where}
				GROUP BY oe.`entity_id` 
				ORDER BY ole.name_sort
				");

		$i = 0;
		$entity = '';
		$entityModal = '';
		$moreEntity = FALSE;
		$isEntity = FALSE;
		while ($entity_result->nextRow())
		{
			$isEntity = TRUE;
			$record = $entity_result->getRow();
			$val = htmlentities($record['displayname']);
			$checked = '';
			if (count($selectedEntity) > 0 && in_array($record['entity_id'], $selectedEntity))
				$checked = 'checked="checked"';
			if ($i < 6)
			{

				$entity .= "<div><div style='float:left;'><input {$checked} id='entity_{$record['entity_id']}' type='checkbox' name='entity[]' value='{$record['entity_id']}|||" . html_entity_decode($val) . "'/></div><div style='margin-left: 20px;'>{$record['displayname']}</span></div></div>";
			}
			else
			{
				$moreEntity = TRUE;
				$entityModal .="<div><div style='float:left;'><input {$checked} id='entity_{$record['entity_id']}' onclick=\"$('#entitySearchModal').modal('toggle');\" type='checkbox' name='entity[]' value='{$record['entity_id']}|||" . html_entity_decode($val) . "'/></div><div style='margin-left: 20px;'>{$record['displayname']}</span></div></div>";
			}

			$i ++;
		}
		$entity .='<div id="entitySearchModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h1 id="myModalLabel">More Individual, Organization, Meeting</h1>
						</div>
						<div class="modal-body">' .
		$entityModal
		. '</div>
						<div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
						</div>
					</div>';
		$this->view->setVar('show_more_entity', $moreEntity);
		$this->view->setVar('entity', $entity);
		$this->view->setVar('isEntity', $isEntity);


		if ($_POST)
		{
			echo $this->render("Browse/browse_facet_html.php", TRUE);
			exit;
		}
		$this->render("Browse/browse_facet_html.php");
	}

	/**
	 * Get all collection list to show in modal popup.
	 * @author Nouman Tayyab <nouman@avpreserve.com>
	 */
	public function getAllCollections()
	{
		$o_db = new Db();
		$qr_res = $o_db->query("SELECT o.object_id
					FROM ca_objects o
					INNER JOIN ca_object_labels ol ON ol.object_id=o.object_id AND ol.is_preferred=1
					WHERE o.type_id=21
					AND o.deleted=0 AND o.status=0 AND o.access !=0
					ORDER BY ol.name_sort");
		$object = array();
		$states = caGetStateList();
		while ($qr_res->nextRow())
		{

			$record = $qr_res->getRow();
			$collection = new ca_objects($record['object_id']);
			$collection_state = $collection->get('ca_occurrences.repository_state', array('convertCodesToDisplayText' => true, 'returnAsArray' => 1));
			$place = '';
			foreach ($collection_state as $state_val)
			{
				$place .=' ' . $states['US'][$state_val['repository_state']];
			}

			$object[] = array('id' => $record['object_id'],
				'name' => $collection->get('ca_objects.preferred_labels.name'),
				'place' => ((trim($place))) ? $place : 'NaN');
		}
		echo json_encode($object);
		exit;
	}

	/**
	 * Get all occurances list to show in modal popup.
	 * @author Nouman Tayyab <nouman@avpreserve.com>
	 */
	public function getAllRepository()
	{
		$o_db = new Db();
		$qr_res = $o_db->query("SELECT o.occurrence_id
								FROM ca_occurrences o
								INNER JOIN ca_occurrence_labels ol ON ol.occurrence_id =o.occurrence_id AND ol.is_preferred =1
								WHERE o.deleted=0 AND o.status=0 AND o.access !=0
								ORDER BY ol.name_sort");
		$repository = array();
		$states = caGetStateList();
		while ($qr_res->nextRow())
		{
			$record = $qr_res->getRow();
			$occurance = new ca_occurrences($record['occurrence_id']);
			$occurance_state = $occurance->get('ca_occurrences.repository_state', array('convertCodesToDisplayText' => true, 'returnAsArray' => 1));
			$place = '';
			foreach ($occurance_state as $state_val)
			{
				$place .=' ' . $states['US'][$state_val['repository_state']];
			}
			$repository[] = array('id' => $record['occurrence_id'],
				'name' => $occurance->get('ca_occurrences.preferred_labels.name'),
				'place' => ((trim($place))) ? $place : 'NaN');
		}

		echo json_encode($repository);
		exit;
	}

	/**
	 * Get all entities list to show in modal popup.
	 * @author Nouman Tayyab <nouman@avpreserve.com>
	 */
	public function getAllEntities()
	{
		$o_db = new Db();
		$qr_res = $o_db->query("
				SELECT o.`entity_id` , ol.displayname
				FROM ca_entities o
				INNER JOIN ca_entity_labels ol ON ol.entity_id = o.entity_id AND ol.is_preferred =1
				WHERE o.deleted =0
				AND o.status =0
				AND o.access !=0
				ORDER BY ol.displayname");
		//address.stateprovince
		$entities = array();
		while ($qr_res->nextRow())
		{
			$record = $qr_res->getRow();
//			$entity=  ca_entities( $record['entity_id']);
			$entities[] = array('id' => $record['entity_id'],
				'name' => $record['displayname'],
//				'place'=> $entity->get('ca_entities.preferred_labels.name')
			);
		}

		echo json_encode($entities);
		exit;
	}

	public function test()
	{
		echo '<pre>';
		$o_db = new Db();
		$qr_res = $o_db->query("
					SELECT o.*
					FROM ca_objects o
					WHERE o.type_id=21
					and o.deleted=0 and o.status=0
				");
		$i = 0;
		while ($qr_res->nextRow())
		{
			$i ++;
			$record = $qr_res->getRow();
			$collection = new ca_objects($record['object_id']);
			echo $collection->get('ca_objects.preferred_labels.name');
			echo '<br/>';
			echo $collection->getTypeName();
			echo '<br/>';
//      print "GOT ACCESSION NUM=".$qr_res->getRow()."<br/>\n";
		}
		echo $i;
		exit;


		$va_facet = $this->opo_browse->getFacet('collection_facet', array('sort' => 'name', 'checkAccess' => 1));
		$states = array();
		foreach ($va_facet as $collections)
		{
			$object_id = $collections['object_id'];
//			echo $object_id.'<br/>';
			$t_object = new ca_objects($object_id);
			$states[] = $t_object->get('ca_occurrences.repository_state', array('convertCodesToDisplayText' => true));
		}
//		$t_object = new ca_objects(5);
//			$facet='collection_facet';
//		$place=$t_object->get('ca_occurrences.repository_state', array('convertCodesToDisplayText' => true));
		echo '<pre>';
		print_r($states);
		exit;
//		$o_data = new Db();
// $qr_result = $o_data->query("
//    SELECT * 
//    FROM ca_objects 
//    LIMIT 1
// "); 
// echo '<pre>';
// while($qr_result->nextRow()) {
//		print_r($qr_result->getRow());
////      print "GOT ACCESSION NUM=".$qr_result->getRow()."<br/>\n";
// }exit;
//		echo "<pre>";
//		$o_db = new Db();
//		$qr_res = $o_db->query("
//					SELECT o.*,p.name
//					FROM ca_occurrences o
//					INNER JOIN ca_occurrence_labels AS p ON p.occurrence_id = o.occurrence_id
//					
//					GROUP BY o.occurrence_id
//				");
//		 while($qr_res->nextRow()) {
//		print_r($qr_res->getRow());
//      print "GOT ACCESSION NUM=".$qr_res->getRow()."<br/>\n";
// }exit;
		$t_object = new ca_objects(5);   // load ca_object record with object_id = 40
		echo '<pre>';
		print_r($t_object->get('ca_occurrences.repository_state', array('convertCodesToDisplayText' => true)));
		exit;
//		print "The title of the object is ".$t_object->get('ca_objects.georeference')."<br/>\n";    // get the preferred name of the object
//		echo '<pre>';print_r($t_object);
//		exit;
		$count = 1;
		while ($t_object->nextHit())
		{
			print "Hit " . $count . ": " . $t_object->get('ca_objects.preferred_labels.name') . "<br/>\n";
			$count ++;
		}
		//
		echo 'Nouman';
		exit;
	}

	# -------------------------------------------------------
}

?>