<?php
/* ----------------------------------------------------------------------
 * nfaiToolsPlugin.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012 Whirl-i-Gig
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
 
	class nfaiToolsPlugin extends BaseApplicationPlugin {
		# -------------------------------------------------------
		private $opo_config;
		private $ops_plugin_path;
		# -------------------------------------------------------
		public function __construct($ps_plugin_path) {
			$this->ops_plugin_path = $ps_plugin_path;
			$this->description = 'Provides nfai services.';
			parent::__construct();
			$this->opo_config = Configuration::load($ps_plugin_path.'/conf/nfaiTools.conf');
		}
		# -------------------------------------------------------
		/**
		 * Override checkStatus() to return true - the ampasFrameImporterPlugin plugin always initializes ok
		 */
		public function checkStatus() {
			return array(
				'description' => $this->getDescription(),
				'errors' => array(),
				'warnings' => array(),
				'available' => ((bool)$this->opo_config->get('enabled'))
			);
		}
		# -------------------------------------------------------
		/**
		 * Insert import menu
		 */
		public function hookRenderMenuBar($pa_menu_bar) {
			if ($o_req = $this->getRequest()) {
				if (isset($pa_menu_bar['nfaiTools_menu'])) {
					$va_menu_items = $pa_menu_bar['nfaiTools_menu']['navigation'];
					if (!is_array($va_menu_items)) { $va_menu_items = array(); }
				} else {
					$va_menu_items = array();
				}

				$va_menu_items['nfai_import'] = array(
					'displayName' => 'Import EAD',
					"default" => array(
						'module' => 'nfaiTools', 
						'controller' => 'NFAIImport', 
						'action' => 'Index'
					)
				);

				$va_menu_items['nfai_export'] = array(
					'displayName' => 'Export EAD',
					"default" => array(
						'module' => 'nfaiTools', 
						'controller' => 'NFAIExport', 
						'action' => 'Index'
					)
				);
				
				$pa_menu_bar['nfaiTools_menu'] = array(
					'displayName' => "NFAI",
					'navigation' => $va_menu_items
				);
			} 
			
			return $pa_menu_bar;
		}
		# -------------------------------------------------------
		/**
		 * Add plugin user actions
		 */
		public function hookGetRoleActionList($pa_role_list) {
			$pa_role_list['plugin_nfaiTools'] = array(
				'label' => _t('nfai Import plugin'),
				'description' => _t('Actions for nfai import plugin'),
				'actions' => nfaiToolsPlugin::getRoleActionList()
			);
	
			return $pa_role_list;
		}
		# -------------------------------------------------------
		/**
		 * Get plugin user actions
		 */
		static public function getRoleActionList() {
			return array(
				'can_use_nfai_import_plugin' => array(
					'label' => _t('Can use nfai import plugin functions'),
					'description' => _t('User can use all nfai import plugin functionality, including batch nfai import.')
				)
			);	
		}
		# -------------------------------------------------------
	}
?>
