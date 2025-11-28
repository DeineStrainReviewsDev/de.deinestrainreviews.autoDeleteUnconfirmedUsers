{include file='header' pageTitle='wcf.acp.resentActivationEmailLog.title'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.resentActivationEmailLog.title{/lang}</h1>
	</div>
</header>

<form method="post" action="{link controller='ResentActivationEmailLog'}{/link}" class="resentActivationEmailLogFilter">
    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>

        <div class="row rowColGap formGrid">
            <dl class="col-xs-12 col-md-2">
                <dt><label for="userID">{lang}wcf.acp.resentActivationEmailLog.userID{/lang}</label></dt>
                <dd>
                    <input type="number" id="userID" name="filter[userID]" value="{$filter[userID]}" class="long">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-5">
                <dt><label for="registrationFromDate">{lang}wcf.acp.resentActivationEmailLog.registrationDate{/lang}</label></dt>
                <dd class="dateRangeInput">
                    <input type="date" name="filter[registrationFromDate]" id="registrationFromDate" value="{$filter[registrationFromDate]}" placeholder="{lang}wcf.date.period.start{/lang}">
                    <span class="dateRangeSeparator">&ndash;</span>
                    <input type="date" name="filter[registrationToDate]" id="registrationToDate" value="{$filter[registrationToDate]}" placeholder="{lang}wcf.date.period.end{/lang}">
                </dd>
            </dl>

            <dl class="col-xs-12 col-md-5">
                <dt><label for="resendFromDate">{lang}wcf.acp.resentActivationEmailLog.resendEmailDate{/lang}</label></dt>
                <dd class="dateRangeInput">
                    <input type="date" name="filter[resendFromDate]" id="resendFromDate" value="{$filter[resendFromDate]}" placeholder="{lang}wcf.date.period.start{/lang}">
                    <span class="dateRangeSeparator">&ndash;</span>
                    <input type="date" name="filter[resendToDate]" id="resendToDate" value="{$filter[resendToDate]}" placeholder="{lang}wcf.date.period.end{/lang}">
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
			{pages print=true assign=pagesLinks controller="ResentActivationEmailLog" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&$filterParameter"}
		{/content}
	</div>
{/hascontent}

{if $items}
	<div class="section tabularBox">
		<table class="table">
				<thead>
					<tr>
						<th class="columnID columnLogID{if $sortField == 'logID'} active {$sortOrder}{/if}"><a href="{link controller='ResentActivationEmailLog'}pageNo={$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
						<th class="columnID{if $sortField == 'userID'} active {$sortOrder}{/if}"><a href="{link controller='ResentActivationEmailLog'}pageNo={$pageNo}&sortField=userID&sortOrder={if $sortField == 'userID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.resentActivationEmailLog.userID{/lang}</a></th>
						<th class="columnDate{if $sortField == 'registrationDate'} active {$sortOrder}{/if}"><a href="{link controller='ResentActivationEmailLog'}pageNo={$pageNo}&sortField=registrationDate&sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.resentActivationEmailLog.registrationDate{/lang}</a></th>
						<th class="columnDate{if $sortField == 'resendEmailDate'} active {$sortOrder}{/if}"><a href="{link controller='ResentActivationEmailLog'}pageNo={$pageNo}&sortField=resendEmailDate&sortOrder={if $sortField == 'resendEmailDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&{$filterParameter}{/link}">{lang}wcf.acp.resentActivationEmailLog.resendEmailDate{/lang}</a></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$objects item=logEntry}
						<tr>
							<td class="columnID columnLogID">{$logEntry->logID}</td>
							<td class="columnID" title="{lang}wcf.acp.resentActivationEmailLog.userID.click{/lang}">{$logEntry->userID}</td>
							<td class="columnDate">{time time=$logEntry->registrationDate}</td>
							<td class="columnDate">{time time=$logEntry->resendEmailDate}</td>
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
	<woltlab-core-notice type="info">{lang}wcf.acp.resentActivationEmailLog.noEntries{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}