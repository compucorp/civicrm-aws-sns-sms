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

  cj('#to').parent().append('<div id="amazonsnsvalidationmsg" class="messages error"></div>');
  cj('#amazonsnsvalidationmsg').hide();

  cj('#sms_provider_id').on('change', validateSMSProviderName);

  function validateSMSProviderName() {
    CRM.api3('SmsProvider', 'getvalue', {
      'sequential': 1,
      'return': 'name',
      'id': cj('#sms_provider_id').val()
    }).done(function(result) {
      if (result.result == 'uk.co.compucorp.amazonsns') {
        cj('#sms-type-row').show();

        var invalidContacts = [];
        var recipients = cj('#to').val().split(',');

        for (var i = 0; i < recipients.length; i++) {
          var recipientData = recipients[i].split('::');
          var validPhone = /^\+[1-9]\d{1,14}$/.test(recipientData[1]);

          if (!validPhone) {
            invalidContacts.push(recipientData[0]);
          }
        }

        if (invalidContacts.length > 0) {
          CRM.api3('Contact', 'get', {
            'sequential': 1,
            'return': ['display_name','id','phone'],
            'id': {'IN': invalidContacts}
          }).done(function (result) {
            var msg = '';

            if (result.count > 1) {
              msg += '<p>Found ' + result.count + ' invalid phones on selected contacts! Phone numbers to be used to send SMS messages through Amazon SNS require to follow the E.164 format!</p>';
            } else {
              msg += '<p>Found an invalid phone on selected contacts! Phone numbers to be used to send SMS messages through Amazon SNS require to follow the E.164 format!</p>';
            }

            msg += '<p><a href="http://docs.aws.amazon.com/sns/latest/dg/sms_publish-to-phone.html" target="_blank">More information...</a></p>';
            msg += '<p>Invalid phones:</p>';
            msg += '<ul>';

            for (i = 0; i < result.values.length; i++) {
              url = CRM.url('civicrm/contact/view', '{reset: 1, cid: ' + result.values[i].id + '}');
              msg += '<li><a href="' + url + '">' + result.values[i].display_name + ' - ' + result.values[i].phone + '</a></li>';
            }

            msg += '</ul>';

            cj('#amazonsnsvalidationmsg').html(msg);
            cj('#amazonsnsvalidationmsg').show();
          });

        } else {
          cj('#amazonsnsvalidationmsg').hide();
          cj('#amazonsnsvalidationmsg').html('');
        }
      } else {
        cj('#sms-type-row').hide();
        cj('#amazonsnsvalidationmsg').hide();
        cj('#amazonsnsvalidationmsg').html('');
      }
    });
  }
  validateSMSProviderName();
  {/literal}
</script>
