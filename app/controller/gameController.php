<?php
namespace app\controller;
if ( ! defined('PPP')) exit('非法入口');
use app\model\gameModel;
use core\lib\resModel;
use core\lib\Validator;
use core\lib\JWT;
/**
 * 處理比賽api
 *
 */
class gameController extends \core\PPP {
    /**
     * @OA\Get(
     *     path="/api/admin/game", 
     *     tags={"後台選手管理"},
     *     summary="後台獲取比賽",
     *     security={{"Authorization":{}}}, 
     *      @OA\Response(
     *          response="200", 
     *          description="獲取比賽",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="2 || 7"),
     *                      @OA\Property(property="content", type="string(256)", example="詳細資訊"),
     *                      @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     * )
     */
    public function index_() {
        $database = new apiModel();
        $data[0] = $database->index();
        $data[1] = array();
        foreach ($data[0] as $key => $value) {
            if($data[0][$key]['img'] != "") {
                $data[1][] = array(
                    'name' => $data[0][$key]['img'],
                    'path' => $data[0][$key]['img']
                );
            }
            
            if($data[0][$key]['img2'] != "") {
                $data[1][] = array(
                    'name' => $data[0][$key]['img2'],
                    'path' => $data[0][$key]['img2']
                );
            }

            if($data[0][$key]['small_img'] != "") {
                $data[1][] = array(
                    'name' => $data[0][$key]['small_img'],
                    'path' => $data[0][$key]['small_img']
                );
            }
        }
        if(count($data[1]) == 0) {
            $data[1][0] = [];
        }
        json(new resModel(200, $data));
    }

    /**
     * @OA\Post(
     *     path="/api/admin/game", 
     *     tags={"後台選手管理"},
     *     summary="後台新增比賽",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"name", "type", "content", "date"},
     *                  @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                  @OA\Property(property="type", type="string(1)", example="2 || 7"),
     *                  @OA\Property(property="content", type="string(256)", example="詳細資訊"),
     *                  @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="新增成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="新增成功"),
     *              @OA\Property(property="data", example="null"),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="新增失敗"),
     *      @OA\Response(response="401", description="提交格式有誤")
     * )
     */
    public function insert_() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array('ID' => $post['id']),
            array('ID' => array('required', 'maxLen' => 11))
        );

        if($v->error()) {
            json(new resModel(400, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new apiModel();
        $this->_removeFile($post['img']);
        $this->_removeFile($post['img2']);
        $this->_removeFile($post['small_img']);
        $data = $database->delete_index(array('id' => $post['id'], 'orders' => intval($post['orders'])));

        if($data) {
            json(new resModel(200, '圖片刪除成功'));
        } else {
            json(new resModel(400, '圖片刪除失敗'));
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/game", 
     *     tags={"後台選手管理"},
     *     summary="後台更新比賽",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id", "name", "type", "content", "date"},
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                  @OA\Property(property="type", type="string(1)", example="2 || 7"),
     *                  @OA\Property(property="content", type="string(256)", example="詳細資訊"),
     *                  @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="更新成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="更新成功"),
     *              @OA\Property(property="data", example="null"),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="更新失敗"),
     *      @OA\Response(response="401", description="提交格式有誤")
     * )
     */
    public function update_() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array('ID' => $post['id']),
            array('ID' => array('required', 'maxLen' => 11))
        );

        if($v->error()) {
            json(new resModel(400, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new apiModel();
        $this->_removeFile($post['img']);
        $this->_removeFile($post['img2']);
        $this->_removeFile($post['small_img']);
        $data = $database->delete_index(array('id' => $post['id'], 'orders' => intval($post['orders'])));

        if($data) {
            json(new resModel(200, '圖片刪除成功'));
        } else {
            json(new resModel(400, '圖片刪除失敗'));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/game", 
     *     tags={"後台選手管理"},
     *     summary="後台刪除比賽",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id"},
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="刪除成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="刪除成功"),
     *              @OA\Property(property="data", example="null"),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="刪除失敗"),
     *      @OA\Response(response="401", description="提交格式有誤")
     * )
     */
    public function delete_() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array('ID' => $post['id']),
            array('ID' => array('required', 'maxLen' => 11))
        );

        if($v->error()) {
            json(new resModel(400, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new apiModel();
        $this->_removeFile($post['img']);
        $this->_removeFile($post['img2']);
        $this->_removeFile($post['small_img']);
        $data = $database->delete_index(array('id' => $post['id'], 'orders' => intval($post['orders'])));

        if($data) {
            json(new resModel(200, '圖片刪除成功'));
        } else {
            json(new resModel(400, '圖片刪除失敗'));
        }
    }
}