<?php
/**
 * User: Konstantin
 * Date: 26.11.2017
 * Time: 22:52
 */

namespace core\main;

class mock_generator_constants
{
    /**
     * Типы мока, которые возможно сгенерить:
     */
    const TYPE = [
        'RESPONSE' => 0,
        'STATUS' => 1,
        'CALLBACK' => 2,
    ];
    /**
     * Список http кодов, которые возможно будет проверить с помощью мока
     */
    const HTTP_CODES_TO_CHECK = [
        400,
        403,
        404,
        500,
        502,
    ];
    /**
     * Тело ответа с 4хх и 5хх кодами, которое генерирует мок
     */
    const HTTP_MESSAGE = 'Mock generated message';
    /**
     * Время ожидания в моке до обрыва связи (секунд)
     */
    const DISCONNECTION_TIMEOUT = 320;
    /**
     * Время задержки ответа (секунд)
     */
    const WAIT_TIMEOUT = 10;
    /**
     * Имена "лишних" параметров, которые добавятся в соответствующей ситуации в моке.
     */
    const REDUNDANT_PARAMETER_1 = 'redundant_1';
    const REDUNDANT_PARAMETER_2 = 'redundant_2';
    /**
     * Следующие таблицы являются по своей сути сценариями построения логики мока.
     * Также содержат описание и по их подобию генерируется таблица тестовых сумм.
     * Важно! Изменять вручную только понимая зачем и как.
     */
    const COMMON_CATEGORIES                = [
        'missing_parameter'    => [
            'type' => self::BASED_ON['data'],
            'description' => 'Отсуствует параметр REPLACE_ME',
            'note' => '',
        ],
        'empty_parameter'      => [
            'type' => self::BASED_ON['data'],
            'description' => 'Пустое значение параметра REPLACE_ME',
            'note' => '',
        ],
        'invalid_parameter'    => [
            'type' => self::BASED_ON['data'],
            'description' => 'Неверное значение параметра REPLACE_ME',
            'note'        => 'Значение заменено на строку случаных символов',
        ],
        'business_code'        => [
            'type' => self::BASED_ON['business'],
            'description' => 'В синхронном ответе ПС бизнес код REPLACE_ME',
            'note'        => '***ЗАПОЛНИ МЕНЯ***',
        ],
        'empty_body'           => [
            'type' => self::BASED_ON['static'],
            'description' => 'Пустое тело сообщения',
            'note'        => '',
        ],
        'redundant_parameters' => [
            'type' => self::BASED_ON['static'],
            'description' => 'Лишние параметры в сообщении',
            'note'        => 'Добавятся параметры: '.self::REDUNDANT_PARAMETER_1.' и '.self::REDUNDANT_PARAMETER_2,
        ],
        'invalid_sum'          => [
            'type' => self::BASED_ON['static'],
            'description' => 'Ошибка в сумме',
            'note'        => 'Сумма больше на 1.22',
        ],
        'invalid_currency'     => [
            'type' => self::BASED_ON['static'],
            'description' => 'Ошибка в валюте',
            'note'        => 'Валюта XAU - Золото (тройская унция)',
        ],
    ]; // Ситуации общие для любой операции и типа ответа
    const RESPONSE_ADDITIONAL_CATEGORIES   = [
        'timeout'   => [
            self::DISCONNECTION_TIMEOUT => [
                'type' => self::BASED_ON['static'],
                'description' => 'Нет синхронного ответа (обрыв связи)',
                'note' => 'Задержка в моке '.self::DISCONNECTION_TIMEOUT.' секунд',
            ],
            self::WAIT_TIMEOUT => [
                'type' => self::BASED_ON['static'],
                'description' => 'Успешный синхронный ответ с задержкой',
                'note' => 'Задержка в моке '.self::WAIT_TIMEOUT.' секунд',
            ],
        ],
        'http_code' => [
            'type' => self::BASED_ON['http_codes'],
            'description' => 'Ответ с http кодом ошибки REPLACE_ME',
            'note' => 'Сообщение: '.self::HTTP_MESSAGE,
        ],
    ]; // Дополнительные ситуации (синхронный ответ)
    const STATUS_ADDITIONAL_CATEGORIES     = [
        'decline_after_waiting' => [
            'type' => self::BASED_ON['static'],
            'description' => 'Ситуация, приводящая к decline транзакции, после промежуточного статуса (waiting)',
            'note' => '',
        ],
        'infinite_waiting' => [
            'type' => self::BASED_ON['static'],
            'description' => 'Мок всегда будет отвечать промежуточным статусом (waiting)',
            'note' => 'Для проверки автодеклайна',
        ],
    ]; // Дополнительные ситуации (опрос статуса)
    const CALLBACK_ADDITIONAL_CATEGORIES   = [
        'several_callbacks' => [
            'success_decline' => [
                'type' => self::BASED_ON['static'],
                'description' => 'Отправка 2-х различных callback (сначала успешная транзакция, затем отклоненная)',
                'note' => 'Результат операции устанавливается по первому пришедшему callback',
            ],
            'decline_success' => [
                'type' => self::BASED_ON['static'],
                'description' => 'Отправка 2-х различных callback (сначала отклоненная транзакция, затем успешная)',
                'note' => 'Результат операции устанавливается по первому пришедшему callback',
            ],
        ]
    ]; // Дополнительные ситуации (callback)
    /**
     * Разделитель, который будет использован в описании мока
     */
    const DESCRIPTION_DELIMITER = ' || ';
    /**
     * Вспомогательные константы для генерации мока.
     */
    const BASED_ON = [
        'static' => 0,
        'business' => 1,
        'data' => 2,
        'http_codes' => 3,
    ];
    /**
     * Разделитель для вложенных массивов в таблице сумм.
     */
    const SUM_TABLE_DELIMITER = '->';
}