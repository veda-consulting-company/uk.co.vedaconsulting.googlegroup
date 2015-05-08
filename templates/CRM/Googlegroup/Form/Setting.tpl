<div class="crm-block crm-form-block crm-googlegroup-setting-form-block">
  <div class="crm-accordion-wrapper crm-accordion_googlegroup_setting-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}API Key Setting{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      <table class="form-layout-compressed">
        <tr class="crm-googlegroup-setting-api-key-block">
          <td class="label">{$form.client_key.label}</td>
          <td>{$form.client_key.html}</td>
        </tr>
        <tr class="crm-webinar-setting-api-key-email">
          <td class="label">{$form.client_secret.label}</td>
          <td>{$form.client_secret.html}</td>
        </tr>
      </table>
    </div>
    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl"}
    </div>
  </div>
</div>
    
