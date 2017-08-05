<?php
require_once 'config.php';

/*

//Этот кусок собирает категории и подкатегории
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


NormalizationData::dump($filter_categories);



foreach($filter_categories as $filter_category) {

    $url_filter = 'http://tsn.tsitron.ru/tsn/catalogue/?dat_field13=TSN&dat_field2='.urlencode($filter_category).'&dat_field3=';

    $queryHTML_filter = new QueryHTML($url_filter);

    $queryHTML_filter->initDom();

    $sub_categories = $queryHTML_filter->query('//select[@id="dat_field3"]/option');

    $time = time();

    //Если категория не пустая, то записать
    if(!empty($filter_category)) {

        $sql_categories = "INSERT INTO `categories` (`id`, `name`, `timestamp`) VALUES (NULL, '$filter_category', '$time')";

        $stmt_categories = $dbh->prepare($sql_categories);

        $stmt_categories->execute();

        $insert_id = $dbh->lastInsertId();

        //Подкатегории
        foreach ($sub_categories as $sub_category) {

            echo $insert_id . '<br/>';

            $filter_sub_categories[$filter_category][] = $sub_category->getAttribute('value');

            $sub_categories_name = $sub_category->getAttribute('value');

            //Если не пустое имя подкатегории
            if(!empty($sub_categories_name)) {

                //Записываем связку категории с субкатегорией
                $sql_sub_categories = "INSERT INTO `sub_categories` (`id`, `id_categories`, `name`, `timastamp`) VALUES (NULL, '$insert_id', '$sub_categories_name', '$time')";

                $stmt_sub_categories = $dbh->prepare($sql_sub_categories);

                $stmt_sub_categories->execute();

               // NormalizationData::dump($dbh->errorInfo());

            }
        }

    }

}*/

/*
//Собираем все категории и подкатегории по связке из базы.
$sql = "SELECT `sub_categories`.`name` AS `sub_category_name`, categories.`name` AS `category_name` FROM `sub_categories` LEFT JOIN `categories` categories ON categories.`id` = `id_categories` WHERE categories.`id` = 15";

$stmt = $dbh->prepare($sql);

$res = $stmt->execute();

$paginate_array = array();

$links_file = 'links.txt';

while ($row = $stmt->fetch(PDO::FETCH_NAMED)) {

    //Формируем ссылку по фильтру
    $url_filter = 'http://tsn.tsitron.ru/tsn/catalogue/?dat_field13=TSN&dat_field2=' . urlencode($row['category_name']) . '&dat_field3=' . urlencode($row['sub_category_name']) . '';

    $queryHTML = new QueryHTML($url_filter);

    $queryHTML->initDom();

    //Записываем собранные пути для ссылок
    NormalizationData::RecordToFile($links_file, $url_filter);

    //Получаем постраничную навигацию
    $paginate_url = $queryHTML->query('//span[@id="next"]/a');

    //Если не пустая пагинация то записываем пагинацию и переходим по странице пагинации
    if ($paginate_url['length'] !== 0) {

        foreach ($paginate_url as $paginate) {

            $paginate_page = $paginate->getAttribute('href');

            //Записываем ссылки постраничной навигации
            NormalizationData::RecordToFile($links_file, $paginate_page);

        }

    }

}*/

//Файл для записи ссылок
$links_file = 'links.txt';

//Формируем ссылку по фильтру
$url = 'http://tsn.tsitron.ru/tsn/catalogue/';

while(true) {

    //Записываем ссылки
    NormalizationData::RecordToFile($links_file, $url);

    $queryHTML = new QueryHTML($url);

    $queryHTML->initDom();

    //Получаем постраничную навигацию
    $paginate_url = $queryHTML->query('//div [@class="listing"]/span[@class="next"]/a');

    //Если не пустая пагинация то записываем пагинацию и переходим по странице пагинации
    if ($paginate_url['length'] !== 0) {

        //Получаем ссылку на следующую страницу
        $link_next = GetOneAttributeDOM($paginate_url);

        if(!empty($link_next)) {

            //Если ссылка не пустая, записываем новый url
            $url = 'http://tsn.tsitron.ru'.$link_next;

        }
        else {
            break;
        }

    }

}



function GetOneAttributeDOM(DOMNodeList $objects) {
    foreach ($objects as $object) {
        return $object->getAttribute('href');
    }
}