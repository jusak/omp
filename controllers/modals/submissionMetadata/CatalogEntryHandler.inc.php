<?php

/**
 * @file controllers/modals/submissionMetadata/CatalogEntryHandler.inc.php
 *
 * Copyright (c) 2003-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CatalogEntryHandler
 * @ingroup controllers_modals_submissionMetadata
 *
 * @brief Handle the request to generate the tab structure on the New Catalog Entry page.
 */

// Import the base Handler.
import('classes.handler.Handler');

class CatalogEntryHandler extends Handler {

	/** The monograph **/
	var $_monograph;

	/** The current stage id **/
	var $_stageId;

	/** the current tab position **/
	var $_tabPosition;

	/**
	 * Constructor.
	 */
	function CatalogEntryHandler() {
		parent::Handler();
		$this->addRoleAssignment(
			array(ROLE_ID_SERIES_EDITOR, ROLE_ID_PRESS_MANAGER),
			array('fetch'));
	}


	//
	// Overridden methods from Handler
	//
	/**
	 * @see PKPHandler::initialize()
	 */
	function initialize(&$request, $args = null) {
		parent::initialize($request, $args);

		$monographDao =& DAORegistry::getDAO('MonographDAO');
		$this->_monograph =& $this->getAuthorizedContextObject(ASSOC_TYPE_MONOGRAPH);
		$this->_stageId =& $this->getAuthorizedContextObject(ASSOC_TYPE_WORKFLOW_STAGE);
		$this->_tabPosition = (int) $request->getUserVar('tabPos');

		// Load grid-specific translations
		AppLocale::requireComponents(LOCALE_COMPONENT_APPLICATION_COMMON, LOCALE_COMPONENT_OMP_SUBMISSION);
		$this->setupTemplate();
	}

	/**
	 * @see PKPHandler::authorize()
	 * @param $request PKPRequest
	 * @param $args array
	 * @param $roleAssignments array
	 */
	function authorize(&$request, $args, $roleAssignments) {
		$stageId = (int) $request->getUserVar('stageId');
		import('classes.security.authorization.OmpWorkflowStageAccessPolicy');
		$this->addPolicy(new OmpWorkflowStageAccessPolicy($request, $args, $roleAssignments, 'monographId', $stageId));
		return parent::authorize($request, $args, $roleAssignments);
	}


	//
	// Getters and Setters
	//
	/**
	 * Get the Monograph
	 * @return Monograph
	 */
	function getMonograph() {
		return $this->_monograph;
	}

	/**
	 * Get the stage id
	 * @return int
	 */
	function getStageId() {
		return $this->_stageId;
	}

	/**
	 * Get the current tab position
	 * @return int
	 */
	function getTabPosition() {
		return $this->_tabPosition;
	}


	//
	// Public handler methods
	//
	/**
	 * Display the tabs index page.
	 * @param $request PKPRequest
	 * @param $args array
	 */
	function fetch($request, $args) {
		$templateMgr =& TemplateManager::getManager();

		$monograph =& $this->getMonograph();

		$templateMgr->assign('monographId', $monograph->getId());
		$templateMgr->assign('stageId', $this->getStageId());

		// check to see if this monograph has been published yet
		$publishedMonographDao =& DAORegistry::getDAO('PublishedMonographDAO');
		$publishedMonograph =& $publishedMonographDao->getById($monograph->getId());
		if ($publishedMonograph !== null) {
			$templateMgr->assign('published', true);
			$templateMgr->assign('monographId', $monograph->getId());
			$tabPosition = (int) $this->getTabPosition();
			if ($tabPosition == 0) {
				$tabPosition = 1; // prevents a reload of the (disabled) submission tab now.  Show catalog instead.
			}
			$templateMgr->assign('selectedTab', $tabPosition);

			// load in any publication formats assigned to this published monograph
			$assignedPublicationFormatDao =& DAORegistry::getDAO('AssignedPublicationFormatDAO');
			$formats =& $assignedPublicationFormatDao->getFormatsByMonographId($monograph->getId());
			$publicationFormats = array();
			while ($publicationFormat =& $formats->next()) {
				$publicationFormats[] =& $publicationFormat;
			}

			$templateMgr->assign_by_ref('publicationFormats', $publicationFormats);
		}

		$this->setupTemplate();
		return $templateMgr->fetchJson('controllers/modals/submissionMetadata/catalogEntryTabs.tpl');
	}
}

?>
