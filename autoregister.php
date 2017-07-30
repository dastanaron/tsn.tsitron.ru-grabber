<?php
spl_autoload_register(function ($class) {
    include 'vendor/' . $class . '.php';
});
