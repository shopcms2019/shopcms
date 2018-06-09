<?php

namespace Shop;
class Base {
    
    /**
     * Версия магазина
     * 
     * @var string
     * @access protected
     */
    protected $shop_version;
    
    /**
     * Установлен ли магазин
     * 
     * @var boolean
     * @access protected
     */
    protected $is_installed;
    
    /**
     * Логическая переменная, которая показывает, есть ли вообще на сервере СУБД
     * 
     * TRUE - хотя бы одна СУБД присутствует
     * FALSE - СУБД на сервере не обнаружено
     * 
     * @var bool
     * @access protected
     */
    protected $sql_exists;
    
    /**
     * Массив значений, использующихся везде в магазине
     * 
     * @var array
     * @access protected
     */
    protected $config;
    
    
    
    public function __construct() {
        
        $root = "http://".filter_input(INPUT_SERVER, 'SERVER_NAME');
        $xml = $this->read_xml($root."/config/config.xml");
        
        if (!$xml) {
            die("This class cannot be instantiated without a valid XML configuration file");
        }
        
        $this->config   =   array();
        $this->add_var('server_db_host', $xml->database->server);
        $this->add_var('server_db_user', $xml->database->user);
        $this->add_var('server_db_pass', $xml->database->password);
        $this->add_var('server_db_name', $xml->database->db);
        
    }
    
    protected function read_xml(string $filename) {
        $file = simplexml_load_file($filename);
        if ($file) {
            return $file;
        }
        
    }
    
    /**
     * Добавляет конфигурационную переменную - на самом деле, это ещё одна пара
     * "ключ - значение" массива $this->config
     * 
     * @param string $name Название переменной, которую нужно добавить
     * @param string $value Значение этой переменной
     * @return void
     */
    protected function add_var(string $name, string $value):void {
        if (!array_key_exists($name, $this->config)) {
            $this->config[$name]    =   trim($value);
        }
    }
    
    
    
    /**
     * Получает значение переменной с заданным ключом. Если ключа в массиве не существует,
     * возвращается null, иначе - значение переменной
     * 
     * @param string $name Название переменной, значение которой необходимо получить
     * @return mixed
     */
    public function get_var(string $name) {
        if (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        
        else {
            return null;
        }
    }
    
    
    /**
     * Устанавливает значение переменной is_installed
     * 
     * @param bool $value
     * @return void
     */
    protected function setInstalled(bool $value):void {
        $this->is_installed = $value;
    }
    
    /**
     * Получает значение переменной is_installed
     * 
     * @return bool
     */
    public function getIsInstalled():bool {
        return $this->is_installed;
    }
    
    /**
     * Статическая функция класса, позволяющая автоматически загружать необходимые
     * классы по ходу выполнения
     * 
     * @param type $class_name Класс, подлежащий загрузке
     * @return void
     */
    public static function autoload($class_name):void {
        
        $path = str_replace("\\", "/", $class_name);
        $path = str_replace("Shop", "classes", $path);
        
        $path = $path.".php";
        
        include $path;
    }
    
    /**
     * Получает текущую версию магазина
     * 
     * @return string
     */
    public function getShopVersion():string {
        return $this->shop_version;
    }
    
    /**
     * Изменяет версию магазина
     * 
     * @param string $shop_version Новая версия
     * @return void
     */
    public function setShopVersion(string $shop_version):void {
        $this->shop_version = $shop_version;
    }
    
    
    /**
     * Функция возвращает флаг, показывающий, имеется ли на данном сервере доступ к СУБД.
     * Метод не конкретизирует, какое именно подключение доступно, просто возвращает true или false
     * 
     * @return bool
     */
    public function getSQL():bool{
        
        $root = "http://".filter_input(INPUT_SERVER, "SERVER_NAME");
        $xml = $this->read_xml($root."/config/config.xml");
        $cnt = count($xml->sql->layers->layer);
        $cnt_classes = count($xml->sql->classes->class);
        
        $db = false;
        for ($i = 0; $i < ($cnt - 1); $i++) {
            
            if (function_exists($xml->sql->layers->layer[$i]['function'])) {
                $db['exists'] = true;
                break;
            }
        }
        
        for ($j = 0; $j < ($cnt_classes - 1); $j++) {
            if (class_exists($xml->sql->classes->class[$j]['name'])) {
                $db['exists'] = true;
                break;
            }
        }
        
        $this->sql_exists   =   $db['exists'];
        return $this->sql_exists;
    }
    
    /**
     * Функция возвращает список СУБД, которые можно использовать. 
     * Они не обязательно должны быть доступны на данном сервере
     * 
     * @return array
     */
    public function getAllSQL():array {
        $root = "http://".filter_input(INPUT_SERVER, "SERVER_NAME");
        $xml = $this->read_xml($root."/config/config.xml");
        $cnt = count($xml->sql->layers->layer);
        $cnt_classes = count($xml->sql->classes->class);
        
       
        $layers = $classes = array();
        for ($i = 0; $i < ($cnt - 1); $i++) {
            
                $layers []['name'] = $xml->sql->layers->layer[$i]['name'];
                $layers []['function'] = $xml->sql->layers->layer[$i]['function'];
        }
        
        for ($j = 0; $j < ($cnt_classes); $j++) {
                $classes[]['class_name'] = $xml->sql->classes->class[$j]['name'];

        }
        
        return array_merge($layers, $classes);
        
    }
}