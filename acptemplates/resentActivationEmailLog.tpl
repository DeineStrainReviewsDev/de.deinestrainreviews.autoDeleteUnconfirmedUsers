{include file='header'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.resentActivationEmailLog.title{/lang}</h1>
	</div>
</header>

<section class="section">
	{if $objects|count}
		{pages print=true assign=pagesLinks controller="resentActivationEmailLog" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
		<div class="tabularBox">
			<table class="table">
				<thead>
					<tr>
						<th class="columnID columnLogID{if $sortField == 'logID'} active {@$sortOrder}{/if}"><a href="{link controller='resentActivationEmailLog'}pageNo={@$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
						<th class="columnID{if $sortField == 'userID'} active {@$sortOrder}{/if}"><a href="{link controller='resentActivationEmailLog'}pageNo={@$pageNo}&sortField=userID&sortOrder={if $sortField == 'userID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.resentActivationEmailLog.userID{/lang}</a></th>
						<th class="columnDate{if $sortField == 'registrationDate'} active {@$sortOrder}{/if}"><a href="{link controller='resentActivationEmailLog'}pageNo={@$pageNo}&sortField=registrationDate&sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.resentActivationEmailLog.registrationDate{/lang}</a></th>
						<th class="columnDate{if $sortField == 'resendEmailDate'} active {@$sortOrder}{/if}"><a href="{link controller='resentActivationEmailLog'}pageNo={@$pageNo}&sortField=resendEmailDate&sortOrder={if $sortField == 'resendEmailDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.resentActivationEmailLog.resendEmailDate{/lang}</a></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$objects item=logEntry}
						<tr>
							<td class="columnID columnLogID">{$logEntry->logID}</td>
							<td class="columnID" title="{lang}wcf.acp.resentActivationEmailLog.userID.click{/lang}">{$logEntry->userID}</td>
							<td class="columnDate">{@$logEntry->registrationDate|time}</td>
							<td class="columnDate">{@$logEntry->resendEmailDate|time}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{@$pagesLinks}
	{else}
		<p class="info">{lang}wcf.acp.resentActivationEmailLog.noEntries{/lang}</p>
	{/if}
</section>

{include file='footer'}

