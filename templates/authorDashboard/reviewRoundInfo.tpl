{**
 * details.tpl
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display monograph details (metadata, file grid)
 *}

<!--  Display round status -->
{include file="common/reviewRoundStatus.tpl" round=$round roundStatus=$roundStatus}

<!-- Display editor's message to the author -->
{if $monographEmails}
	<h6>{translate key="editor.review.personalMessageFromEditor"}:</h6>
	{iterate from=monographEmails item=monographEmail}
		<textarea class="pkp_authorDashboard_editorPersonalMessage" disabled=true class="textArea">{$monographEmail->getBody()}</textarea>
	{/iterate}
	<br />
{/if}

<!-- Display review attachments grid -->
{if $showReviewAttachments}
	{url|assign:reviewAttachmentsGridUrl router=$smarty.const.ROUTE_COMPONENT component="grid.files.attachment.AuthorReviewAttachmentsGridHandler" op="fetchGrid" monographId=$monograph->getId() escape=false}
	{load_url_in_div id="reviewAttachmentsGridContainer" url="$reviewAttachmentsGridUrl"}
{/if}