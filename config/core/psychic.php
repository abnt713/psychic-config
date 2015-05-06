<?php

final class PsychicConfig{

    private static $config_dir;
    private static $configs;

    private static function init(){
        if(is_null(self::$config_dir)){
            self::$config_dir = realpath(__DIR__ . '/..');
            self::$configs = array();
        }
    }

    private static function load($configuration_type){
        self::init();

        if(isset(self::$configs[$configuration_type])){
            return self::$configs[$configuration_type];
        }

        if(self::require_config_file($configuration_type)){
            $config = self::get_config_object($configuration_type);
            self::add_config($configuration_type, $config->config());
            return $config->config();
        }else{
            trigger_error('Could not load configuration ' . $configuration_type);
            return false;
        }
    }

    public static function value($configuration_type, $value){
        $config = self::load($configuration_type);
        if(isset($config[$value])){
            return $config[$value];
        }else{
            return null;
        }
    }

    private static function get_configuration_path($configuration_type){
        $config_file = strtolower($configuration_type) . '.php';
        $config_file_full_path = self::$config_dir . '/' . $config_file;
        if(is_file($config_file_full_path)){
            require_once $config_file_full_path;
            return true;
        }else{
            return false;
        }
    }

    private static function get_config_object($configuration_type){
        $class_name = ucfirst($configuration_type) . 'Config';
        $config_obj = new $class_name();
        if($config_obj instanceof PsychicConfiguration){
            return $config_obj;
        }else{
            trigger_error('Configuration for ' . $configuration_type);
        }
    }

    private static function add_config($configuration_type, $config){
        self::$configs[$configuration_type] = $config;
    }
}
