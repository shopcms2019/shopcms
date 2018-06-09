<?php


namespace Shop\DB\MySQL;


class MySQL extends \Shop\DB\DB {
    
    protected function setErrorText() {
        $this->error_text   =   mysql_error();
    }
    
    protected function setErrorNumber() {
        $this->error_num    =   mysql_errno();
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
        
        
        $this->connection_id    =   \mysql_connect($host, $user, $pass);
        $this->setErrorText();
        $this->setErrorNumber();
        
        if (empty($this->getErrorText())) {
            \mysql_select_db('shop');
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
       
       $this->query_id  =   @mysql_query($this->query_text);
       $this->setErrorText();
       $this->setErrorNumber();
       
      if (empty($this->getErrorText())) {
          if ($query_type == 'select') {
              $this->num_rows   =   \mysql_num_rows($this->query_id);
              return $this->query_id;
          }
          
          if (in_array($query_type, ["update", "insert", "delete"])) {
              $this->affected_rows  = \mysql_affected_rows();
          }
      }
      
      else {
          return $this->getErrorText();
      }
   }
   
   public function fetch(string $fetch_type) {
       if (!is_resource($this->query_id)) {
           return false;
       }
       
       switch ($fetch_type) {
           case 'array': return \mysql_fetch_array($this->query_id); break;
           case 'object': return \mysql_fetch_object($this->query_id); break;
           default: return false;
       }
   }
    
    
}

?>
