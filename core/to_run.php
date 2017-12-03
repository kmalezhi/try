<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 06.11.2017
 * Time: 23:19
 */
namespace core\main;
require      __DIR__.'/generate_mock.php';       use core\main\generate_mock as GM;
require      '../supplementary/array_print.php'; use supplementary\array_print\array_print as SUP;
require_once 'mock_generator_constants.php'    ; use core\main\mock_generator_constants as CONSTANTS;
require      'mock_description.php';             use core\main\mock_description as DESCRIPTION;

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

$name = 'status';
$instance_of_generate = new generate_mock();
$sum_table = $instance_of_generate->create_sum_table($data, $codes, CONSTANTS::TYPE['STATUS']);
$description = (new DESCRIPTION($sum_table))->get_mock_description();
echo $description;
echo "\n\n";
echo new SUP($sum_table, true, $name.'_sum_array');
echo new SUP($codes, true, $name.'_code_array');
echo GM::generate_signature_validation_function_code($name);
echo GM::generate_signature_creation_function_code($name);
echo GM::generate_method_start_code($name);
echo GM::generate_validation_code($request_data);
echo GM::generate_signature_validation_code($name);
echo GM::generate_http_response_code('self::$'.$name.'_sum_array');
echo GM::generate_invalid_sum('self::$'.$name.'_sum_array');
echo GM::generate_invalid_currency('self::$'.$name.'_sum_array');
echo GM::generate_data($data);
echo GM::generate_invalid_value('self::$'.$name.'_sum_array');
echo GM::generate_empty_value('self::$'.$name.'_sum_array');
echo GM::generate_parameter_remove('self::$'.$name.'_sum_array');
echo GM::generate_redundant_values('self::$'.$name.'_sum_array');
echo GM::generate_business_codes('self::$'.$name.'_sum_array', 'self::$'.$name.'_code_array');
echo GM::generate_signature_creation($name);
echo GM::generate_delay('self::$'.$name.'_sum_array');
echo GM::generate_response();
echo GM::generate_method_end_code();



