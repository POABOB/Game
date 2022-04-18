<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class playerModel extends model {
    
    public function get_player($para = array('player_id', 'name', 'unit', 'comment'), $where = array(), $table = 'Player') {
        $where = array('hidden' => '0', 'ORDER' => array('player_id' => 'DESC'));
        return $this->select($table, $para, $where);
    }

    public function insert_player($para = array(), $table = 'Player') {
        return $this->insert($table,$para);
    }

    public function update_player($para = array(), $where = array(), $table = 'Player') {
        $this->update($table, $para, $where);
        $err = $this->error;
        if($err == null) {
            return 1;
        } else {
            return 0;
        }
    }
}
