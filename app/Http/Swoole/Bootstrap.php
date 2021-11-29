<?php

/**
 * @desc    composer自动加载，这是单独的项目
 * @author  BabyBuffary
 * @date    2020-06-05
 */
class Bootstrap
{
    public function __construct ()
    {
        defined('ROOT_PATH') || define('ROOT_PATH', realpath(__DIR__ . '/../laravel/'));

        // composer自动加载
        require_once ROOT_PATH . '/vendor/autoload.php';
    }
}
