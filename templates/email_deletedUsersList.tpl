{* Template for deleted unconfirmed users list in admin notification emails *}
<table style="border-collapse: collapse; width: 100%; margin: 20px 0;">
    <thead>
        <tr style="background-color: #f0f0f0; border-bottom: 2px solid #ddd;">
            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">{lang}wcf.acp.deletedUnconfirmedUsersLog.username{/lang}</th>
            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">{lang}wcf.acp.deletedUnconfirmedUsersLog.email{/lang}</th>
            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">{lang}wcf.acp.deletedUnconfirmedUsersLog.registrationDate{/lang}</th>
            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">{lang}wcf.acp.deletedUnconfirmedUsersLog.deletionDate{/lang}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$anonymizedUsers item=user}
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px; border: 1px solid #ddd;">{$user.username}</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$user.email}</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$user.registrationDate|date:'Y-m-d H:i:s'}</td>
                <td style="padding: 8px; border: 1px solid #ddd;">{$user.deletionDate|date:'Y-m-d H:i:s'}</td>
            </tr>
        {/foreach}
    </tbody>
</table>
