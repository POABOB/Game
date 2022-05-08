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
     *     tags={"後台選手管理"},
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
     *     tags={"後台選手管理"},
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
                '選手姓名' => $post['name'],
                '選手單位' => $post['unit'],
                '選手備註' => $post['comment']
            ),
            array(
                '選手姓名' => array('required', 'maxLen' => 128),
                '選手單位' => array('required', 'maxLen' => 128),
                '選手備註' => array( 'maxLen' => 128),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new playerModel();
        $data = $database->insert_player(
            array(
                'name' => $post['name'],
                'unit' => $post['unit'],
                'comment' => $post['comment']
            )
        );


        if($data) {
            json(new resModel(200, '新增成功'));
        } else {
            json(new resModel(400, '新增失敗，選手名字和單位相同'));
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/player", 
     *     tags={"後台選手管理"},
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
                '選手編號' => $post['player_id'],
                '選手姓名' => $post['name'],
                '選手單位' => $post['unit'],
                '選手備註' => $post['comment']
            ),
            array(
                '選手編號' => array('required', 'maxLen' => 11),
                '選手姓名' => array('required', 'maxLen' => 128),
                '選手單位' => array('required', 'maxLen' => 128),
                '選手備註' => array( 'maxLen' => 128),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new playerModel();
        $return = $database->update_player(
            array(
                'name' => $post['name'],
                'unit' => $post['unit'],
                'comment' => $post['comment']
            ),
            array('player_id' => $post['player_id'])
        );


        if($return == 1) {
            json(new resModel(200, '更新成功'));
        } else if($return == 0) {
            json(new resModel(400, '更新失敗'));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/player", 
     *     tags={"後台選手管理"},
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
            array('選手編號' => $post['player_id']),
            array('選手編號' => array('required', 'maxLen' => 11))
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new playerModel();
        $return = $database->update_player(
            array('hidden' => '1'),
            array('player_id' => $post['player_id'])
        );


        if($return == 1) {
            json(new resModel(200, '刪除成功'));
        } else if($return == 0) {
            json(new resModel(400, '刪除失敗'));
        }
    }
}