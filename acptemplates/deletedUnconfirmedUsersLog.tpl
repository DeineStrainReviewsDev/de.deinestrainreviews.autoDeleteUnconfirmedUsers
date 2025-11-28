{include file='header' pageTitle='wcf.acp.deletedUnconfirmedUsersLog.title'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.deletedUnconfirmedUsersLog.title{/lang}</h1>
	</div>
</header>

<form method="post" action="{link controller='DeletedUnconfirmedUsersLog'}{/link}" class="deletedUnconfirmedUsersLogFilter">
    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>

        <div class="row rowColGap formGrid">
            <dl class="col-xs-12 col-md-3">
                <dt><label for="userID">{lang}wcf.acp.deletedUnconfirmedUsersLog.userID{/lang}</label></dt>
                <dd>
                    <input type="number" id="userID" name="filter[userID]" value="{$filter[userID]}" class="long">
                </dd>
            </dl>
            
            <dl class="col-xs-12 col-md-9">
                <dt><label for="registrationFromDate">{lang}wcf.acp.deletedUnconfirmedUsersLog.registrationDate{/lang}</label></dt>
                <dd class="dateRangeInput">
                    <input type="date" name="filter[registrationFromDate]" id="registrationFromDate" value="{$filter[registrationFromDate]}" placeholder="{lang}wcf.date.period.start{/lang}">
                    <span class="dateRangeSeparator">&ndash;</span>
                    <input type="date" name="filter[registrationToDate]" id="registrationToDate" value="{$filter[registrationToDate]}" placeholder="{lang}wcf.date.period.end{/lang}">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-3">
                <dt><label for="deletionType">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType{/lang}</label></dt>
                <dd>
                    <select id="deletionType" name="filter[deletionType]">
                        <option value="">{lang}wcf.global.noSelection{/lang}</option>
                        <option value="automatic"{if $filter[deletionType] == 'automatic'} selected{/if}>{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.automatic{/lang}</option>
                        <option value="manual"{if $filter[deletionType] == 'manual'} selected{/if}>{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.manual{/lang}</option>
                        <option value="silent"{if $filter[deletionType] == 'silent'} selected{/if}>{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.silent{/lang}</option>
                    </select>
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-9">
                <dt><label for="deletionFromDate">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionDate{/lang}</label></dt>
                <dd class="dateRangeInput">
                    <input type="date" name="filter[deletionFromDate]" id="deletionFromDate" value="{$filter[deletionFromDate]}" placeholder="{lang}wcf.date.period.start{/lang}">
                    <span class="dateRangeSeparator">&ndash;</span>
                    <input type="date" name="filter[deletionToDate]" id="deletionToDate" value="{$filter[deletionToDate]}" placeholder="{lang}wcf.date.period.end{/lang}">
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
			{pages print=true assign=pagesLinks controller="DeletedUnconfirmedUsersLog" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&$filterParameter"}
		{/content}
	</div>
{/hascontent}

{if $items}
	<div class="section tabularBox">
		<table class="table">
				<thead>
					<tr>
						<th class="columnID columnLogID{if $sortField == 'logID'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
						<th class="columnID{if $sortField == 'userID'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=userID&sortOrder={if $sortField == 'userID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.userID{/lang}</a></th>
						<th class="columnTitle{if $sortField == 'username'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.username{/lang}</a></th>
						<th class="columnText{if $sortField == 'email'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=email&sortOrder={if $sortField == 'email' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.email{/lang}</a></th>
						<th class="columnDate{if $sortField == 'registrationDate'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=registrationDate&sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.registrationDate{/lang}</a></th>
						<th class="columnDate{if $sortField == 'deletionDate'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=deletionDate&sortOrder={if $sortField == 'deletionDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionDate{/lang}</a></th>
						<th class="columnText{if $sortField == 'deletionType'} active {$sortOrder}{/if}"><a href="{link controller='DeletedUnconfirmedUsersLog'}pageNo={$pageNo}&sortField=deletionType&sortOrder={if $sortField == 'deletionType' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType{/lang}</a></th>
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
							<td class="columnDate">{time time=$logEntry->registrationDate}</td>
							<td class="columnDate">{time time=$logEntry->deletionDate}</td>
							<td class="columnText">
								{if !$logEntry->deletionType || $logEntry->deletionType == ''}
									<span class="badge blue">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.notAvailable{/lang}</span>
									<span class="icon icon16 fa-question-circle jsTooltip" title="{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.notAvailable.tooltip{/lang}"></span>
								{elseif $logEntry->deletionType == 'manual'}
									<span class="badge orange">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.manual{/lang}</span>
									<span class="icon icon16 fa-question-circle jsTooltip" title="{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.manual.tooltip{/lang}"></span>
								{elseif $logEntry->deletionType == 'silent'}
									<span class="badge yellow">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.silent{/lang}</span>
									<span class="icon icon16 fa-question-circle jsTooltip" title="{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.silent.tooltip{/lang}"></span>
								{elseif $logEntry->deletionType == 'automatic'}
									<span class="badge green">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.automatic{/lang}</span>
									<span class="icon icon16 fa-question-circle jsTooltip" title="{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionType.automatic.tooltip{/lang}"></span>
								{else}
									<span class="badge label">{$logEntry->deletionType}</span>
								{/if}
							</td>
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
	<woltlab-core-notice type="info">{lang}wcf.acp.deletedUnconfirmedUsersLog.noEntries{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}