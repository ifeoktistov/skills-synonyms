<?php
class Work {
    static $_API_URL = 'http://wikisynonyms.ipeirotis.com/api/';

    static function processPOST(){
        $type = $_POST['type'];
        $result = '';

        switch($type) {
            case 'api-test':
                $word = $_POST['word'];
                $terms = self::getAPIResult($word);

                if ($terms['success']) {
                    $result .= 'Terms: ';
                    $result .= implode(', ',$terms['data']);
                }
                else {
                    $result .= 'Internal error =( <br/>';
                    $result .= 'Error message: '.$terms['error_message'];
                }
                break;

            default:
                break;
        }

        return $result;
    }

    static function getAPIResult($world){
        $result = array(
            'success' => false,
            'error_message' => '',
            'data' => array(),
        );
        $api_result = @json_decode(self::curlUsingGet(self::$_API_URL.urlencode($world)));



        if ($api_result and property_exists($api_result,'message') and $api_result->message=='success') {
            $result['success'] = true;
            foreach($api_result->terms as $term_data){
                $result['data'][] = $term_data->term;
            }
        }
        else {
            $result['error_message'] = 'api request is not success';
            if ($api_result and property_exists($api_result,'message')) {
                $result['error_message'] .= ' ('.$api_result->message.')';
            }
        }

        return $result;
    }


    static function curlUsingGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10); # timeout after 10 seconds, you can increase it
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($ch, CURLOPT_URL, $url); #set the url and get string together

        $return = curl_exec($ch);
        curl_close($ch);

        return $return;
    }
}