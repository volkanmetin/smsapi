<?php

/**
 * Laravel 5 SMS Api
 * @license MIT License
 * @author Volkan Metin <ben@volkanmetin.com>
 * @link http://www.volkanmetin.com
 *
*/

namespace Volkanmetin\Smsapi;
use Queue;

class Smsapi
{
    protected $app;

    protected $config;
    protected $lang;

    protected $api_url;
    public $last_message;
    protected $last_err_code = 900;
    public $data;

    protected $allowed_providers = array('turacell');

    public function __construct($app)
    {
        $this->app      = $app;
        $locale         = $app['config']['app.locale'];
        $this->lang     = $app['translator']->get("smsapi::{$locale}");
        $this->config   = $app['config']['smsapi'];

        if(!in_array($this->config['sms_provider'], $this->allowed_providers)) {
            $this->last_message = $this->lang['api']['20'];
            return false;
        }
     
        if(!$this->setApiUrl()) {
            $this->last_message = $this->lang['api']['21'];
            return false;
        }
    }

    /**
     * Get Credit
     * Shows how much SMS you have left
     * @return integer number of SMSes left for the account
     */
    public function getCredit()
    {
        $credit = 0;

        if($this->config['sms_provider'] == "turacell") {
            $data = '<MainReportRoot>'.$this->data_header(6).'</MainReportRoot>';

            if($result = $this->postXML($data) === false)
            {
                if(!empty($this->last_err_code)) {
                    if(!empty($this->lang[$provider]['default'][$this->last_err_code]))
                        $this->last_message = $this->lang[$provider]['default'][$this->last_err_code];
                    else
                        $this->last_message = $this->lang[$provider]['services'][$this->last_err_code];
                }
                else
                    $this->last_message = $this->lang['api']['901'];

                return false;
            }
            
            $lines = explode("\n", $result);
            $credit = intval($lines[0]);

        } else {
            return false;
        }

        return $credit;
    }

    /**
     * Get originators list
     * Shows senderID list you have
     * @return array list of originators
     */
    public function getOriginators()
    {
        $originators = array();

        if($this->config['sms_provider'] == "turacell") {
            $data = '<MainReportRoot>'.$this->data_header(6).'</MainReportRoot>';

            if($result = $this->postXML($data) === false)
            {
                if(!empty($this->last_err_code)) {
                    if(!empty($this->lang[$provider]['default'][$this->last_err_code]))
                        $this->last_message = $this->lang[$provider]['default'][$this->last_err_code];
                    else
                        $this->last_message = $this->lang[$provider]['services'][$this->last_err_code];
                }
                else
                    $this->last_message = $this->lang['api']['901'];

                return false;
            }
            
            $lines = explode("\n",$result);
            unset($lines[0]);
            if(count($lines) > 0) {
                foreach ($lines as $item) {
                    if(trim($item) != "")
                        $originators[md5(trim($item))] = trim($item);
                }

                $originators = array_values($originators);
            }
        } else {
            return false;
        }

        return $originators;
    }

    /**
     * Sending SMS method
     * @param mix $number
     * @param mix $message
     * @return array list of originators
     */
    public function send($number, $message, $date = null, $originator = null)
    {
        $provider = $this->config['sms_provider'];

        if(!$this->checkParams($number, $message, $date))
        {
            if(empty($this->last_message))
                $this->last_message = $this->lang['api']['900'];

            return false;
        }

        if($originator == null)
        {
            $originator = $this->config[$provider]['originator'];

            if(empty($originator)) {
                $this->last_message = $this->lang['api']['4'];
                return false;
            }
        }

        $data = $this->prepare_data($number, $message, $originator, $date);

        $result = $this->postXML($data);

        if($result === false)
        {
            if(!empty($this->last_err_code)) {
                if(!empty($this->lang[$provider]['default'][$this->last_err_code]))
                    $this->last_message = $this->lang[$provider]['default'][$this->last_err_code];
                else
                    $this->last_message = $this->lang[$provider]['services'][$this->last_err_code];
            }
            else
                $this->last_message = $this->lang['api']['901'];

            return false;
        }
        else {
            
            if($this->config['sms_provider'] == "turacell") {
                $p = explode(':', $result);
                return trim($p[1]);
            }

        }
    }

    /**
     * Get last message from API
     * @return string last message
     *
     */
    public function lastMessage()
    {
        return $this->last_message;
    }


    /**
     * setting API endpoint url method
     * @return boolen
     *
     */
    private function setApiUrl()
    {
        switch ($this->config['sms_provider']) {
            case 'turacell':
                $this->api_url = 'https://processor.smsorigin.com/xml/process.aspx';
                break;

            default:
                return false;
                break;
        }
        return true;
    }

