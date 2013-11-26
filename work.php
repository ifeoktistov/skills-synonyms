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
                $list = explode("\n", $_POST['list']);
                $list_words = self::getListResult($list);

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


            case 'csv-file':
                if (isset($_FILES['file']) and $_FILES['file']['error'] == 0) {

                    $file_csv = $_FILES['file']['tmp_name'];
                    $list = array();

                    $handle = fopen($file_csv, "r");
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (isset($data[0])) {
                            $list[] = $data[0];
                        }
                    }
                    $list_words = self::getListResult($list);

                    $csv_array = array();
                    foreach($list_words as $word){
                        if ($word['success']) {
                            $csv_array[] = array($word['word'], implode(', ',$word['data']));
                        }
                    }

                    self::download_send_headers("data_export_" . date("Y-m-d") . ".csv");
                    echo self::array2csv($csv_array);
                    die;
                }
                else {

                    $result .= 'File upload error =(';
                }
                break;

            default:
                $result .= 'Not supported type';
                break;
        }

        return $result;
    }

    static function array2csv(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        foreach ($array as $row) {
            fputcsv($df, $row, ';');
        }
        fclose($df);
        return ob_get_clean();
    }

    static function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    static function getListResult($list_words){
        $result = array();

        foreach($list_words as $word){
            if (!$word = trim($word)) {
                continue;
            }
            $api_result = self::getAPIResult($word);
            $api_result['word'] = $word;
            $result[] = $api_result;
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