<?php

/**
 * Description of dhSmsSendclass
 *
 * @author dasher
 */
class dhSimpleSms {

    protected $options =
        array(
            'api_id'    => null,
            'user'      => null,
            'password'  => null
            );

    protected $session =
        array(
            'session_id'    => null,
            'last_accessed' => null,
            'use_sessions'  => true,
        );

    protected $allowedOptions = array('from', 'callback', 'deliv_ack', 'msg_type');

    /**
     *
     * @var sfContext
     */
    protected $context = null;

    public function  __construct($apiID = null, $username=null, $password=null) {
        $this->options['user'] = (!null===$username?$username:sfConfig::get('app_simplesms_config_username', null));
        $this->options['password'] = (!null===$password?$password:sfConfig::get('app_simplesms_config_password', null));
        $this->options['api_id'] = (!null===$apiID?$password:sfConfig::get('app_simplesms_config_apiId', null));

        if (null === $this->options['user'] || null === $this->options['password'] || null=== $this->options['api_id']) {
            throw new sfException('Required Options not set.  User, Password and api_id must all be defined');
        }

        $this->context = sfContext::getInstance();
    }


    private function updateLastRequest() {
        $this->session['last_accessed'] = time();
    }

    private function parseResponse($response) {
        $result =  explode(":", $response);
        return $result[1];
    }

    private function validResponse($response) {
        $result =  explode(":", $response);
        return strtolower($result[0]) == "ok";
    }

    private function setupSession() {
        $this->log(sprintf("setupSession"));
        $result = $this->doCall("http://api.clickatell.com/http/auth", $this->options);
        $this->session['session_id'] = $result;
        return true;
    }

    private function hasSession() {
        if ($this->session['use_sessions']) {
            $this->log("1");
            // so we should be using sessions
            if ($this->session['session_id']) {
                $this->log("2");
                // We have an existing session_id
                if ((time() - $this->session['last_accessed']) > (15 * 60) ) {
                    $this->log("3");
                    // Session expired
                    $this->session['use_sessions'] = false;
                    $this->setupSession();
                    $this->session['use_sessions'] = true;
                }
            } else {
                $this->log("4");
                $this->session['use_sessions'] = false;
                $this->setupSession();
                $this->session['use_sessions'] = true;
            }
            return null !== $this->session['session_id'];
        }
        $this->log("5");
        return false;
    }


    private function buildRequest($params = array()) {

        $result = "?";
        foreach($params as $key => $value) {
            $result .= sprintf("%s=%s&",$key,$value);
        }
        return $result;
    }

    public function doCall($url, $params = array()) {

        $browser = new sfWebBrowser();

        if ($this->hasSession()) {
            $params['session_id'] = $this->session['session_id'];
        }

        $request = $url . $this->buildRequest($params);

        $this->log(sprintf("Executing Request: %s",$request));

        $browser->get($request);
        $rawResponse = $browser->getResponseText();
        $this->updateLastRequest();

        $this->log(sprintf("Response: %s",$rawResponse));

        $response = $this->parseResponse($rawResponse);
        if ($this->validResponse($rawResponse)) {
            return trim($response);
        } else {
            throw new sfException($response);
        }

    }

    public function accountBalance() {
        return $this->doCall("http://api.clickatell.com/http/getbalance");
    }

    public function sendMessage($to, $content, $options = null) {

        $messageOptions = array('to' => $to, 'text' => urlencode($content));
        foreach($this->allowedOptions as $optionName) {
            if (isset ($options[$optionName])) {
                $messageOptions[$optionName] = $options[$optionName];
            }
        }

        return
            $this->doCall(
                "http://api.clickatell.com/http/sendmsg",
                $messageOptions
                );
    }

    public function queryMessage($messageID) {
        return $this->doCall("http://api.clickatell.com/http/querymsg", array('apimsgid' => $messageID));
    }

    private function log($message) {
        if (sfConfig::get('sf_debug', true)) {
            $this->context->getLogger()->log($message);
        }
    }

}
?>
