<?php
namespace core\common;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\resModel;
use core\lib\JWT;

class auth {
    //工廠方法
    public static function factory() { 
        return new self; 
    } 

	public function admin($msg = 'Permission denied') {
        $token = JWT::getHeaders();
        $payload = JWT::verifyToken($token);
        if($payload == false || intval($payload['right']) !== 2) {
            json(new resModel(403, $msg));
            exit();
        }
    }

    public function user1($msg = 'Permission denied') {
        $token = JWT::getHeaders();
        $payload = JWT::verifyToken($token);
        // p($payload == false, $_SESSION['user'] == false, $payload['right'] < 1);
        if($payload == false || intval($payload['right']) < 1) {
            json(new resModel(403, $msg));
            exit();
        }
    }

    public function user0($msg = 'Permission denied') {
        $token = JWT::getHeaders();
        $payload = JWT::verifyToken($token);
        if($payload == false || intval($payload['right']) > 0) {
            json(new resModel(403, $msg));
            exit();
        }
    }

    public function user_info($msg = 'Permission denied') {
        $token = JWT::getHeaders();
        $payload = JWT::verifyToken($token);
        if($payload == false || intval($payload['right']) !== 2) {
            json(new resModel(403, $msg));
            exit();
        } else {
            json(new resModel(200, $payload));
            exit();
        }
    }
}