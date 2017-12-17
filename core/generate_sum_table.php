<?php
/**
 * User: Konstantin
 * Date: 06.12.2017
 * Time: 0:26
 */

namespace core\main;

require_once 'mock_generator_constants.php'; use core\main\mock_generator_constants as CONSTANTS;

class generate_sum_table
{
    private $sum = 1000; // Хранит текущее значение суммы для генерации таблицы. Можно переопределить при создании экземпляра класса
    private $sum_table = [];
    private $minor = false;

    /**
     * Конструктор класса.
     * Устанавливает сумму с которой начнется генерация и наличие отсуствие у валюты копеек
     *
     * @param bool $minor - true (сумма без "копеек") / false (сумма с "копейками")
     * @param mixed $sum - сумма с которой начнется генерация
     */
    public function __construct($sum = false, bool $minor = false)
    {
        if (($sum) AND is_integer($sum)) {
            $this->sum = (integer)$sum;
        }
        $this->minor = $minor;
    }

    /**
     * Возвращает последующую сумму для генерации таблицы
     * @return string - сумма в формате ХХХ.00 (minor = false) или ХХХ (minor = true)
     */
    private function calculate_sum (){
        $this->sum++;
        if ($this->minor) {
            $sum = (string)$this->sum;
        } else {
            $sum = (((string)$this->sum).'.00');
        }
        return $sum;
    }

    /**
     * Подготовка имен параметров для генерации таблицы сумм
     * Принцип работы:
     *      Берет параметры из входного массива и сохраняет в выходной.
     *      В случае вложенных массивов переобразует имена параметров следующим образом: "category1->category2->parameter"
     *
     * @param $inputArray           - данные, которые должен отправить мок, преобразованные к массиву
     * @param string $currentPrefix - Используется только для рекурсивного вызова.
     * @return array                - Массив имен параметров, необходимый для генерации таблицы сумм
     */
    private function prepare_parameters_list ($inputArray, $currentPrefix = ''){
        $preparedList = [];
        foreach ($inputArray as $category => $parameter) {
            if (is_array($parameter)) {
                $nextLevelPrefix = $currentPrefix."$category->";
                $preparedList = array_merge($preparedList, $this->prepare_parameters_list($parameter, $nextLevelPrefix));
            } else {
                $preparedList = array_merge($preparedList, [$currentPrefix.$category]);
            }
        }
        return $preparedList;
    }

    private function fill_sum_table_rows(array $design,
                                         array $parameters_list,
                                         array $available_codes = []
    )
    {
        foreach ($design as $category => $action) {
            if (array_key_exists('type', $action)) {
                switch ($action['type']) {
                    case CONSTANTS::BASED_ON['static']:
                        $this->sum_table [$category] = $this->calculate_sum();
                        break;
                    case CONSTANTS::BASED_ON['http_codes']:
                        foreach (CONSTANTS::HTTP_CODES_TO_CHECK as $http_code) {
                            $this->sum_table [$category][$http_code] = $this->calculate_sum();
                        }
                        break;
                    case CONSTANTS::BASED_ON['business']:
                        foreach ($available_codes as $code => $message) {
                            $this->sum_table [$category][$code] = $this->calculate_sum();
                        }
                        break;
                    case CONSTANTS::BASED_ON['data']:
                        foreach ($parameters_list as $parameter) {
                            $this->sum_table [$category][$parameter] = $this->calculate_sum();
                        }
                        break;
                    default:
                        die (__FUNCTION__.__LINE__.' Что-то пошло не так...');
                }
            } else { // FIXME пока что большой костыль, сюда должна попасть только ветка со временем. Иначе все поломается
                foreach ($action as $SUBCATEGORY => $subaction) {
                    $this->sum_table [$category][$SUBCATEGORY] = $this->calculate_sum();
                }
            }
        }
    }

    public function create_sum_table(array $data, string $type, array $available_codes = []) {
        switch ($type) {
            case CONSTANTS::TYPE['RESPONSE']:
                $design = array_merge(
                    CONSTANTS::COMMON_CATEGORIES,
                    CONSTANTS::RESPONSE_ADDITIONAL_CATEGORIES);
                break;
            case CONSTANTS::TYPE['STATUS']:
                $design = array_merge(
                    CONSTANTS::COMMON_CATEGORIES,
                    CONSTANTS::RESPONSE_ADDITIONAL_CATEGORIES,
                    CONSTANTS::STATUS_ADDITIONAL_CATEGORIES);
                break;
            case CONSTANTS::TYPE['CALLBACK']:
                $design = array_merge(
                    CONSTANTS::COMMON_CATEGORIES,
                    CONSTANTS::CALLBACK_ADDITIONAL_CATEGORIES);
                break;
            default:
                die (__FUNCTION__.__LINE__.' Что-то пошло не так...');
                break;
        }
        $this->fill_sum_table_rows($design, $this->prepare_parameters_list($data), $available_codes);
        return $this->sum_table;
    }
}