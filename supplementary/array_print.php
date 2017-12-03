<?php
/**
 * Печатает массив в формате исполняемого кода на php
 *
 * $array_to_print - Массив, который необходимо распечатать;
 * $name           - Имя массива
 * $return_text    - (true)  возвращает массив в виде текста.
 *                   (false) выводит массив с помощью "echo".
 * @kmalezhi
 */
namespace supplementary\array_print;

class array_print
{
    const SPACES  = "    "; // Число пробелов в табуляции (4)
    const NEWLINE = "\n";   // Символ переноса строки

    private $level = 0; // Переменная необходима для рекурсивных вызовов метода form_array
    private $array_text = '';

    public function __construct(array $array_to_print, bool $return_text = true, string $name = 'some_array') {
        $this->array_text = $this->form_array($array_to_print, $return_text, $name);
    }
    public function __toString()
    {
        return $this->array_text;
    }
    private function form_array(array $var, bool $return, string $name){
        $spaces  = self::SPACES;
        $newline = self::NEWLINE;
        $tabs    = $spaces;
        for ($i = 1; $i <= $this->level; $i++) {
            $tabs .= $spaces;
        }
        if ($this->level === 0) {
            $output = "\nstatic private $".$name.' = ['.$newline;
        } else {
            $output = "'".$name."' => [". $newline;
        }
        foreach($var as $key => $value) {
            if (is_array($value)) {
                $this->level++;
                $value = $this->form_array($value,true, $key);
                $this->level--;
                $output .= $tabs . $value . $tabs . '],'. $newline;
            } else {
                $output .= $tabs . "'" . $key . "' => '" . $value ."',". $newline;
            }
        }
        if ($this->level === 0) {
            $output .= '];';
        }
        if ($return) {
            return $output;
        } else {
            echo $output;
            return false;
        }
    }
}