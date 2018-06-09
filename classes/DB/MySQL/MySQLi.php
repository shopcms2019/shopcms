<?php


namespace Shop\DB\MySQL;


class MySQLi extends \Shop\DB\DB {
    
    protected function setErrorText() {
        $this->error_text   =   \mysqli_error($this->connection_id);
    }
    
    protected function setErrorNumber() {
        $this->error_num    =   \mysqli_errno($this->connection_id);
    }
    
    public function getErrorText() {
        return $this->error_text;
    }
    
    public function getErrorNumber() {
        return $this->error_number;
    }
    
    public function __construct($port = 3306){
        parent::__construct($port = 3306);
        $host   =   $this->get_var('server_db_host');
        $user   =   $this->get_var('server_db_user');
        $pass   =   $this->get_var('server_db_pass');
        $db     =   $this->get_var('server_db_name');
        
        if ($this->port != 3306) {
            $host = $host.":".$port;
        }
        
        
        $this->connection_id    =   \mysqli_connect($host, $user, $pass);
        $this->setErrorText();
        $this->setErrorNumber();
        
        if (empty($this->getErrorText())) {
            \mysqli_select_db($this->connection_id, 'shop');
            $this->query_text = 'SET CHARACTER SET utf8';
            $this->query();
            return $this->connection_id;
        }
        
        else {
            return false;
        }
    }
    
    
    
    public function query() {
       if (empty($this->query_text)) {
           die('Empty query');
           return;
       }
       
       $query_type = $this->getQueryType();
       
       $this->query_id  =   \mysqli_query($this->connection_id, $this->query_text);
       $this->setErrorText();
       $this->setErrorNumber();
       
      if (empty($this->getErrorText())) {
          if ($query_type == 'select') {
              $this->num_rows   =   \mysqli_num_rows($this->query_id);
              return $this->query_id;
          }
          
          if (in_array($query_type, ["update", "insert", "delete"])) {
              $this->affected_rows  = \mysqli_affected_rows();
          }
      }
      
      else {
          return $this->getErrorText();
      }
   }
   
   public function fetch(string $fetch_type) {
       
       
       switch ($fetch_type) {
           case 'array': return \mysqli_fetch_array($this->query_id, MYSQLI_BOTH); break;
           case 'object': return \mysqli_fetch_object($this->query_id); break;
           default: return false;
       }
   }
   
   public function escape(string $string) {
       return mysqli_real_escape_string($this->connection_id, $string);
   }
    
    
}

?>
