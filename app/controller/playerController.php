<?php
namespace app\controller;
if ( ! defined('PPP')) exit('非法入口');
use app\model\playerModel;
use core\lib\resModel;
use core\lib\Validator;
use core\lib\JWT;
/**
 * 處理選手api
 *
 */
class playerController extends \core\PPP {
        /**
     * @OA\Get(
     *     path="/api/admin/player", 
     *     tags={"後台比賽管理"},
     *     summary="後台獲取選手",
     *     security={{"Authorization":{}}}, 
     *      @OA\Response(
     *          response="200", 
     *          description="獲取選手",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="player_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="王小明"),
     *                      @OA\Property(property="unit", type="string(1)", example="台中一中(單位)"),
     *                      @OA\Property(property="comment", type="string(256)", example="詳細資訊"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     * )
     */
    public function index_() {
        $database = new playerModel();
        $data = $database->get_player();
        json(new resModel(200, $data));
    }

    /**
     * @OA\Post(
     *     path="/api/admin/player", 
     *     tags={"後台比賽管理"},
     *     summary="後台新增選手",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"name", "unit", "comment"},
     *                      @OA\Property(property="name", type="string(128)", example="王小明"),
     *                      @OA\Property(property="unit", type="string(1)", example="台中一中(單位)"),
     *                      @OA\Property(property="comment", type="string(256)", example="詳細資訊"),
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
            array(
                '圖片1' => $post['img'],
                '圖片2' => $post['img2'],
                '圖片(mobile)' => $post['small_img'],
                '狀態' => $post['status']
            ),
            array(
                '圖片1' => array('required', 'maxLen' => 256),
                '圖片2' => array('maxLen' => 256),
                '圖片(mobile)' => array('maxLen' => 256),
                '狀態' => array('required', 'maxLen' => 9)
            )
        );

        if($v->error()) {
            json(new resModel(400, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new apiModel();
        $data = $database->insertOrUpdate_index(
            array(
                'img' => $post['img'],
                'img2' => $post['img2'],
                'small_img' => $post['small_img'],
                'status' => $post['status']
            )
        );

        if($data) {
            json(new resModel(200, '圖片新增成功'));
        } else {
            json(new resModel(400, '圖片新增失敗'));
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/player", 
     *     tags={"後台比賽管理"},
     *     summary="後台更新選手",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"player_id", "name", "unit", "comment"},
     *                      @OA\Property(property="player_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="王小明"),
     *                      @OA\Property(property="unit", type="string(1)", example="台中一中(單位)"),
     *                      @OA\Property(property="comment", type="string(256)", example="詳細資訊"),
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
            array(
                'ID' => $post['id'], 
                '圖片1' => $post['img'],
                '圖片2' => $post['img2'],
                '圖片(mobile)' => $post['small_img'],
                '狀態' => $post['status']
            ),
            array(
                'ID' => array('required', 'maxLen' => 11),
                '圖片1' => array('required', 'maxLen' => 256),
                '圖片2' => array('maxLen' => 256),
                '圖片(mobile)' => array('maxLen' => 256),
                '狀態' => array('required', 'maxLen' => 9)
            )
        );

        if($v->error()) {
            json(new resModel(400, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new apiModel();
        $return = $database->insertOrUpdate_index(
            array(
                'img' => $post['img'],
                'img2' => $post['img2'],
                'small_img' => $post['small_img'],
                'status' => $post['status']
            ),
            array('id' => $post['id'])
        );

        if($return == 2) {
            json(new resModel(200, '圖片更新成功'));
        } else if($return == 1) {
            json(new resModel(400, '圖片更新失敗'));
        } else {
            json(new resModel(400, '圖片ID異常'));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/player", 
     *     tags={"後台比賽管理"},
     *     summary="後台刪除選手",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"player_id"},
     *                  @OA\Property(property="player_id", type="int(11)", example="1"),
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