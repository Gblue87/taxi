<?php
namespace NewVision\CoreBundle\Lib;

/**
 * NewVisionText class
 *
 *  Helper class for text processing - slugify, transliterate
 *
 * @package    eNewVision
 * @author     Marin Ivanov
 */

class NewVisionText
{
    /**
     * Generates slug based on given text
     *
     * @param string $text
     */
    static public function slugify($text, $separatorUsed, $objectBeingSlugged)
    {
        $text = self::transliterateBulgarian($text);

        // replace all non letters or digits by -
        $text = preg_replace('/\W+/', $separatorUsed, $text);

        // trim and lowercase
        $text = strtolower(trim($text, $separatorUsed));

        return $text;
    }

    static public function transliterateBulgarian($bulgarian_string)
    {
        // Streamlined System for the Romanization of Bulgarian
        // L.L. Ivanov, On the Romanization of Bulgarian and English, Contrastive Linguistics, XXVIII, 2003, 2, pp. 109-118. ISSN 0204-8701; Errata, id., XXIX, 2004, 1, p. 157.
        $table = array(
                    'А' => 'A',
                    'Б' => 'B',
                    'В' => 'V',
                    'Г' => 'G',
                    'Д' => 'D',
                    'Е' => 'E',
                    'Ё' => 'Yo',
                    'Ж' => 'J',
                    'З' => 'Z',
                    'И' => 'I',
                    'Й' => 'Y',
                    'К' => 'K',
                    'Л' => 'L',
                    'М' => 'M',
                    'Н' => 'N',
                    'О' => 'O',
                    'П' => 'P',
                    'Р' => 'R',
                    'С' => 'S',
                    'Т' => 'T',
                    'У' => 'U',
                    'Ф' => 'F',
                    'Х' => 'H',
                    'Ц' => 'Ts',
                    'Ч' => 'Ch',
                    'Ш' => 'Sh',
                    'Щ' => 'Sht',
                    'Ь' => 'Y',
                    'Ы' => 'Y',
                    'Ъ' => 'A',
                    'Э' => 'E',
                    'Ю' => 'Yu',
                    'Я' => 'Ya',

                    'а' => 'a',
                    'б' => 'b',
                    'в' => 'v',
                    'г' => 'g',
                    'д' => 'd',
                    'е' => 'e',
                    'ё' => 'yo',
                    'ж' => 'j',
                    'з' => 'z',
                    'и' => 'i',
                    'й' => 'y',
                    'к' => 'k',
                    'л' => 'l',
                    'м' => 'm',
                    'н' => 'n',
                    'о' => 'o',
                    'п' => 'p',
                    'р' => 'r',
                    'с' => 's',
                    'т' => 't',
                    'у' => 'u',
                    'ф' => 'f',
                    'х' => 'h',
                    'ц' => 'ts',
                    'ч' => 'ch',
                    'ш' => 'sh',
                    'щ' => 'sht',
                    'ь' => 'y',
                    'ы' => 'y',
                    'ъ' => 'a',
                    'э' => 'e',
                    'ю' => 'yu',
                    'я' => 'ya',
        );

        $latin_string = str_replace(
            array_keys($table),
            array_values($table),$bulgarian_string
        );

        return $latin_string;
    }
}