<?php
namespace Shop\DB;
/**
 * Абстрактный класс, который наследуется всеми классами, работающими с СУБД. 
 * В классе представлены методы, являющиеся общими для всех классов, работающих с СУБД
 * 
 * @package DB
 * @category DB
 */
abstract class DB extends \Shop\Base {
    
    /**
     * Сервер для подключения к базе данных
     * 
     * @var string 
     * @access protected
     */
    protected $server;
    
    /**
     * Имя пользователя
     * 
     * @var string
     * @access protected
     */
    protected $user;
    
    /**
     * Пароль пользователя БД
     * 
     * @var string
     * @access protected
     */
    protected $password;
    
    /**
     * База данных, к которой осуществляется подключение
     * 
     * @var string
     * @access protected
     */
    protected $db;
    
    /**
     * Идентификатор соединения с базой
     * 
     * @var resource
     * @access protected
     */
    protected $connection_id;
    
    /**
     * Идентификатор запроса. Применимо только для запросов типа SELECT
     * 
     * @var resource
     * @access protected
     */
    protected $query_id;
    
    /**
     * Текст запроса
     * 
     * @var string
     */
    public $query_text;
    
    /**
     * Текст последней ошибки в результате выполнения запроса
     * 
     * @var string
     * @access protected
     */
    protected $error_text;
    
    /**
     * Номер последней ошибки в результате выполнения запроса
     * 
     * @var int
     * @access protected
     */
    protected $error_num;
    
    /**
     * Порт на который осуществляется соединение. По умолчанию - 3306.
     * 
     * @var int
     * @access protected
     */
    protected $port;
    
    /**
     * Кол-во строк, затронутое результатом запроса INSERT, UPDATE ИЛИ DELETE
     * Если здесь 0, это не обязательно означает ошибку: например запрос мог просто быть
     * составлен таким образом, что изменениям не подлежала ни одна строка
     * 
     * @var int
     * @access protected
     */
    protected $affected_rows;
    
    /**
     * Кол-во строк, полученное в результате запроса SELECT
     * 
     * @var int
     * @access protected
     */
    protected $num_rows;
    
    /*********************************************
     *  GET- и SET-функции для соответствующих свойств класса
     * **************************************************/
    public function getServer():string {
        return $this->server;
    }
    
    public function getNumRows():int {
        return $this->num_rows;
    }
    
    public function getAffectedRows():int {
        return $this->affected_rows;
    }
    
    public function setServer(string $server):void {
        $this->server   =   trim($server);
    }
    
    public function getUser():string {
        return $this->user;
    }
    
    public function setUser(string $user):void {
        $this->user     =   trim($user);
    }
    
    public function getPassword():string {
        return $this->password;
    }
    
    public function setPassword(string $password):void {
        $this->password     =   trim($password);
    }
    
    public function getConnectionID() {
        return $this->connection_id;
    }
    
    public function getQueryID() {
        return $this->query_id;
    }
    
    public function getQueryText() {
        return $this->query_text;
    }
    
     /*********************************************
     *  GET- и SET-функции для соответствующих свойств класса
     * **************************************************/
    
    public function __construct($port = 3306) {
        
        parent::__construct();
        $this->getSQL();
        if (!$this->sql_exists) {
            die('Cannot instantiate class ' .__CLASS__.'due to the lack of DB layers!');
        }
        
        $this->port = $port;
        $this->affected_rows = 0;
    }
    
    public function getPort() {
        return $this->port;
    }
    
    public function setPort(int $port) {
        $this->port     =   $port;
    }
    

    
    abstract protected function setErrorText();
    
    abstract protected function setErrorNumber();
    
    protected function getQueryType() {
        if (empty($this->query_text)) {
            return false;
        }
        
        $string = substr(strtolower($this->query_text), 0, 6);
        switch ($string) {
            case 'select': return 'select'; break;
            case 'update': return 'update'; break;
            case 'delete': return 'delete'; break;
            case 'insert': return 'insert'; break;
            default: return 'unknown'; break;
        }
    }
}

?>