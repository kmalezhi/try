<?php

namespace core\main;

require_once 'mock_generator_constants.php'; use core\main\mock_generator_constants as CONSTANTS;

class generate_mock
{
    private $sum = 1000;
    private $sum_table = [];
    public function __construct($sum = false)
    {
        if (($sum) AND is_integer($sum)) {
            $this->sum = (string)$sum;
        }
    }

    private function calculate_sum (){
        $this->sum++;
        return (((string)$this->sum).'.00');
    }
    private function fill_sum_table_rows(array $design,
                                         array $parameters_list,
                                         array $available_codes = []
    )
    {
        foreach ($design as $CATEGORY => $action) {
            if (array_key_exists('type', $action)) {
                switch ($action['type']) {
                    case CONSTANTS::BASED_ON['static']:
                        $this->sum_table [$CATEGORY] = $this->calculate_sum();
                        break;
                    case CONSTANTS::BASED_ON['http_codes']:
                        foreach (CONSTANTS::HTTP_CODES_TO_CHECK as $http_code) {
                            $this->sum_table [$CATEGORY][$http_code] = $this->calculate_sum();
                        }
                        break;
                    case CONSTANTS::BASED_ON['business']:
                        foreach ($available_codes as $code => $message) {
                            $this->sum_table [$CATEGORY][$code] = $this->calculate_sum();
                        }
                        break;
                    case CONSTANTS::BASED_ON['data']:
                        foreach ($parameters_list as $parameter => $value) {
                            $this->sum_table [$CATEGORY][$parameter] = $this->calculate_sum();
                        }
                        break;
                    default:
                        die (__FUNCTION__.__LINE__.' Что-то пошло не так...');
                }
            } else { // FIXME пока что большой костыль, сюда должна попасть только ветка со временем. Иначе все поломается
                foreach ($action as $SUBCATEGORY => $subaction) {
                    $this->sum_table [$CATEGORY][$SUBCATEGORY] = $this->calculate_sum();
                }
            }
        }
    }

    public function create_sum_table(array $data,
                                     array $available_codes = [],
                                     string $type) {

        switch ($type) {
            case CONSTANTS::TYPE['RESPONSE']:
                $design = array_merge(CONSTANTS::COMMON_CATEGORIES,
                                      CONSTANTS::RESPONSE_ADDITIONAL_CATEGORIES);
                break;
            case CONSTANTS::TYPE['STATUS']:
                $design = array_merge(CONSTANTS::COMMON_CATEGORIES,
                                      CONSTANTS::RESPONSE_ADDITIONAL_CATEGORIES,
                                          CONSTANTS::STATUS_ADDITIONAL_CATEGORIES);
                break;
            case CONSTANTS::TYPE['CALLBACK']:
                $design = array_merge(CONSTANTS::COMMON_CATEGORIES,
                                      CONSTANTS::CALLBACK_ADDITIONAL_CATEGORIES);
                break;
            default:
                die (__FUNCTION__.__LINE__.' Что-то пошло не так...');
                break;
        }

        $this->fill_sum_table_rows($design, $data, $available_codes);
        return $this->sum_table;
    }

    public static function generate_method_start_code                  (string $name)
    {
        $some_str = "\n    static function $name (MockHelper \$helper) {
        /* FIXME Эту секцию необходимо дописать вручную! */
        \$content  = json_decode(\$helper->getContent(),1); // Этот код только для json запросов!
        \$amount   = \$content['Amount'];   // FIXME Указать поле, содержащее сумму!
        \$currency = \$content['Currency']; // FIXME Указать поле, содержащее валюту!
        \$id       = time().microtime();
        \$helper->setRedisEx(\$id.'$name',\$content); // Сохраняем пришедший запрос для других операций
        ";
        return $some_str;
    }
    public static function generate_validation_code                    (array $request_data)
    {
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
    public static function generate_signature_validation_function_code (string $name){
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
    public static function generate_signature_validation_code          (string $name){
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
    public static function generate_http_response_code                 (string $sum_array){
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
    public static function generate_invalid_sum                        (string $sum_array){
    $some_str = "
       /**************************************
        * Неверная суммма (на основе Amount) *
        **************************************/
        if (\$amount == ".$sum_array."['invalid_sum']) {
            \$amount = (string)((float)\$amount + 1.22);
        }";
    return $some_str;
}
    public static function generate_invalid_currency                   (string $sum_array){
        $some_str = "
       /**************************************
        * Неверная суммма (на основе Amount) *
        **************************************/
        if (\$amount == ".$sum_array."['invalid_currency']) {
            \$currency = 'XAU';
        }";
        return $some_str;
    }
    public static function generate_data                               (array $request_data) {
        $some_str = "
       /*******************
        * Формируем ответ *
        * *****************/
         
       /* FIXME Уточнить данные! 
        * FIXME Не забываем про сумму, валюту и id!
        */
         
        \$data = [";
        foreach ($request_data as $parameter => $value) {
            $some_str .= "\n            '" . $parameter . "' => '".$value."',";
        }
        $some_str .= "\n        ];";
        return $some_str;
    }
    public static function generate_invalid_value                      (string $sum_array){
        $some_str = "
        /****************************************
         * Неверные значения (на основе Amount) *
         * **************************************/
        if (in_array(\$amount,".$sum_array."['invalid_parameter'])) {
            \$parameter = array_flip(".$sum_array."['invalid_parameter']);
            \$data [\$parameter[\$amount]] = self::\$invalid_string; // случайный текст
        }";
        return $some_str;
    }
    public static function generate_empty_value                        (string $sum_array){
        $some_str = "
       /**********************************
        * Пустое поле (на основе Amount) *
        **********************************/
        if (in_array(\$amount,".$sum_array."['empty_parameter'])) {
            \$parameter = array_flip(".$sum_array."['empty_parameter']);
            \$data [\$parameter[\$amount]] = ''; // пустое поле
        }";
        return $some_str;
    }
    public static function generate_parameter_remove                   (string $sum_array){
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
    public static function generate_redundant_values                   (string $sum_array){
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
    public static function generate_business_codes                     (string $sum_array, string $code_array = ''){
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
    public static function generate_signature_creation_function_code   (string $name){
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
    public static function generate_signature_creation                 (string $name){
        $some_str = "
        /**********************
         * Генерируем подпись *
         **********************/
         // FIXME Не забудте указать имя параметра!
        \$data['УКАЖИТЕ ИМЯ ПАРАМЕТРА С ПОДПИСЬЮ']= self::".$name."_signature_generation(\$data);";
        return $some_str;
    }
    public static function generate_delay                              (string $sum_array){
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
    public static function generate_response                           (){
        $some_str = "
        /********************
         * Синхронный ответ *
         ********************/
        \$helper->makeResponse(json_encode(\$data));";
        return $some_str;
    }
    public static function generate_method_end_code                    (){
        return "\n    }";
    }

}
