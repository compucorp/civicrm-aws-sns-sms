<?php

/**
 * Provides several methods to validate phone numbers meet the E.164 format,
 * which must be used to send SMS messages via Amazon SNS.
 */
class CRM_Amazonsns_SMS_PhoneValidator {

  /**
   * Regular expression to be used to validate E.164 format.
   *
   * @var string
   */
  private static $pattern = '^[+][1-9][0-9]{1,14}$';

  /**
   * Count the number of invalid phones associated to a mailing of SMS messages.
   *
   * @param int $mailingID
   *
   * @return int
   *   Number of invalid phone numbers found for the given mailing job.
   */
  public static function countMailingInvalidPhones($mailingID) {
    $query = "
      SELECT COUNT(civicrm_mailing_recipients.contact_id) AS total
      FROM civicrm_mailing_recipients, civicrm_phone, civicrm_contact
      WHERE civicrm_mailing_recipients.phone_id = civicrm_phone.id
      AND civicrm_mailing_recipients.contact_id = civicrm_contact.id
      AND civicrm_phone.phone REGEXP '" . self::$pattern . "' = 0
      AND civicrm_mailing_recipients.mailing_id = %1
    ";
    $dbResult = CRM_Core_DAO::executeQuery($query, array(
      1 => array($mailingID, 'Integer')
    ));
    $dbResult->fetch();

    return intval($dbResult->total);
  }

  /**
   * Fetches a list of some of the invalid phone numbers associated to an SMS
   * mailing.
   *
   * @param $mailingID
   *
   * @return array
   */
  public static function getMailingInvalidPhonesSample($mailingID) {
    $invalidRecipients = array();

    $query = "
      SELECT civicrm_mailing_recipients.contact_id, civicrm_contact.display_name, 
        civicrm_phone.phone
      FROM civicrm_mailing_recipients, civicrm_phone, civicrm_contact
      WHERE civicrm_mailing_recipients.phone_id = civicrm_phone.id
      AND civicrm_mailing_recipients.contact_id = civicrm_contact.id
      AND civicrm_phone.phone REGEXP '" . self::$pattern . "' = 0
      AND civicrm_mailing_recipients.mailing_id = %1
      LIMIT 0, 20
    ";
    $dbResult = CRM_Core_DAO::executeQuery($query, array(
      1 => array($mailingID, 'Integer')
    ));

    while ($dbResult->fetch()) {
      $invalidRecipients[] = array(
        'contact_id' => $dbResult->contact_id,
        'contact_name' => $dbResult->display_name,
        'phone' => $dbResult->phone
      );
    }

    return $invalidRecipients;
  }

  /**
   * Validates given phone number vs E.164 format
   *
   * @param $phone
   *
   * @return bool
   *   True if given phone number is compliant, false otherwise.
   */
  public static function validatePhoneNumber($phone) {
    if (preg_match("/" . self::$pattern . "/", $phone)) {
      return true;
    }

    return false;
  }
}
