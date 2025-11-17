{include file='header'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.log.autoDelete{/lang}</h1>
	</div>
</header>

{if $objects|count}
	<div class="section">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID">{lang}wcf.global.objectID{/lang}</th>
					<th class="columnTitle">{lang}wcf.acp.log.autoDelete.type{/lang}</th>
					<th class="columnDate">{lang}wcf.acp.log.autoDelete.executionTime{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=log}
					<tr>
						<td class="columnID">{$log->logID}</td>
						<td class="columnTitle">
							{if $log->userID !== null}
								{* Vollständiger Log-Eintrag mit PII *}
								{lang}wcf.acp.log.autoDelete.fullEntry{/lang}
								<ul>
									<li>{lang}wcf.user.userID{/lang}: {$log->userID}</li>
									<li>{lang}wcf.user.username{/lang}: {$log->username|encodeJS}</li>
									<li>{lang}wcf.user.email{/lang}: {$log->email|encodeJS}</li>
								</ul>
							{elseif $log->usersDeletedCount !== null}
								{* Anonymer Log-Eintrag *}
								{lang}wcf.acp.log.autoDelete.anonymousEntry{/lang}: {$log->usersDeletedCount}
							{/if}
						</td>
						<td class="columnDate">{@$log->executionTime|date}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<div class="contentFooter">
		{hascontent}
			<div class="paginationBottom">
				{content}{pages print=true assign=pagesLinks controller="AutoDeleteLog" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
			</div>
		{/hascontent}
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}

