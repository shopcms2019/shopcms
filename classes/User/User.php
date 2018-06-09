<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shop\User;

require "/includes/functions_user.php";
require "/includes/functions_strings.php";

/**
 * Description of User
 *
 * @author kis
 */
class User extends \Shop\Base {
    
    const USER_LOGIN_LENGTH     =   20;
    const USER_EMAIL_LENGTH     =   60;
    
    /**
     * ID пользователя
     * 
     * @var int
     * @access protected
     */
    protected $user_id;
    
    /**
     * Логин
     * 
     * @var string
     * @access protected
     */
    protected $login;
    
    /**
     * Данные пользователя
     * 
     * @var array
     * @access protected
     */
    protected $data;
    
    public function __construct(int $id) {
        if (user_exists($id)) {
            $this->user_id  =   $id;
        }
        
        else {
            $this->user_id  =   -1;
        }
    }
    
    /**
     * Проверяет, заблокирован ли пользователь. Возращает true, если пользователь заблокирован,
     * false - в противном случае
     * 
     * @global type $sql
     * @return bool
     */
    protected function user_is_blocked():bool {
        global $sql;
        $sql->query_text = "SELECT user_is_blocked FROM users WHERE user_id = ".$this->user_id;
        $sql->query();
        $result = $sql->fetch('array');
        if ($result['user_is_blocked'] == 0) {
            return false;
        }
        
        else {
            return true;
        }
    }
    
    /**
     * Блокирует пользователя. Блокировка происходит только в том случае,
     * если данный пользователь предварительно не был ещё заблокирован. В 
     * противном случае возвращается false.
     * 
     * Если пользователь не заблокирован, возвращается true, если блокировка удалась,
     * false - в противном случае.
     * 
     * @global \Shop\User\type $sql
     * @return bool
     */
    public function block_user():bool {
        global $sql;
        
        if (!$this->user_is_blocked()) {
            $sql->query_text = "UPDATE users"
                . "SET user_is_blocked = true"
                . "WHERE user_id = ".$this->user_id;
        
             $sql->query();
             $affected_rows = $this->getAffectedRows();
            if ($rows > 0) {
                return true;
            }
            else {
                return false;
            }
        } 
        else {
            return false;
        }
        
    }
    
    public function get_user_email() {
        global $sql;
        
        if (empty($this->data['user_email'])) {
            $sql->query_text = "SELECT user_email FROM users"
                    . "WHERE user_id = ".$this->user_id;

            $sql->query();
            $result = $sql->fetch('array');

            $this->data['user_email']   =   $result['user_email'];
            return $this->data['user_email'];
        }
        
        else {
            return $this->data['user_email'];
        }
       
    }
}
