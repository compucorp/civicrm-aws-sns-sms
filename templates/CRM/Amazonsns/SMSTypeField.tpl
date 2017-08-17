{* template block that contains the new field *}
<table>
  <tr id="sms-type-row">
    <td nowrap>SMS Type (Amazon SNS):</td>
    <td>{$form.sms_type.html}</td>
  </tr>
</table>
<script type="text/javascript">
  {literal}
    tr = cj('#sms-type-row').remove().clone();
    cj('.form-layout-compressed tbody').append(tr);
    cj('#sms_provider_id').on('change', validateSMSProviderName);

    function validateSMSProviderName() {
      CRM.api3('SmsProvider', 'getvalue', {
        'sequential': 1,
        'return': 'name',
        'id': cj('#sms_provider_id').val()
      }).done(function(result) {
        if (result.result == 'uk.co.compucorp.amazonsns') {
          cj('#sms-type-row').show();
          var recipientData = cj('#to').val().split('::');
          var validPhone = /^\+[1-9]\d{1,14}$/.test(recipientData[1]);
          if (!validPhone) {
            cj('#to').parent().append('<div id="amazonsnsvalidationmsg">This contact doesn\'t have a mobile phone in the required E.164 format!<br/><a href="http://docs.aws.amazon.com/sns/latest/dg/sms_publish-to-phone.html" target="_blank">More information...</a></div>');
          }
        } else {
          cj('#sms-type-row').hide();
        }
      });
    }
    validateSMSProviderName();

  {/literal}
</script>
