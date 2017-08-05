<?php

require_once 'parser/config.php';

$sql = "SELECT category.`name` AS `category`, sub_category.`name` AS `subcategory`, product.`photo`, product.`article`, product.`applicability`, product.`analogs`, product.`performances`
FROM `products` AS product
LEFT JOIN `categories` category ON category.`id` = product.`category`
LEFT JOIN `sub_categories` sub_category ON sub_category.`id` = product.`sub_category`
WHERE category.`id`=3";

$stmt = $dbh->prepare($sql);

$res = $stmt->execute();

$string_csv = '"brd";"cat";"prc";"stk";"vimg";"num";"shdesc";"desc";"name"'.PHP_EOL;

while($row = $stmt->fetch(PDO::FETCH_NAMED)) {



    $string_csv .= '"TSN";"'.category($row['subcategory']).'";"0";"1";"'.img($row['article']).'";"'.$row['article'].'";"Применимо для: '.$row['applicability'].'";"Применимо для '. $row['applicability'].PHP_EOL.'Аналоги: '.$row['analogs'].'";"'.$row['performances'].'"'.PHP_EOL;
    NormalizationData::dump(img($row['article']));
}

file_put_contents('test.csv', $string_csv);

NormalizationData::dump($string_csv);

function category ($string)
{
    preg_match('#([а-яА-Я\s]+)(.*)#', $string, $math);

    return $math[1].$math[2];

}

function img ($string)
{
    $string = str_replace('.', '_', $string);
    return $string.'.jpg';

}