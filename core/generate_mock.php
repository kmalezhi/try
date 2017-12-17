<?php

namespace core\main;

require_once 'mock_generator_constants.php'; use core\main\mock_generator_constants as CONSTANTS;
require_once '../supplementary/array_print.php'; use supplementary\array_print\array_print as ARRAY_PRINT;

class generate_mock
{
    private $request_data =[];
    private $name = 'some_name';
    public function __construct(array $request_data, string $name) {
        $this->request_data = $request_data;
        $this->name = $name;
    }
    public function create_status_code () {
        $sumArrayName = 'self::$'.$this->name.'_sum_array';
        $codeArrayName = 'self::$'.$this->name.'_code_array';
        $code  = '';
        $code .= $this->generate_signature_validation_function_code($this->name);
        $code .= $this->generate_signature_creation_function_code($this->name);
        $code .= $this->generate_method_start_code($this->name);
        $code .= $this->generate_validation_code($this->request_data);
        $code .= $this->generate_signature_validation_code($this->name);
        $code .= $this->generate_get_request_data();
        $code .= $this->generate_set_amount();
        $code .= $this->generate_http_response_code($sumArrayName);
        $code .= $this->generate_invalid_sum($sumArrayName);
        $code .= $this->generate_invalid_currency($sumArrayName);
        $code .= $this->generate_data($this->request_data);
        $code .= $this->generate_invalid_value_heading($sumArrayName);
        $code .= $this->generate_invalid_value_nesting($sumArrayName);
        $code .= $this->generate_empty_value_heading();
        $code .= $this->generate_empty_value($sumArrayName);
        $code .= $this->generate_parameter_remove($sumArrayName);
        $code .= $this->generate_redundant_values($sumArrayName);
        $code .= $this->generate_business_codes($sumArrayName, $codeArrayName);
        $code .= $this->generate_signature_creation($this->name);
        $code .= $this->generate_delay($sumArrayName);
        $code .= $this->generate_response();
        $code .= $this->generate_method_end_code();
        return $code;
    }

