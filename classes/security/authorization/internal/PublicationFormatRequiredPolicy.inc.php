<?php
/**
 * @file classes/security/authorization/internal/PublicationFormatRequiredPolicy.inc.php
 *
 * Copyright (c) 2000-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PublicationFormatRequiredPolicy
 * @ingroup security_authorization_internal
 *
 * @brief Policy that ensures that the request contains a valid publication format.
 */

import('lib.pkp.classes.security.authorization.DataObjectRequiredPolicy');

class PublicationFormatRequiredPolicy extends DataObjectRequiredPolicy {
	/**
	 * Constructor
	 * @param $request PKPRequest
	 * @param $args array request parameters
	 * @param $submissionParameterName string the request parameter we expect
	 *  the submission id in.
	 */
	function PublicationFormatRequiredPolicy(&$request, &$args, $parameterName = 'publicationFormatId', $operations = null) {
		parent::DataObjectRequiredPolicy($request, $args, $parameterName, 'user.authorization.invalidPublicationFormat', $operations);
	}

	//
	// Implement template methods from AuthorizationPolicy
	//
	/**
	 * @see DataObjectRequiredPolicy::dataObjectEffect()
	 */
	function dataObjectEffect() {
		$publicationFormatId = (int)$this->getDataObjectId();
		if (!$publicationFormatId) return AUTHORIZATION_DENY;

		$publicationFormatDao =& DAORegistry::getDAO('PublicationFormatDAO');
		$publicationFormat =& $publicationFormatDao->getById($publicationFormatId);
		if (!is_a($publicationFormat, 'PublicationFormat')) return AUTHORIZATION_DENY;

		// Save the review Assignment to the authorization context.
		$this->addAuthorizedContextObject(ASSOC_TYPE_PUBLICATION_FORMAT, $publicationFormat);
		return AUTHORIZATION_PERMIT;
	}
}

?>
