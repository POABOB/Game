<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class loginModel extends model {

    public function admin_login($where = array(), $para = array('judger_id', 'name', 'right'), $table = 'Judger') {
        return $this->get($table,$para,$where);
    }
    
    public function login($where = array(), $para = array('judger_id', 'name', 'right'), $table = 'Judger') {
        return $this->get($table,$para,$where);
    }
}