    /**
     * Parameters check method
     * @param string $number number(s) to recieve
     * @param string $message message(s) to be sent
     * @param string $date message send time whenever you want (format: Y-m-d H:i)
     * @return boolen
     */
    protected function checkParams($number, $message, $date)
    {
        // number check rules
        if(is_array($number)) {

            foreach ($number as $key => $value) {
                if(empty($value)) {
                    $this->last_message = $this->lang['api']['3'];
                    return false;
                }
            }
            return true;

        } else {
            if(empty($number)) {
                $this->last_message = $this->lang['api']['2'];
                return false;
            }
        }

        // message check rules
        if(is_array($message)) {

            foreach ($message as $key => $value) {
                if(empty($value)) {
                    $this->last_message = $this->lang['api']['3'];
                    return false;
                }
            }
            return true;

        } else {
            if(empty($message)) {
                $this->last_message = $this->lang['api']['4'];
                return false;
            }
        }


        // number & message count must be equal rule
        if(is_array($number) && is_array($message)) {
            if(count($number) != count($message)) {
                $this->last_message = $this->lang['api']['7'];
                return false;
            }
        }


        // send date check rules
        if(!empty($date)) {
            if($date != date('Y-m-d H:i', strtotime($date))) {
                $this->last_message = $this->lang['api']['5'];
                return false;
            }
        }

        return true;
    }

    /**
     * Preparing xml data
     * @param mix $message 
     * @param mix $number 
     * @return string XML data
     *
     */
    private function prepare_data($number, $message, $originator, $date = null)
    {
        $data = false;

        if($this->config['sms_provider'] == "turacell") {

            if(is_array($number) && is_array($message)) {
                $command = '1';
            } else {
                if(is_array($number)) {
                    $command = '0';
                } elseif (is_array($message)) {
                    $command = '0';
                } else {
                    $command = '0';
                }
            }

            $data = '<MainmsgBody>'.$this->data_header($command);

            if($command == '1') {

                $data .= '<Messages>';
                for($i=0; $i < count($number); $i++) {
                    $data .=
                    '<Message>
                        <Mesgbody>'.$message[$i].'</Mesgbody>
                        <Number>'.$number[$i].'</Number>
                    </Message>'; 
                }
                $data .= '</Messages>';

            } else {

                if(is_array($number)) {
                    $data .= '<Mesgbody>'.$message.'</Mesgbody><Numbers>'.implode(',', $number).'</Numbers>';
                } elseif(is_array($message)) {

                    $data .= '<Messages>';
                    foreach ($message as $item) {
                        $data .=
                            '<Message>
                                <Mesgbody>'.$item.'</Mesgbody>
                                <Number>'.$number.'</Number>
                            </Message>'; 
                    }
                    $data .= '</Messages>';
                } else {
                    $data .= '<Mesgbody>'.$message.'</Mesgbody><Numbers>'.$number.'</Numbers>';
                }
            }

            $data .= 
                '<Type>1</Type>
                <Originator>'.$originator.'</Originator>
                <Concat>'.($this->config['concat'] == true ? '1' : '0').'</Concat>';

            $data .= '<SDate>'.($date != null ? date('dmYHi', strtotime($date)) : '').'</SDate>';
            $data .= '<EDate></EDate></MainmsgBody>';
        }

        return $data;
    }

    /**
     * CURL XML post sending method
     * @param string $data formatted string
     * @return string API Status
     *
     */
    private function postXML($data)
    {
        $this->last_err_code = 900;
        $this->data = $data;

        if ($this->config['queue']) {
            
            Queue::push(function() use ($data) {
                $headers = array(
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "SOAPAction: " . $this->api_url, 
                    "Content-length: ".strlen($data),
                );

                // PHP cURL  for https connection with auth
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_URL, $this->api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                // converting
                $response = trim(curl_exec($ch)); 
                curl_close($ch);


                // get error code if exits
                if($this->config['sms_provider'] == "turacell") {
                    if(strlen($response) == 2 && $response != 'OK') {
                        $this->last_err_code = $response;
                        return false;
                    }
                }

                return $response;
            });

        } else {

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: " . $this->api_url, 
                "Content-length: ".strlen($data),
            );

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $this->api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = trim(curl_exec($ch)); 
            curl_close($ch);

            // get error code if exits
            if($this->config['sms_provider'] == "turacell") {
                if(strlen($response) == 2 && $response != 'OK') {
                    $this->last_err_code = $response;
                    return false;
                }
            }

            return $response;
        
        }
        
    }


    /**
     * Prepare the xml data header for Turacell
     * @param string $commant sms action type
     * @return string xml header
     */
    private function data_header($command)
    {
        return 
            '<Command>'.$command.'</Command>
            <PlatformID>'.$this->config['turacell']['platform_id'].'</PlatformID>
            <UserName>'.$this->config['turacell']['username'].'</UserName>
            <PassWord>'.$this->config['turacell']['password'].'</PassWord>
            <ChannelCode>'.$this->config['turacell']['channel_code'].'</ChannelCode>';
    }

    /**
     * Checks whether the number is an integer or not with Regex
     * !I'm not using is_int() because people may add numbers in quotes!
     * Taken from PHP-Fusion <http://php-fusion.co.uk>
     * @param string $value string to be checked
     * @return boolean
     */
    private function isnum($value)
    {
        if (!is_array($value)) {
            return preg_match("/^[0-9]+$/", $value);
        } else {
            return false;
        }
    }
}