    private function generate_method_start_code                  (string $name) {
        return "\n    static function $name (MockHelper \$helper) {
        /* FIXME Эту секцию необходимо дописать вручную! */
        \$content  = json_decode(\$helper->getContent(),1); // Этот код только для json запросов!
        ";
    }
    private function generate_id_and_dataSave_generation_code    (){
        return "
        \$id       = time().microtime();
        \$helper->setRedisEx(\$id,\$content); // Сохраняем пришедший запрос для других операций";
    }
    private function generate_get_request_data (){
        return "\n
<<<<<<< HEAD
       /****************************************************
        * Достаем пришедший первоначальный запрос операции *
        ****************************************************/
=======
        /****************************************************
         * Достаем пришедший первоначальный запрос операции *
         ****************************************************/
>>>>>>> 2ebac5b68794dd5b120f9e06c820b87897ff55a3
        \$id = \$content['ID'];   // FIXME Указать поле, содержащее id!
        \$requestData = \$helper->getRedisEx(\$id);";
    }
    private function generate_set_amount (){
        return "\n
<<<<<<< HEAD
       /**************************************
        * Сумма (для управления логикой мока *
        **************************************/
=======
        /**************************************
         * Сумма (для управления логикой мока *
         **************************************/
>>>>>>> 2ebac5b68794dd5b120f9e06c820b87897ff55a3
        \$amount   = \$content['Amount'];   // FIXME Указать поле, содержащее сумму!";
    }
    private function generate_validation_code                    (array $request_data) {
        $some_str ="
        /********************************
         * Валидация пришедшего запроса *
         ********************************/
         
        /* FIXME Уточнить параметры валидации! */
         
         \$is_valid = \\GUMP::is_valid(\$content,
                    [";
        foreach ($request_data as $key => $value) {
            $some_str .= "\n                        '" . $key . "' => 'required',";
        }
        $some_str .= "\n                    ]);";

        $some_str .= "\n        if (\$is_valid !== true) {
            \$helper->makeResponse('Validation failed! Details: '.\$is_valid,400);
            return;
        }";
        return $some_str;
    }
    private function generate_signature_validation_function_code (string $name){
        $some_string = "
    /* FIXME! Эту функцию нужно переписать вручную */
    static private function ".$name."_signature_validation (array \$content){
            \$sign_str= \$content['VALUE1'].
                \$content['VALUE2'].
                self::SECRET_KEY;
            return (hash ('sha256', \$sign_str) == \$content['signature_field_name']);
        }";
        return $some_string;
    }
    private function generate_signature_validation_code          (string $name){
        $some_string = "
       /*********************
        * Валидация подписи *
        *********************/
        if (! self::".$name."_signature_validation(\$content)) {
            \$helper->makeResponse('Invalid signature!',400);
            return;
        }";
        return $some_string;
    }
    private function generate_http_response_code                 (string $sum_array){
        $string ="       
       /************************************************
        * Ответ с http кодом ошибки (на основе Суммы) *
        ************************************************/
        if (in_array(\$amount,$sum_array)) {
            \$http_code = (array_flip($sum_array))[\$amount];
            \$helper->makeResponse('Mock generated http error', \$http_code);
        };";
        return $string;
    }
    private function generate_invalid_sum                        (string $sum_array){
    $some_str = "
       /**************************************
        * Неверная суммма (на основе Amount) *
        **************************************/
        if (\$amount == ".$sum_array."['invalid_sum']) {
            \$amount = (string)((float)\$amount + 1.22);
        }";
    return $some_str;
}
    private function generate_invalid_currency                   (string $sum_array){
        $some_str = "
       /**************************************
        * Неверная суммма (на основе Amount) *
        **************************************/
        if (\$amount == ".$sum_array."['invalid_currency']) {
            \$currency = 'XAU';
        }";
        return $some_str;
    }
    private function generate_data                               (array $request_data) {
        $some_str = "
       /*******************
        * Формируем ответ *
        * *****************/
         
       /* FIXME Уточнить данные! 
        * FIXME Не забываем про сумму, валюту и id!
        */";
        $some_str .= new ARRAY_PRINT($request_data,"data", '        ');
        return $some_str;
    }
    private function generate_invalid_value_heading              (){
        return "
        /****************************************
         * Неверные значения (на основе Amount) *
         * **************************************/";    
    }
    private function generate_invalid_value_1_level              (string $sumArrayName){
        /* Старая реализация
        $someStr .= "
        if (in_array(\$amount,".$sumArrayName."['invalid_parameter'])) {
            \$parameter = array_flip(".$sumArrayName."['invalid_parameter']);
            \$data [\$parameter[\$amount]] = self::\$invalid_string; // случайный текст
        }";
        */
        $someStr .= "
        if (\$parameterName = array_search(\$amount,".$sumArrayName."['invalid_parameter'])) {
            \$data [\$parameterName] = self::\$invalid_string; // случайный текст
        }";
        return $someStr;
    }
    private function generate_invalid_value_nesting              (string $sumArrayName){
        $someStr = "
        if (\$nestingParametersString = array_search(\$amount,".$sumArrayName."['invalid_parameter'])) {
            \$nestingParameters = explode('".CONSTANTS::SUM_TABLE_DELIMITER."', \$nestingParametersString);
            switch (count(\$nestingParameters)){
                case 1:
                    \$data[\$nestingParameters[0]] = self::\$invalid_string; // случайный текст;
                    break;
                case 2:
                    \$data[\$nestingParameters[0]][\$nestingParameters[1]] = self::\$invalid_string; // случайный текст;
                    break;
                case 3:
                    \$data[\$nestingParameters[0]][\$nestingParameters[1]][\$nestingParameters[2]] = self::\$invalid_string; // случайный текст;
                    break;
                default:
                    dye;
                    break;
            }
        }";
        return $someStr;
    }
    private function generate_empty_value_heading                (){
       return "
       /**********************************
        * Пустое поле (на основе Amount) *
        **********************************/";
    }
    private function generate_empty_value                        (string $sum_array){
        $some_str = "
        if (in_array(\$amount,".$sum_array."['empty_parameter'])) {
            \$parameter = array_flip(".$sum_array."['empty_parameter']);
            \$data [\$parameter[\$amount]] = ''; // пустое поле
        }";
        return $some_str;
    }
    private function generate_parameter_remove                   (string $sum_array){
        $some_str = "
       /***********************************
        * Удаляем поле (на основе Amount) *
        ***********************************/
        if (in_array(\$amount,".$sum_array."['missing_parameter'])) {
            \$parameter = array_flip(".$sum_array."['missing_parameter']);
            unset(\$data [\$parameter[\$amount]]); // удаляем параметр
        }";
        return $some_str;
    }
    private function generate_redundant_values                   (string $sum_array){
    $some_str = "
        /***************************************
        * Лишние параметры (на основе Amount) *
        ***************************************/
        if (\$amount == ".$sum_array."['redundant_parameters']) {
            \$data['".CONSTANTS::REDUNDANT_PARAMETER_1."'] = '1';
            \$data['".CONSTANTS::REDUNDANT_PARAMETER_2."'] = '2';
        }";
    return $some_str;
}
    private function generate_business_codes                     (string $sum_array, string $code_array = ''){
        $some_str = "
        /**********************************
         * Бизнес коды (на основе Amount) *
         **********************************/
        // FIXME Не забудте указать верные имена полей или удалить раздел, если в нем нет необходимости!
        if (in_array(\$amount,".$sum_array."['business_code'])) {
            \$business_codes = array_flip(".$sum_array."['business_code']);
            \$data ['УКАЖИТЕ ИМЯ ПАРАМЕТРА С БИЗНЕС КОДОМ'] = \$business_codes[\$amount]; //бизнес код";
        if ($code_array != '') {
            $some_str .= "
            \$data ['УКАЖИТЕ ИМЯ ПАРАМЕТРА С БИЗНЕС СООБЩЕНИЕМ'] = ". $code_array ."[\$business_codes[\$amount]]; //бизнес сообщение";
        }

        $some_str .= "
        }";
        return $some_str;
    }
    private function generate_signature_creation_function_code   (string $name){
        $some_string = "
    /* FIXME! Эту функцию нужно переписать вручную */
    static private function ".$name."_signature_generation (array \$content){
            \$sign_str= \$content['VALUE1'].
                \$content['VALUE2'].
                self::SECRET_KEY;
            return (hash ('sha256', \$sign_str) == \$content['signature_field_name']);
        }";
        return $some_string;
    }
    private function generate_signature_creation                 (string $name){
        $some_str = "
        /**********************
         * Генерируем подпись *
         **********************/
         // FIXME Не забудте указать имя параметра!
        \$data['УКАЖИТЕ ИМЯ ПАРАМЕТРА С ПОДПИСЬЮ']= self::".$name."_signature_generation(\$data);";
        return $some_str;
    }
    private function generate_delay                              (string $sum_array){
        $some_str = "
       /**************************************
        * Задержка ответа (на основе Amount) *
        **************************************/
        if (in_array(\$amount,".$sum_array."['timeout'])) {
            \$time_to_sleep = array_flip(".$sum_array."['timeout']);
            sleep(\$time_to_sleep[\$amount]); // задержка
        }";
        return $some_str;
    }
    private function generate_response                           (){
        $some_str = "
        /********************
         * Синхронный ответ *
         ********************/
        \$helper->makeResponse(json_encode(\$data));";
        return $some_str;
    }
    private function generate_method_end_code                    (){
        return "\n    }";
    }

}
