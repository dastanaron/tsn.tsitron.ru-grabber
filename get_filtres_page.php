<?php
require_once 'autoregister.php';

$url = 'http://tsn.tsitron.ru/tsn/catalogue/?dat_field13=TSN&dat_field2=&dat_field3=';

$queryHTML = new QueryHTML($url);

$queryHTML->initDom();

//Получаем список категорий
$categories = $queryHTML->query('//select[@id="dat_field2"]/option');
//Получаем подкатегории


//Категории
foreach ($categories as $category) {

    $filter_categories[] = $category->getAttribute('value');
}


//NormalizationData::dump($filter_categories);

foreach($filter_categories as $filter_category) {

    $url_filter = 'http://tsn.tsitron.ru/tsn/catalogue/?dat_field13=TSN&dat_field2='.urlencode($filter_category).'&dat_field3=';

    $queryHTML_filter = new QueryHTML($url_filter);

    $queryHTML_filter->initDom();

    $sub_categories = $queryHTML_filter->query('//select[@id="dat_field3"]/option');


    //Подкатегории
    foreach ($sub_categories as $sub_category) {

        $filter_sub_categories[$filter_category][] = $sub_category->getAttribute('value');
    }

}


NormalizationData::dump($filter_sub_categories);