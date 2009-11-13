## Introduction ##

The simple SMS Plugin allows sending of SMS's via the [http://www.clickatell.com](http://www.clickatell.com) SMS gateway.

## Requirements ##

You'll need to have an account with clickatell to be able to use this plugin.  You can either sign up for one on their site - or drop me a mail and I'll send you back an account with some test credits on.

Clickatell also support 2-way messaging, flash messages, sending ringtones, images, etc.


A clickatell account has 3 primary attributes - username, password and the appID. 

This plugin requires the [sfWebBrowser](http://www.symfony-project.org/plugins/sfWebBrowserPlugin) plugin to operate.
Requests from your application to clickatell.com are performed via http - so you'll need to ensure your firewall rules are set accordingly.


## API ##

The clickatell API also allows server side callbacks to a url of your chosing for status & delivery information.


It has a very simple but flexible api:

    // Construct the message object
    $msg = new dhSimpleSMS('appID', 'username','password');

    // Query the account balance
    $balance = $msg->accountBalance();

    // Simply send a message
    $messageID = $msg->sendMessage("+CCNNUUMMBBEERR", "message");

    // Send a message with extended options
    $messageID = $msg->sendMessage(
                "+CCNNUUMMBBEERR", 
                "message",
                array(
                    'callback' =>3,
                    'deliv_ack' =>1,
                    'from' => '+CCNNUUMMBBEERR'
                    ));
    // Retrieve a status update using a messageID
    $status = $msg->queryMessage($messageID);

