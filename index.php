<?php

require_once 'config.php';

$file = file_get_contents('links.txt');

$links = explode(PHP_EOL, $file);

$c = 0;

foreach ($links as $link) {

    if(!empty($link)) {

        $queryHTML = new QueryHTML($link);

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
        //Собираем Категории
        $categories= $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[7]');
        //Собираем Подкатегории
        $sub_categories= $queryHTML->query('//table[@class="web_ar_datagrid items-table"]/tr[position() > 2]/td[8]');



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

        //Распарсиваем Категории
        $i=0;
        foreach ($categories as $category) {

            $all[$i]['category'] = NormalizationData::clearCategory($category->nodeValue);
            $i++;
        }

        //Распарсиваем Категории
        $i=0;
        foreach ($sub_categories as $sub_category) {

            $all[$i]['sub_category'] = NormalizationData::clearCategory($sub_category->nodeValue);
            $i++;
        }

        echo 'Закончен парсинг страницы';

        $time = time();

        foreach($all as $product) {

            $sql = "INSERT INTO `products`(`id`, `category`, `sub_category`, `photo`, `article`, `applicability`, `analogs`, `performances`, `packing_rate`, `link`, `timestamp`) VALUES (NULL, (SELECT `categories`.`id` FROM `categories` WHERE `categories`.`name` = :category_name), (SELECT `sub_categories`.`id` FROM `sub_categories` WHERE `sub_categories`.`name` = :sub_categories_name), :photo, :article, :applicability, :analogs, :performances, :packing_rate, :link, :timestamp)";


            $stmt = $dbh->prepare($sql);

            $res_record = $stmt->execute([
                ':category_name' => $product['category'],
                ':sub_categories_name' => $product['sub_category'],
                ':photo' => 'uploads/'.$product['image'],
                ':article' => $product['article'],
                ':applicability' => $product['applicability'],
                ':analogs' => $product['analog'],
                ':performances' => $product['performance'],
                ':packing_rate' => $product['packing_rate'],
                ':link' => $link,
                ':timestamp' => $time,
            ]);

            if(!$res_record) {

                NormalizationData::dump($dbh->errorInfo(), true);
                NormalizationData::dump($stmt->queryString);

            }

            echo 'Запись в базу завершена' . PHP_EOL;

        }



    }

    if($c >3) {
        break;
    }

    $c++;
}

//Выводим дамп
//NormalizationData::dump($all);





