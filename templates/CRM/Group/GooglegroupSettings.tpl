<table id="googlegroup_settings" style="display: none">
<tr class="custom_field-row google_group" id="google_group_tr">
    <td class="label">{$form.google_group.label}</td>
    <td class="html-adjust">{$form.google_group.html}</td>
</tr>
</table>

{literal}
<script>
cj( document ).ready(function() {
    var googlegroup_settings = cj('#googlegroup_settings').html();
    googlegroup_settings = googlegroup_settings.replace("<tbody>", "");
    googlegroup_settings = googlegroup_settings.replace("</tbody>", "");
    cj("input[data-crm-custom='Googlegroup_Settings:Googlegroup_Group']").parent().parent().after(googlegroup_settings);
    cj("input[data-crm-custom='Googlegroup_Settings:Googlegroup_Group']").parent().parent().hide();
    cj("#google_group").change(function() {
        var group_id = cj("#google_group :selected").val();
        cj("input[data-crm-custom='Googlegroup_Settings:Googlegroup_Group']").val(group_id);
    });
});
</script>
{/literal}