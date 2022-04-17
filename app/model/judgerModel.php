<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class judgerModel extends model {
    public function get_judger($where = array(), $para = array('judger_id', 'name', 'ID', 'phone', 'right'), $table = 'Judger') {
        $where['hidden'] = '0';
        return $this->select($table, $para, $where);
    }

    public function insert_judger($para = array(), $table = 'Judger') {
        return $this->insert($table,$para);
    }

    public function update_judger($para = array(), $where = array(), $table = 'Judger') {
        $this->update($table, $para, $where);
        $err = $this->error;
        if($err == null) {
            return 1;
        } else {
            return 0;
        }
    }
}
