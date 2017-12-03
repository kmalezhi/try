<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 26.11.2017
 * Time: 22:46
 */

namespace core\main;
use core\main\mock_generator_constants as CONSTANTS;

class mock_description
{
    private $mock_description = '';

    /**
     * @return string - возвращает описание мока, созданное при создании экземпляра класса.
     */
    public function get_mock_description () {
        return $this->mock_description;
    }
    public function clear_mock_description () { // TODO А зачем тут нужна эта функция? :-)
        $this->mock_description = '';
    }

    /**
     * mock_description constructor.
     * Создает описание мока на основе массива с суммами для тестирования.
     * @param array $data_array - массив с суммами для тестирования.
     */
    public function __construct(array $data_array)
    {
        // Будем искать категории по всем описаниям:
        $description_table = array_merge(CONSTANTS::COMMON_CATEGORIES,
                                         CONSTANTS::RESPONSE_ADDITIONAL_CATEGORIES,
                                             CONSTANTS::STATUS_ADDITIONAL_CATEGORIES,
                                                CONSTANTS::CALLBACK_ADDITIONAL_CATEGORIES);
        // Перебираем все разделы, которые есть в таблице сумм:
        foreach ($data_array as $category => $content) {
            if (array_key_exists('description', $description_table[$category])) {
                // В разделе несколько ситуаций, перебираем все, добавляем описания:
                if (is_array($content)) {
                    foreach ($content as $field_name => $sum) {
                        $str = "\n" . $sum . CONSTANTS::DELIMITER . $description_table[$category]['description'] . CONSTANTS::DELIMITER . $description_table[$category]['note'];
                        $this->mock_description .= str_replace("REPLACE_ME", $field_name, $str); // Уточняем описание
                    }
                // В разделе одна ситуация, записываем её описание:
                } else {
                    $sum = $content;
                    $this->mock_description .= "\n" . $sum . CONSTANTS::DELIMITER . $description_table[$category]['description'] . CONSTANTS::DELIMITER . $description_table[$category]['note'];
                }
            } else { // FIXME пока что большой костыль, сюда должна попасть только ветка со временем. Иначе все поломается
                     // FIXME переписать нафиг, как дойдут руки
                foreach ($description_table[$category] as $subcategory => $subdescription) {
                    $this->mock_description .= "\n" . $content[$subcategory] . CONSTANTS::DELIMITER . $subdescription['description'] . CONSTANTS::DELIMITER . $subdescription['note'];
                }
            }
        }

    }
}