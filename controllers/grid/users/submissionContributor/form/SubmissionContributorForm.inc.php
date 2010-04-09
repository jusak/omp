<?php

/**
 * @file controllers/grid/users/submissionContributor/form/SubmissionContributorForm.inc.php
 *
 * Copyright (c) 2003-2008 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SubmissionContributorForm
 * @ingroup controllers_grid_submit_submissionContributor_form
 *
 * @brief Form for adding/editing a submissionContributor
 */

import('form.Form');

class SubmissionContributorForm extends Form {
	/** The monograph associated with the submission contributor being edited **/
	var $_monographId;

	/** SubmissionContributor the submissionContributor being edited **/
	var $_submissionContributor;

	/**
	 * Constructor.
	 */
	function SubmissionContributorForm($monographId, $submissionContributor) {
		parent::Form('controllers/grid/users/submissionContributor/form/submissionContributorForm.tpl');
		assert(is_numeric($monographId));
		$this->_monographId = (int) $monographId;

		//FIXME: Author?
		//assert(!$submissionContributor || is_a($submissionContributor, 'Author'));
		$this->_submissionContributor =& $submissionContributor;

		// Validation checks for this form
		$this->addCheck(new FormValidator($this, 'firstName', 'required', 'author.submit.form.authorRequiredFields'));
		$this->addCheck(new FormValidator($this, 'lastName', 'required', 'author.submit.form.authorRequiredFields'));
		$this->addCheck(new FormValidatorEmail($this, 'email', 'required', 'installer.form.emailRequired'));
		$this->addCheck(new FormValidatorUrl($this, 'url', 'optional', 'user.profile.form.urlInvalid'));
		$this->addCheck(new FormValidatorPost($this));
	}

	//
	// Getters and Setters
	//
	/**
	* Get the submissionContributor
	* @return SubmissionContributor
	*/
	function &getSubmissionContributor() {
		return $this->_submissionContributor;
	}

	/**
	 * Get the MonographId
	 * @return int monographId
	 */
	function getMonographId() {
		return $this->_monographId;
	}

	//
	// Template methods from Form
	//
	/**
	* Initialize form data from the associated submissionContributor.
	* @param $submissionContributor SubmissionContributor
	*/
	function initData() {
		$submissionContributor =& $this->getSubmissionContributor();

		if ( $submissionContributor ) {
			$this->_data = array(
				'authorId' => $submissionContributor->getId(),
				'firstName' => $submissionContributor->getFirstName(),
				'middleName' => $submissionContributor->getMiddleName(),
				'lastName' => $submissionContributor->getLastName(),
				'affiliation' => $submissionContributor->getAffiliation(),
				'country' => $submissionContributor->getCountry(),
				'email' => $submissionContributor->getEmail(),
				'url' => $submissionContributor->getUrl(),
				'biography' => $submissionContributor->getBiography(Locale::getLocale()),
				'primaryContact' => $submissionContributor->getPrimaryContact(),
			// FIXME: need to implement roles
				'role' => 'Author'
				);
		}
	}

	/**
	 * Display the form.
	 */
	function display($request) {
		$submissionContributor =& $this->getSubmissionContributor();
		//assert(!$submissionContributor || is_a($submissionContributor, 'Author'));

		$templateMgr =& TemplateManager::getManager();
		$countryDao =& DAORegistry::getDAO('CountryDAO');
		$countries =& $countryDao->getCountries();
		$templateMgr->assign_by_ref('countries', $countries);

		$templateMgr->assign('monographId', $this->getMonographId());

		parent::display($request);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('authorId',
									'firstName',
									'middleName',
									'lastName',
									'affiliation',
									'country',
									'email',
									'url',
									'biography',
									'primaryContact'));
	}

	/**
	 * Save submissionContributor
	 */
	function execute() {
		$authorDao =& DAORegistry::getDAO('AuthorDAO');
		$monographId = $this->getMonographId();

		$submissionContributor =& $this->getSubmissionContributor();
		if (!$submissionContributor) {
			// this is a new submission contributor
			$submissionContributor =& new Author();
			$submissionContributor->setMonographId($monographId);
			$existingSubmissionContributor = false;
		} else {
			$existingSubmissionContributor = true;
		}

		assert($monographId == $submissionContributor->getMonographId());

		$submissionContributor->setFirstName($this->getData('firstName'));
		$submissionContributor->setMiddleName($this->getData('middleName'));
		$submissionContributor->setLastName($this->getData('lastName'));
		$submissionContributor->setAffiliation($this->getData('affiliation'));
		$submissionContributor->setCountry($this->getData('country'));
		$submissionContributor->setEmail($this->getData('email'));
		$submissionContributor->setUrl($this->getData('url'));
		$submissionContributor->setBiography($this->getData('biography'), Locale::getLocale()); // localized
		$submissionContributor->setPrimaryContact(($this->getData('primaryContact') ? true : false));

		if ($existingSubmissionContributor) {
			$authorDao->updateAuthor($submissionContributor);
			$authorId = $submissionContributor->getId();
		} else {
			$authorId = $authorDao->insertAuthor($submissionContributor);
		}

		return $authorId;
	}
}

?>