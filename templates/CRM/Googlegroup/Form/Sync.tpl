<div class="crm-block crm-form-block crm-campaignmonitor-sync-form-block">

  {if $smarty.get.state eq 'done'}
    <div class="help">
      {ts}Sync completed with result counts as:{/ts}<br/> 
      <!--<tr><td>{ts}Civi Blocked{/ts}:</td><td>{$stats.Blocked}&nbsp; (no-email / opted-out / do-not-email / on-hold)</td></tr>-->
      {foreach from=$stats item=group}
      <h2>{$group.name}</h2>
      <table class="form-layout-compressed bold">
      <tr><td>{ts}Contacts on CiviCRM{/ts}:</td><td>{$group.stats.c_count}</td></tr>
      <tr><td>{ts}Contacts on Google Group (originally){/ts}:</td><td>{$group.stats.gg_count}</td></tr>
      <tr><td>{ts}Contacts Subscribed or updated at Google Group{/ts}:</td><td>{$group.stats.added}</td></tr>
      <tr><td>{ts}Contacts Unsubscribed from Google Group{/ts}:</td><td>{$group.stats.removed}</td></tr>
      </table>
      {/foreach}
    </div>
  {/if}
  
  <div class="crm-block crm-form-block crm-campaignmonitor-sync-form-block">
    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl"}
    </div>
  </div>
  
</div>
