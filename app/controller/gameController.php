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
     *     tags={"後台比賽管理"},
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
        $database = new gameModel();
        $data = $database->get_game();
        json(new resModel(200, $data));
    }

    public function get_player() {
        $database = new gameModel();
        $data = $database->get_player();
        json(new resModel(200, $data));
    }

    public function get_judger() {
        $database = new gameModel();
        $data = $database->get_judger();
        json(new resModel(200, $data));
    }

    public function index_history() {
        $database = new gameModel();
        $data = $database->get_game_history();
        json(new resModel(200, $data));
    }

    /**
     * @OA\Post(
     *     path="/api/admin/game", 
     *     tags={"後台比賽管理"},
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
            array(
                '比賽名稱' => $post['name'],
                '比賽類型' => $post['type'],
                '比賽內容' => $post['content'],
                '比賽日期' => $post['date'],
            ),
            array(
                '比賽名稱' => array('required', 'maxLen' => 128),
                '比賽類型' => array('required', 'maxLen' => 1),
                '比賽內容' => array('maxLen' => 256),
                '比賽日期' => array('required', 'maxLen' => 10),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $data = $database->insert_game(
            array(
                'name' => $post['name'],
                'type' => (string)$post['type'],
                'content' => $post['content'],
                'date' => $post['date'],
            )
        );

        if($data) {
            json(new resModel(200, '新增成功'));
        } else {
            json(new resModel(400, '新增失敗'));
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/game", 
     *     tags={"後台比賽管理"},
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
            array(
                '比賽編號' => $post['game_id'],
                '比賽名稱' => $post['name'],
                '比賽類型' => $post['type'],
                '比賽內容' => $post['content'],
                '比賽日期' => $post['date'],
            ),
            array(
                '比賽編號' => array('required', 'maxLen' => 11),
                '比賽名稱' => array('required', 'maxLen' => 128),
                '比賽類型' => array('required', 'maxLen' => 1),
                '比賽內容' => array('maxLen' => 256),
                '比賽日期' => array('required', 'maxLen' => 10),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $return = $database->update_game(
            array(
                'name' => $post['name'],
                'type' => (string)$post['type'],
                'content' => $post['content'],
                'date' => $post['date'],
            ),
            array('game_id' => $post['game_id'])
        );


        if($return == 1) {
            json(new resModel(200, '更新成功'));
        } else if($return == 0) {
            json(new resModel(400, '更新失敗'));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/game", 
     *     tags={"後台比賽管理"},
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
            array('比賽編號' => $post['game_id']),
            array('比賽編號' => array('required', 'maxLen' => 11))
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $return = $database->update_game(
            array('hidden' => '1'),
            array('game_id' => $post['game_id'])
        );

        $database->update_rank_status(array('game_id' => $post['game_id']));


        if($return == 1) {
            json(new resModel(200, '刪除成功'));
        } else if($return == 0) {
            json(new resModel(400, '刪除失敗'));
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/game/player", 
     *     tags={"後台比賽管理"},
     *     summary="後台新增比賽選手",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id", "player_id"},
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="player_id", type="int(11)", example="1"),
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
    public function insert_player() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array(
                '比賽編號' => $post['game_id'],
                '選手編號' => $post['player_id'],
            ),
            array(
                '比賽編號' => array('required', 'maxLen' => 11),
                '選手編號' => array('required', 'maxLen' => 11),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $return = $database->insert_game_player(
            array(
                'game_id' => $post['game_id'],
                'player_id' => $post['player_id']
            )
        );


        if($return == 2) {
            json(new resModel(400, '比賽或選手不存在!'));
        } else if($return == 1) {
            json(new resModel(400, '請勿重複插入!'));
        } else {
            json(new resModel(200, '新增成功'));
        }
    }
    
        /**
     * @OA\Post(
     *     path="/api/admin/game/judger", 
     *     tags={"後台比賽管理"},
     *     summary="後台新增比賽裁判",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id", "judger_id"},
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="judger_id", type="int(11)", example="1"),
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
    public function insert_judger() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array(
                '比賽編號' => $post['game_id'],
                '裁判編號' => $post['judger_id'],
            ),
            array(
                '比賽編號' => array('required', 'maxLen' => 11),
                '裁判編號' => array('required', 'maxLen' => 11),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $return = $database->insert_game_judger(
            array(
                'game_id' => $post['game_id'],
                'judger_id' => $post['judger_id']
            )
        );


        if($return == 2) {
            json(new resModel(400, '比賽或裁判不存在!'));
        } else if($return == 1) {
            json(new resModel(400, '請勿重複插入!'));
        } else {
            json(new resModel(200, '新增成功'));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/game/player", 
     *     tags={"後台比賽管理"},
     *     summary="後台刪除比賽選手",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id", "player_id"},
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
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
    public function delete_player() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array(
                '比賽編號' => $post['game_id'],
                '選手編號' => $post['player_id'],
            ),
            array(
                '比賽編號' => array('required', 'maxLen' => 11),
                '選手編號' => array('required', 'maxLen' => 11),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $return = $database->delete_game_player(
            array(
                'game_id' => $post['game_id'],
                'player_id' => $post['player_id']
            )
        );


        if($return == 0) {
            json(new resModel(200, '刪除成功'));
        } else {
            json(new resModel(400, '刪除失敗'));
        }
    }
    
    /**
     * @OA\Delete(
     *     path="/api/admin/game/judger", 
     *     tags={"後台比賽管理"},
     *     summary="後台刪除比賽裁判",
     *     security={{"Authorization":{}}}, 
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id", "judger_id"},
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="judger_id", type="int(11)", example="1"),
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
    public function delete_judger() {
        $post = array();
        $post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array(
                '比賽編號' => $post['game_id'],
                '裁判編號' => $post['judger_id'],
            ),
            array(
                '比賽編號' => array('required', 'maxLen' => 11),
                '裁判編號' => array('required', 'maxLen' => 11),
            )
        );

        if($v->error()) {
            json(new resModel(401, $v->error(), '提交格式有誤'));
            return;
        }

        $database = new gameModel();
        $return = $database->delete_game_judger(
            array(
                'game_id' => $post['game_id'],
                'judger_id' => $post['judger_id']
            )
        );

        json(new resModel(200, '刪除成功'));
    }
}