<?php

class NormalizationData {

    public static function dump($data, $console=false)
    {
        if(!$console) {
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
        }
        else {
            var_dump($data);
        }

    }

    public static function getArticle($string)
    {



        return trim(str_replace('article', PHP_EOL, $string));
    }

    public static function BRToNewString($string)
    {

        $string = str_replace('property', '', $string);

        return strip_tags(str_replace('<br />', PHP_EOL, $string));

    }
    public static function clearCategory($string)
    {

        $string = preg_replace('#filter[\d]#U', '', $string);

        return strip_tags(str_replace('<br />', PHP_EOL, $string));

    }

    public static function PackingRate($string)
    {

        $string = str_replace('min_quantity', '', $string);

        return strip_tags($string);

    }

    public static function getAnalogs($link)
    {

        $analogs = str_replace('<BR>', PHP_EOL, file_get_contents('http://tsn.tsitron.ru/'.$link));

        $analogs = str_replace('<h3>Аналоги:</h3>', '', $analogs);

        $analogs = strip_tags($analogs);

        return trim($analogs);

    }

    public static function getPicture($link)
    {

        $file = file_get_contents($link);

        $path_info = pathinfo($link);

        file_put_contents('uploads/'.$path_info['basename'], $file);

        return $path_info['basename'];

    }

    public static function RecordToFile($file, $string)
    {
        return file_put_contents($file, $string.PHP_EOL, LOCK_EX | FILE_APPEND);
    }
    
}