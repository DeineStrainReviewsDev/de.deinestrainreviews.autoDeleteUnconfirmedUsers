{include file='header'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.deletedUnconfirmedUsersLog.title{/lang}</h1>
	</div>
</header>

<section class="section">
	{if $objects|count}
		{pages print=true assign=pagesLinks controller="deletedUnconfirmedUsersLog" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
		<div class="tabularBox">
			<table class="table">
				<thead>
					<tr>
						<th class="columnID columnLogID{if $sortField == 'logID'} active {@$sortOrder}{/if}"><a href="{link controller='deletedUnconfirmedUsersLog'}pageNo={@$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
						<th class="columnID{if $sortField == 'userID'} active {@$sortOrder}{/if}"><a href="{link controller='deletedUnconfirmedUsersLog'}pageNo={@$pageNo}&sortField=userID&sortOrder={if $sortField == 'userID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.userID{/lang}</a></th>
						<th class="columnTitle{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='deletedUnconfirmedUsersLog'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.username{/lang}</a></th>
						<th class="columnText{if $sortField == 'email'} active {@$sortOrder}{/if}"><a href="{link controller='deletedUnconfirmedUsersLog'}pageNo={@$pageNo}&sortField=email&sortOrder={if $sortField == 'email' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.email{/lang}</a></th>
						<th class="columnDate{if $sortField == 'registrationDate'} active {@$sortOrder}{/if}"><a href="{link controller='deletedUnconfirmedUsersLog'}pageNo={@$pageNo}&sortField=registrationDate&sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.registrationDate{/lang}</a></th>
						<th class="columnDate{if $sortField == 'deletionDate'} active {@$sortOrder}{/if}"><a href="{link controller='deletedUnconfirmedUsersLog'}pageNo={@$pageNo}&sortField=deletionDate&sortOrder={if $sortField == 'deletionDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionDate{/lang}</a></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$objects item=logEntry}
						<tr>
							<td class="columnID columnLogID">{$logEntry->logID}</td>
							<td class="columnID" title="{lang}wcf.acp.deletedUnconfirmedUsersLog.userID.click{/lang}">
								{if $logEntry->userID > 0}
									{$logEntry->userID}
								{else}
									<span class="badge label" title="{lang}wcf.acp.deletedUnconfirmedUsersLog.userID.notAvailable{/lang}">N/A</span>
								{/if}
							</td>
							<td class="columnTitle">{$logEntry->username}</td>
							<td class="columnText">{$logEntry->email}</td>
							<td class="columnDate">{@$logEntry->registrationDate|time}</td>
							<td class="columnDate">{@$logEntry->deletionDate|time}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{@$pagesLinks}
	{else}
		<p class="info">{lang}wcf.acp.deletedUnconfirmedUsersLog.noEntries{/lang}</p>
	{/if}
</section>

{include file='footer'}

