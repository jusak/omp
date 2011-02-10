{**
 * templates/common/sitenav.tpl
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Site-Wide Navigation Bar
 *
 *}

<div class="pkp_structure_header_siteNav">
	<div class="unit size1of2">
		<ul class="flat_list">
			<li><a href="{url page="index"}">{translate key="navigation.home"}</a></li>
			{if $isUserLoggedIn}
				<li><a href="{url context="index" page="admin"}">{translate key="navigation.admin"}</a></li>
			{/if}
		</ul>
	</div>
	<div class="unit size1of2 lastUnit align_right">
		<ul class="flat_list">
			<li><div id="sizer"></div></li>
			{if $isUserLoggedIn}
				<li><a href="{url page="user" op="profile"}">{translate key="user.profile"}</a></li>
				<li><a href="{url page="login" op="signOut"}">{translate key="user.logOut"}</a></li>
			{else}
				<li><a href="{url page="login"}">{translate key="navigation.login"}</a></li>
				<li><a href="{url page="user" op="register"}">{translate key="navigation.register"}</a></li>
			{/if}
			<li><a href="{url page="search"}">{translate key="navigation.search"}</a></li>
			{if !$isUserLoggedIn}
				<li><a href="#">{translate key="navigation.sitemap"}</a></li>
			{/if}
		</ul>
	</div>
</div>

