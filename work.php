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
                    $result .= implode('; ',$terms['data']);
                }
                else {
                    $result .= 'Internal error =( <br/>';
                    $result .= 'Error message: '.$terms['error_message'];
                }
                break;

            case 'list':
                $list = $_POST['list'];
                $list = explode("\n", $list);

                $list_words = array();

                foreach($list as $word){
                    if (!$word = trim($word)) {
                        continue;
                    }
                    $api_result = self::getAPIResult($word);
                    $api_result['word'] = $word;
                    $list_words[] = $api_result;
                }

                $result .= '<table class="table table-striped">
              <thead>
                <tr>
                  <th>Input</th>
                  <th>Result</th>
                  <th>Terms</th>
                </tr>
              </thead>
              <tbody>';

                foreach($list_words as $word){
                    $result .= '
                <tr>
                  <td>'.$word['word'].'</td>';

                    if ($word['success']) {
                        $result .= '<td>success</td>';
                        $result .= '<td>'.implode('; ',$word['data']).'</td>';
                    }
                    else {
                        $result .= '<td>Error.</br> Message:'.$word['error_message'].'</td>';
                        $result .= '<td> - </td>';
                    }

                }


                $result .= '
              </tbody>
            </table>';

                break;

            default:
                $result .= 'Not supported type';
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