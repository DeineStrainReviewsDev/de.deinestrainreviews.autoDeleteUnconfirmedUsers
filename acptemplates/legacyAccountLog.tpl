{include file='header' pageTitle='wcf.acp.legacyAccountLog.title'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.legacyAccountLog.title{/lang}</h1>
		<p class="contentHeaderDescription">{lang maxAge=$maxAge}wcf.acp.legacyAccountLog.description{/lang}</p>
	</div>
</header>

<form method="post" action="{link controller='LegacyAccountLog'}{/link}" class="legacyAccountLogFilter">
    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>

        <div class="row rowColGap formGrid">
            <dl class="col-xs-12 col-md-2">
                <dt><label for="userID">{lang}wcf.acp.legacyAccountLog.userID{/lang}</label></dt>
                <dd>
                    <input type="number" id="userID" name="filter[userID]" value="{$filter[userID]}" class="long">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-5">
                <dt><label for="registrationFromDate">{lang}wcf.acp.legacyAccountLog.registrationDate{/lang}</label></dt>
                <dd class="dateRangeInput">
                    <input type="date" name="filter[registrationFromDate]" id="registrationFromDate" value="{$filter[registrationFromDate]}" placeholder="{lang}wcf.date.period.start{/lang}">
                    <span class="dateRangeSeparator">&ndash;</span>
                    <input type="date" name="filter[registrationToDate]" id="registrationToDate" value="{$filter[registrationToDate]}" placeholder="{lang}wcf.date.period.end{/lang}">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-5">
                <dt><label for="detectionFromDate">{lang}wcf.acp.legacyAccountLog.detectionDate{/lang}</label></dt>
                <dd class="dateRangeInput">
                    <input type="date" name="filter[detectionFromDate]" id="detectionFromDate" value="{$filter[detectionFromDate]}" placeholder="{lang}wcf.date.period.start{/lang}">
                    <span class="dateRangeSeparator">&ndash;</span>
                    <input type="date" name="filter[detectionToDate]" id="detectionToDate" value="{$filter[detectionToDate]}" placeholder="{lang}wcf.date.period.end{/lang}">
                </dd>
            </dl>
        </div>

        <div class="formSubmit">
            <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        </div>
    </section>
</form>

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="LegacyAccountLog" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&$filterParameter"}
		{/content}
	</div>
{/hascontent}

{if $items}
	<div class="section tabularBox">
		<table data-type="de.deinestrainreviews.legacyAccount" class="table jsClipboardContainer">
			<thead>
				<tr>
					<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll"></label></th>
						<th class="columnID columnUserID{if $sortField == 'userID'} active {$sortOrder}{/if}"><a href="{link controller='LegacyAccountLog'}pageNo={$pageNo}&sortField=userID&sortOrder={if $sortField == 'userID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.legacyAccountLog.userID{/lang}</a></th>
						<th class="columnText columnUsername">{lang}wcf.acp.legacyAccountLog.username{/lang}</th>
						<th class="columnText columnEmail">{lang}wcf.acp.legacyAccountLog.email{/lang}</th>
						<th class="columnDate columnRegistrationDate{if $sortField == 'registrationDate'} active {$sortOrder}{/if}"><a href="{link controller='LegacyAccountLog'}pageNo={$pageNo}&sortField=registrationDate&sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.legacyAccountLog.registrationDate{/lang}</a></th>
						<th class="columnDate columnDetectionDate{if $sortField == 'detectionDate'} active {$sortOrder}{/if}"><a href="{link controller='LegacyAccountLog'}pageNo={$pageNo}&sortField=detectionDate&sortOrder={if $sortField == 'detectionDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.legacyAccountLog.detectionDate{/lang}</a></th>
					</tr>
				</thead>

			<tbody class="jsReloadPageWhenEmpty">
				{foreach from=$objects item=logEntry}
					<tr class="jsClipboardObject" data-object-id="{$logEntry->logID}">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{$logEntry->logID}"></td>
							<td class="columnID columnUserID">
								<span class="badge jsTooltip" style="cursor: pointer;" title="{lang}wcf.acp.legacyAccountLog.userID.click{/lang}" data-clipboard-text="{$logEntry->userID}">{$logEntry->userID}</span>
							</td>
							<td class="columnText columnUsername">{$logEntry->username}</td>
							<td class="columnText columnEmail">{$logEntry->email}</td>
							<td class="columnDate columnRegistrationDate">{time time=$logEntry->registrationDate}</td>
							<td class="columnDate columnDetectionDate">{time time=$logEntry->detectionDate}</td>
						</tr>
					{/foreach}
			</tbody>
		</table>
	</div>

	<footer class="contentFooter">
		{hascontent}
			<div class="paginationBottom">
				{content}{unsafe:$pagesLinks}{/content}
			</div>
		{/hascontent}
	</footer>
{else}
	<woltlab-core-notice type="info">{lang}wcf.acp.legacyAccountLog.noEntries{/lang}</woltlab-core-notice>
{/if}

{if $__wcf->session->getVar('legacyAccountDeleteSuccess')}
	<woltlab-core-notice type="success">
		{lang count=$__wcf->session->getVar('legacyAccountDeleteSuccess')}wcf.acp.legacyAccountLog.deleteSuccess{/lang}
	</woltlab-core-notice>
	{assign var='_' value=$__wcf->session->unregister('legacyAccountDeleteSuccess')}
{/if}

{if $__wcf->session->getVar('legacyAccountDeleteFailed')}
	<woltlab-core-notice type="error">
		{lang count=$__wcf->session->getVar('legacyAccountDeleteFailed')}wcf.acp.legacyAccountLog.deleteFailed{/lang}
	</woltlab-core-notice>
	{assign var='_' value=$__wcf->session->unregister('legacyAccountDeleteFailed')}
{/if}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Controller/Clipboard', 'WoltLabSuite/Core/Ui/Notification'], (ControllerClipboard, UiNotification) => {
		// Initialize clipboard for bulk actions
		ControllerClipboard.setup({
			hasMarkedItems: {if $hasMarkedItems}true{else}false{/if},
			pageClassName: 'wcf\\acp\\page\\LegacyAccountLogPage'
		});

		// Copy to clipboard functionality for user IDs
		document.querySelectorAll('[data-clipboard-text]').forEach(function(element) {
			element.addEventListener('click', function() {
				var text = this.getAttribute('data-clipboard-text');
				navigator.clipboard.writeText(text).then(function() {
					UiNotification.show(null, function() { return '{jslang}wcf.acp.legacyAccountLog.userID.copied{/jslang}'; });
				});
			});
		});
	});
</script>

{include file='footer'}