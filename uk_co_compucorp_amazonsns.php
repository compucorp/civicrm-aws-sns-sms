<?php
use \Aws\Sns\SnsClient as SNSClient;

class uk_co_compucorp_amazonsns extends CRM_SMS_Provider {

  static private $_singleton;

  public $_apiURL;
  private $snsClient;
  private $senderID;

  public function __construct($providerParameters) {
    if ($this->validateProviderParameters($providerParameters)) {
      $params = array(
        'credentials' => array(
          'key' => $providerParameters['username'],
          'secret' => $providerParameters['password'],
        ),
        'region' => $providerParameters['api_params']['region'],
        'version' => $providerParameters['api_params']['version'],
      );
      $this->snsClient = new SNSClient($params);
    }

    if (!empty($providerParameters['SenderID'])) {
      $this->senderID = $providerParameters['SenderID'];
    }
  }

  private function validateProviderParameters($parameters) {

    switch (true) {
      case empty($parameters['username']):
      case empty($parameters['password']):
      case empty($parameters['api_params']['region']):
      case empty($parameters['api_params']['version']):
        $valid = FALSE;
        break;

      default:
        $valid = TRUE;
    }

    return $valid;
  }

  public static function singleton($providerParams, $force) {
    if (!isset(self::$_singleton) || $force) {
      $provider = array();

      $providerID = CRM_Utils_Array::value('provider_id', $providerParams);
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }

      self::$_singleton = new uk_co_compucorp_amazonsns($provider);
    }

    return self::$_singleton;
  }

  public function send($recipients, $header, $message, $jobID = NULL, $userID = NULL) {

    $messageParams = array();

    if (!empty($this->senderID)) {
      $messageParams['SenderID'] = $this->senderID;
    }

    $messageParams['SMSType'] = 'Transactional';
    $messageParams['Message'] = $message;
    $messageParams['PhoneNumber'] = $recipients;

    try {
      $result = $this->snsClient->publish($messageParams);
      $messageID = $result->get('MessageId');
      $this->createActivity($messageID, $message, $header, $jobID, $userID);
    } catch(Aws\Sns\Exception\SnsException $e) {
      return PEAR::raiseError(
        'Error Sending SMS through Amazon SNS [' . $e->getAwsErrorCode() . ']' . ':' . ' - ' . $e->getAwsErrorMessage(),
        $e->getAwsErrorCode(),
        PEAR_ERROR_RETURN
      );
    }

    return $messageID;
  }
}