<?php

class NormalizationData {

    public static function dump($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public static function getArticle($string) {



        return trim(str_replace('article', PHP_EOL, $string));
    }

    public static function BRToNewString($string) {

        $string = str_replace('property', '', $string);

        return strip_tags(str_replace('<br />', PHP_EOL, $string));

    }

    public static function getAnalogs($link) {

        $analogs = str_replace('<BR>', PHP_EOL, file_get_contents('http://tsn.tsitron.ru/'.$link));

        $analogs = str_replace('<h3>Аналоги:</h3>', '', $analogs);

        $analogs = strip_tags($analogs);

        return trim($analogs);

    }

    public static function getPicture($link) {

        $file = file_get_contents($link);

        $path_info = pathinfo($link);

        file_put_contents('uploads/'.$path_info['basename'], $file);

        return $path_info['basename'];

    }
    
}