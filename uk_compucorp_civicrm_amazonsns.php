<?php
require 'vendor/autoload.php';

class uk_compucorp_civicrm_amazonsns extends CRM_SMS_Provider {

  static private $_singleton;

  public function __construct() {

  }

  public static function singleton($providerParams, $force) {
    if (!isset(self::$_singleton) || $force) {
      $provider = array();

      $providerID = CRM_Utils_Array::value('provider_id', $providerParams);
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }

      self::$_singleton = new uk_compucorp_civicrm_amazonsns($provider);
    }

    return self::$_singleton;
  }

  public function send($recipients, $header, $message, $jobID = NULL, $userID = NULL) {


    $this->createActivity($sid, $message, $header, $jobID, $userID);
    return $sid;
  }
}