<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class viewsModel extends model {

    public function find($para = array(), $where = array(), $table = 'Position') {
        return $this->select($table,$para,$where);
    }
    
    public function insertOrUpdate($para = array(), $where = array(), $table = 'new_Shift_table') {
        if($this->has($table, $where)){
            return $this->update($table, $para, $where);
        } else {
            return $this->insert($table,$para);
        }
    }

    public function update($para, $where, $table = 'Check_img') {
        return $this->update($table, $para, $where);
    }

    public function delete($para, $table = 'Check_img') {
        return $this->delete($table, $para);
    }
}
