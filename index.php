<?php

require_once 'autoregister.php';

$url = 'http://tsn.tsitron.ru/tsn/catalogue/?dat_field13=TSN&dat_field2=1.+%D0%94%D0%95%D0%A2%D0%90%D0%9B%D0%98+%D0%A1%D0%98%D0%A1%D0%A2%D0%95%D0%9C%D0%AB+%D0%97%D0%90%D0%96%D0%98%D0%93%D0%90%D0%9D%D0%98%D0%AF&dat_field3=1.1.+%D0%9A%D0%B0%D1%82%D1%83%D1%88%D0%BA%D0%B8+%D0%B7%D0%B0%D0%B6%D0%B8%D0%B3%D0%B0%D0%BD%D0%B8%D1%8F+%D1%80%D0%BE%D1%81%D1%81.%D0%BC%D0%B0%D1%80%D0%BA%D0%B8+%D0%BC%D0%B0%D1%81%D0%BB%D0%BE%D0%BD%D0%B0%D0%BF%D0%BE%D0%BB%D0%BD%D0%B5%D0%BD.';

$queryHTML = new QueryHTML($url);

$queryHTML->initDom();

//Собираем ссылки на картинки
$images = $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[1]/a');
//Собираем содержимое столбца артиклей
$articles = $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[2]');
//Собираем содержимое применяемости
$applicability = $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[3]');
//Собираем ссылки на аналоги
$analogs = $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[4]/div/a');
//Собираем Исполнение
$performances = $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[5]');
//Собираем норму упаковки
$packing_rates= $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[6]');



//Инициируем пустой массив
$all = array();


//Записываем название картинки в массив, а также сохраняем картинку с помощью метода в классе
$i=0;
foreach ($images as $object) {

    $all[$i]['image'] = NormalizationData::getPicture('http://tsn.tsitron.ru'.$object->getAttribute('href'));
    $i++;
}

//Отрезаем лишнее, приводим строку в должный вид, записываем в массив
$i=0;
foreach ($articles as $object) {

    $all[$i]['article'] = NormalizationData::getArticle($object->nodeValue);
    $i++;
}

//Отрезаем лишнее, приводим строку в должный вид, записываем в массив
$i=0;
foreach ($applicability as $object) {

    $all[$i]['applicability'] = NormalizationData::BRToNewString($object->ownerDocument->saveXML($object));
    $i++;
}

//Регуляркой выцепляем ссылку на мини страничку аналогов, делаем в нее запрос, собираем строку и записываем в массив
$i=0;
foreach ($analogs as $object) {

    preg_match('#window\.open\(\"\/(.*)\"\,#U', $object->getAttribute('onclick'), $match);
    $all[$i]['analog'] =  NormalizationData::getAnalogs($match[1]);
    $i++;
}

//Распарсиваем Исполнение
$i=0;
foreach ($performances as $performance) {

    $all[$i]['performance'] = NormalizationData::BRToNewString($performance->nodeValue);
    $i++;
}

//Распарсиваем норму упаковки
$i=0;
foreach ($packing_rates as $packing_rate) {

    $all[$i]['packing_rate'] = NormalizationData::PackingRate($packing_rate->nodeValue);
    $i++;
}



//Выводим дамп
NormalizationData::dump($all);





