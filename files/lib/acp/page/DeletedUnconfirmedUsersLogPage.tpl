{include file='header'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.deletedUnconfirmedUsersLog.title{/lang}</h1>
	</div>
</header>

<section class="section">
	<h2 class="sectionTitle">{lang}wcf.acp.deletedUnconfirmedUsersLog.title{/lang}</h2>
	
	{if $logEntries|count}
		<div class="tabularBox">
			<table class="table">
				<thead>
					<tr>
						<th class="columnID columnLogID">{lang}wcf.global.objectID{/lang}</th>
						<th class="columnTitle">{lang}wcf.acp.deletedUnconfirmedUsersLog.username{/lang}</th>
						<th class="columnText">{lang}wcf.acp.deletedUnconfirmedUsersLog.email{/lang}</th>
						<th class="columnDate">{lang}wcf.acp.deletedUnconfirmedUsersLog.registrationDate{/lang}</th>
						<th class="columnDate">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionDate{/lang}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$logEntries item=logEntry}
						<tr>
							<td class="columnID columnLogID">{$logEntry->logID}</td>
							<td class="columnTitle">{$logEntry->username}</td>
							<td class="columnText">{$logEntry->email}</td>
							<td class="columnDate">{@$logEntry->registrationDate|time}</td>
							<td class="columnDate">{@$logEntry->deletionDate|time}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{else}
		<p class="info">{lang}wcf.acp.deletedUnconfirmedUsersLog.noEntries{/lang}</p>
	{/if}
</section>

{include file='footer'}

