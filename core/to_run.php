<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 06.11.2017
 * Time: 23:19
 */
namespace core\main;
require      'generate_mock.php';                use core\main\generate_mock as GM;
require_once '../supplementary/array_print.php'; use supplementary\array_print\array_print as SUP;
require_once 'mock_generator_constants.php'    ; use core\main\mock_generator_constants as CONSTANTS;
require      'mock_description.php';             use core\main\mock_description as DESCRIPTION;
require_once 'generate_sum_table.php';           use core\main\generate_sum_table as SUM_TABLE;

$request_data = json_decode('{
"redirect_url":"http:\/\/localhost\/rdp\/service\/test-suite\/T_redirection_hosted_single\/redirect_url",
"notify_url":"http:\/\/localhost\/rdp\/notif_server\/payment_api\/notif-url.php",
"back_url":"http:\/\/localhost\/rdp\/service\/test-suite\/T_redirection_hosted_single\/back_url",
"mid":"1002089860",
"order_id":"TST0005",
"amount":"0.01",
"ccy":"SGD",
"api_mode":"redirection_sop",
"payment_type":"S",
"payer_email":"test@gmail.com",
"signature": "cefba02b32488219b88a644dda783bbed66af7b20e1c58cd3c17685ae3aa11e530f1efb6410f44125f82805ddf6ccc5a926ba425a231ab5831c3e868d0aee694"}', true);

$codes = [
    '0' => 'OLOLO1',
    '1' => 'OLOLO2',
];

$data =  json_decode('{
    "ololo":{
    "ololo1":"1",
    "ololo2":{
    "ololo3":"1"
    }
    },
    
    "created_timestamp": 1509434175,
    "expired_timestamp": 1509520575,
    "mid": "1002089860",
    "order_id": "TST0005",
    "transaction_id": "TST0005_1693812544657086860",
    "payment_url": "https://secure-dev.reddotpayment.com/service/payment/--SERVER--/efb2b36478c2d2a4ac73c355b8ad1a2a33866071e644e50fd272c56dc7237d196d64dc66e9be466637bf1574f04fe692993a9a02462e8e5c9f2edcc4540de84b",
    "response_code": 0,
    "response_msg": "successful",
    "signature": "a55bb0dae264453221948b76b673494b5ed9b4b881d3062eb2dc32bf421d55bc6ece3263da374e920a47d9412ad154913cb836c35dd4e6f95ed548c89347d774"
}', true);
/*
function ololo ($amount = '100', $data = [], $status_sum_array = ['123->345'=>'100']){
        $nestingParametersString = array_search($amount,$status_sum_array);
        if ($nestingParametersString) {
            $nestingParameters = explode('->', $nestingParametersString);
            switch (count($nestingParameters)){
                case 1:
                    $data[$nestingParameters[0]] = 'ILoveYou';//self::$invalid_string; // случайный текст;
                    break;
                case 2:
                    $data[$nestingParameters[0]][$nestingParameters[1]] =  'ILoveYou';//self::$invalid_string; // случайный текст;
                    break;
                case 3:
                    $data[$nestingParameters[0]][$nestingParameters[1]][$nestingParameters[2]] =  'ILoveYou';//self::$invalid_string; // случайный текст;
                    break;
                default:
                    dye;
                    break;
            }
        }
        return $data;
}

print_r(ololo());
*/
$name = 'status';
$file = __DIR__.'/result.php';

//file_put_contents($file,  GM::generate_invalid_value_nesting('self::$'.$name.'_sum_array'));


$instanceOfSumTable = new SUM_TABLE();
$instanceOfGenerateMock = new GM ($data, $name);
$sum_table = $instanceOfSumTable->create_sum_table($data, CONSTANTS::TYPE['STATUS'], $codes);
$description = (new DESCRIPTION($sum_table))->get_mock_description();
file_put_contents($file, $description);
file_put_contents($file, "\n\n", FILE_APPEND);
file_put_contents($file,  new SUP($sum_table, "static private ".$name.'_sum_array'), FILE_APPEND);
file_put_contents($file,  new SUP($codes, "static private ".$name.'_code_array'), FILE_APPEND);
file_put_contents($file, $instanceOfGenerateMock->create_status_code(), FILE_APPEND);

