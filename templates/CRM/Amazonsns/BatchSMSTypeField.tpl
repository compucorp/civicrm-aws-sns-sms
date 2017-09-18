{* template block that contains the new field *}
<table style="display: none">
  <tr id="sms-type-row">
    <td nowrap>SMS Type (Amazon SNS):</td>
    <td>{$form.sms_type.html}</td>
  </tr>
  <tr id="amazonsns-phone-validation">
    <td colspan="2">
      <div id="amazonsns-validation-alert">
        <p>Found {$invalidPhonesCount} invalid phone{if $invalidPhonesCount > 1}s{/if} on selected groups! Phone numbers to be used to send SMS messages through Amazon SNS require to follow the E.164 format!</p>
        <p><a href="http://docs.aws.amazon.com/sns/latest/dg/sms_publish-to-phone.html" target="_blank">More information...</a></p>
        <p>Invalid phones:</p>
        <ul>
          {foreach from=$invalidPhones item="phoneData"}
            <li>
              <a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$phoneData.contact_id`"}">
                {$phoneData.contact_name} - {$phoneData.phone}
              </a>
            </li>
          {/foreach}
          {if $invalidPhonesCount > 20}
            <li>...</li>
          {/if}
        </ul>
      </div>
    </td>
  </tr>
</table>
<script type="text/javascript">
  {literal}
  smsTypeRow = cj('#sms-type-row').remove().clone();
  smsTypeRow.hide();
  cj('.form-layout-compressed tbody').append(smsTypeRow);

  phoneValidationRow = cj('#amazonsns-phone-validation').remove().clone();
  phoneValidationRow.hide();
  cj('.form-layout-compressed tbody').append(phoneValidationRow);

  cj('#sms_provider_id').on('change', validateSMSProviderName);

  function validateSMSProviderName() {
    CRM.api3('SmsProvider', 'getvalue', {
      'sequential': 1,
      'return': 'name',
      'id': cj('#sms_provider_id').val()
    }).done(function(result) {
      if (result.result == 'uk.co.compucorp.amazonsns') {
        cj('#sms-type-row').show();
        cj('#amazonsns-validation-alert').addClass('messages warning');
        cj('#amazonsns-phone-validation').show();
      } else {
        cj('#sms-type-row').hide();
        cj('#amazonsns-phone-validation').hide();
      }
    });
  }
  validateSMSProviderName();
  {/literal}
</script>
