<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'amazonsns.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function amazonsns_civicrm_config(&$config) {
  _amazonsns_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function amazonsns_civicrm_install() {
  _amazonsns_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function amazonsns_civicrm_enable() {
  _amazonsns_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_buildForm, to add SMS Type field required by Amazon
 * SNS, and to check if phone numbers being used on a batch SMS sending job are
 * valid E.164 numbers.
 *
 * @param string $formName
 * @param object $form
 */
function amazonsns_civicrm_buildForm($formName, &$form) {

  // Add SMS Type Field
  if ($formName == 'CRM_Contact_Form_Task_SMS' || $formName == 'CRM_SMS_Form_Upload') {

    $amazonSMSTypes = array('Promotional' => ts('Promotional'), 'Transactional' => ts('Transactional'));
    $form->add('select', 'sms_type', ts('SMS Type'), $amazonSMSTypes, TRUE);

    $templatePath = realpath(dirname(__FILE__)."/templates");
    $template = ($formName == 'CRM_Contact_Form_Task_SMS' ? 'SMSTypeField.tpl' : 'BatchSMSTypeField.tpl');

    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "{$templatePath}/CRM/Amazonsns/$template"
    ));
  }

  // Find Invalid Phones on Batch SMS Sending
  if ($formName == 'CRM_SMS_Form_Upload') {
    $invalidPhonesCount = CRM_Amazonsns_SMS_PhoneValidator::countMailingInvalidPhones($form->_mailingID);

    if ($invalidPhonesCount > 0) {
      $invalidPhones = CRM_Amazonsns_SMS_PhoneValidator::getMailingInvalidPhonesSample($form->_mailingID);

      $form->assign('invalidPhonesCount', $invalidPhonesCount);
      $form->assign('invalidPhones', $invalidPhones);
    }
  }
}

/**
 * Implements hook_civicrm_postProcess, to store SMS Type field selected for a
 * batch SMS mailing job as a template option, so it may be available when the
 * actual SMS sending operation is executed.
 *
 * @param string $formName
 * @param object $form
 */
function amazonsns_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_SMS_Form_Upload') {
    $smsType = CRM_Utils_Request::retrieve('sms_type', 'String') ?: 'Promotional';

    civicrm_api3('Mailing', 'create', array(
      'id' => $form->_mailingID,
      'template_options' => array('sms_type' => $smsType),
    ));
  }
}
