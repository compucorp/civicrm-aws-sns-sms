Amazon Simple Notification Service SMS Provider Extension
======
This extension enables users of CiviCRM to:

- Send SMS text messages from CiviCRM via the Amazon SNS service.
- Validate Phone numbers against E.164 format, so that sending not attempted if it does not match.
- Categorize SMS type (‘promotional’ or ‘transactional’) with a field only visible for AWS SMS processor type.

Installation
------ 
In order to send SMS messages using Amazon SNS, the AWS-SDK for PHP needs to be installed. This dependency is handled with composer, 
which needs to be run in order to install all required libraries.
- Download / clone repository files and unpack them into CiviCRM's extension directory.
- Run ´composer install´ from the extension's directory, to install all required libraries.
- Go to Administer -> System Settings -> Extensions
- Install Amazon SNS SMS Provider (uk.co.compucorp.amazonsns) extension, that should appear on the extension list, by clicking on the 'Install' link.

Configuration
------
In order to be able to use this extension, an account in AWS needs to be set up (https://aws.amazon.com/). Once you have an account, use 
the AWS Console (https://console.aws.amazon.com) to create a user on the IAM component with sufficient permissions to send SMS messages (ie. Publish 
permission for Amazon SNS). When you create the user, you will need to take note of the user's credentials, composed by two keys: 
'Access key ID', which is public, and 'Secret Access Key', which is private.

After installing, the new 'Amazon SNS' provider type is available to configure SMS providers. To configure a new provider, follow this 
procedure:
- Go to Administer -> System Settings -> SMS Providers
- Click on Add SMS Provider
- Choose 'Amazon SNS' Name Field
- Add a Title
- For Username, use the 'Access key ID' provided by Amazon for the IAM user you creaated.
- For password, use the 'Secret Access Key' provided by amazon for your IAM user.
- Select API Type as http
- For API URL, leave the one preloaded when yo selected 'Amazon SNS' on the Name field. This value is moot, as the AWS SDK determines the URL on-the-fly.
- For API Parameters field, there are three parameters you must provide. Be sure to put each parameter on a
 different line, with the format 'parameter_name=parameter_value':
  - region: The AWS region from where the SMS message should be sent.
  - version: AWS SDK version to use. Use 'latest' for this value.
  - SenderID: An alphanumeric string with at least one letter which identifies the sender. Bear in mind that not all countries support showing SenderID.
```
region=eu-west-1
version=latest
SenderID=TestSender
```
- Click Save!

Usage
------
Now that the extension is installed and you've configured a new SMS Provider, you can start sending SMS messages with it. Choosing a 
provider for Amason SNS will require the input for an additional field, SMS Type, which can be 'Promotional' or 'Transactional'. 
Basically, Amazon SNS will treat the SMS message differently, adding some additional security and verification of delivery for messages 
marked as 'Transactionsl'.

Other than that, phone numbers are validated to meet the E.164 standard, but enforcing the use of a '+' sign at the start of the number (in E.164, the + 
sign is optional). E.164 phone numbers can have between 2 and 15 digits. Basically, phone numbers will have to follow this format:

```
+<country_code><phonenumber>
```

So these are VALID phone numbers:

```
+10
+571234567
+18002527799
```

And these are NOT VALID phone numbers:

```
+1 // Less than two digits
+57 123 4567      // Can't use spaces
+1-800-252-7799   // Can't use characters other than the '+'
+(72) 456 789     // Can't use characters other than the '+'
+1234567890123456 // More than 15 digits
18002527799       // Doesn't have a '+' sign at the beginning
```
