<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Amazonsns_Upgrader extends CRM_Extension_Upgrader_Base {

  public function install() {
    civicrm_api3( 'OptionValue','create', array(
      'name'  => 'amazonsns',
      'label' => 'Amazon SNS',
      'value' => 'uk.co.compucorp.amazonsns',
      'option_group_id' => 'sms_provider_name',
      'is_default' => 1,
      'is_active'  => 1,
    ));

    $this->runAllUpgraders();
  }

  public function uninstall() {
    civicrm_api3( 'SmsProvider','get', array(
      'name' => 'uk.co.compucorp.amazonsns',
      'api.SmsProvider.delete' => array('id' => '$value.id'),
    ));

    civicrm_api3( 'OptionValue','get', array(
      'value' => 'uk.co.compucorp.amazonsns',
      'option_group_id' => 'sms_provider_name',
      'api.OptionValue.delete' => array('id' => '$value.id'),
    ));
  }

  public function enable() {
    civicrm_api3( 'SmsProvider','get', array(
      'name' => 'uk.co.compucorp.amazonsns',
      'api.SmsProvider.create' => array(
        'id' => '$value.id',
        'is_active' => 1,
      ),
    ));
  }

  public function disable() {
    civicrm_api3( 'SmsProvider','get', array(
      'name' => 'uk.co.compucorp.amazonsns',
      'api.SmsProvider.create' => array(
        'id' => '$value.id',
        'is_active' => 0,
      ),
    ));

    civicrm_api3( 'OptionValue','get', array(
      'value' => 'uk.co.compucorp.amazonsns',
      'option_group_id' => 'sms_provider_name',
      'api.OptionValue.create' => array(
        'id' => '$value.id',
        'is_active' => 0,
      ),
    ));
  }

  private function runAllUpgraders() {
    $revisions = $this->getRevisions();

    foreach ($revisions as $revision) {
      $methodName = 'upgrade_' . $revision;

      if (is_callable([$this, $methodName])) {
        $this->{$methodName}();
      }
    }
  }

}
