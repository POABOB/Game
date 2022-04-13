<?php
namespace app\controller;
if ( ! defined('PPP')) exit('非法入口');
use app\model\apiModel;
use core\lib\CacheHTML;
/**
 * 處理視圖
 *
 */
class viewsController extends \core\PPP {
	//GET 首頁視圖
	public function index() {
        // $this->assign('data', $data);
        $this->display('index.html');
	}

    //GET 後台視圖
	public function admin() {
        // $this->assign('data', $data);
        $this->display('admin/index.html');
	}

    //GET DOC視圖
	public function doc() {
        $this->display('dist/index.html');
	}
}