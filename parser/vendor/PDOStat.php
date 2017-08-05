<?php

class PDOStat extends PDOStatement
{
    protected $_debugValues = null;

    protected function __construct()
    {
        // этот пустой конструктор необходим
    }

    public function execute($values=array())
    {
        $this->_debugValues = $values;
        try {
            $t = parent::execute($values);
            // *размышления о логировании*
        } catch (PDOException $e) {
            // *размышления о логировании*
            throw $e;
        }

        return $t;
    }

    public function _debugQuery($replaced=true)
    {
        $q = $this->queryString;

        if (!$replaced) {
            return $q;
        }

        return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, '_debugReplace'), $q);
    }

    protected function _debugReplace($m)
    {
        $v = $this->_debugValues[$m[1]];
        if ($v === null) {
            return "NULL";
        }
        if (!is_numeric($v)) {
            $v = str_replace("'", "''", $v);
        }

        return "'". $v ."'";
    }
}

// см. http://www.php.net/manual/en/pdo.constants.php
/*
// создание PDO со своим PDOStatement классом
$pdo = new PDO($dsn, $username, $password, $options);

// подготовка запроса
$query = $pdo->prepare("INSERT INTO mytable (column1, column2, column3)
  VALUES (:col1, :col2, :col3)");

// выполнение подготовленного выражения
$query->execute(array(
    'col1' => "hello world",
    'col2' => 47.11,
    'col3' => null,
));

// вывод запроса и запроса с подставленными данными
var_dump( $query->queryString, $query->_debugQuery() );
*/