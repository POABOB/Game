<?php
namespace app\controller;
if ( ! defined('PPP')) exit('非法入口');
use app\model\frontModel;
use core\lib\resModel;
use core\lib\Validator;
use core\lib\JWT;
/**
 * 處理比賽api
 *
 */
class frontController extends \core\PPP {
          /**
     * @OA\Get(
     *     path="/api_test/f/rank/{game_id}", 
     *     tags={"前台"},
     *     summary="查看比賽歷史排行",
     *     @OA\Parameter(
     *          name="game_id",
     *          description="比賽id",
     *          in = "path",
     *          required=true,
     *          @OA\Schema(type="integer") 
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="查看比賽歷史排行",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="7"),
     *                      @OA\Property(property="rank", type="array",
     *                          @OA\Items(type="object",
     *                              @OA\Property(property="player_id", type="int(11)", example="1"),
     *                              @OA\Property(property="name", type="string(128)", example="王小明"),
     *                              @OA\Property(property="score", type="int", example="[7,6,5,2,1,8,8]"),
     *                              @OA\Property(property="totalScore", type="tinyint(3)", example="28"),
     *                              @OA\Property(property="unit", type="string(128)", example="台中一中"),
     *                          )
     *                      ),
     *                      @OA\Property(property="last_rank", type="object",
     *                          @OA\Property(property="player_id", type="int(11)", example="1"),
     *                          @OA\Property(property="name", type="string(128)", example="王小明"),
     *                          @OA\Property(property="score", type="int", example="[7,6,5,2,1,8,8]"),
     *                          @OA\Property(property="totalScore", type="tinyint(3)", example="28"),
     *                          @OA\Property(property="unit", type="string(128)", example="台中一中"),
     *                          @OA\Property(property="last_updated", type="string(20)", example="2022-05-26 17:55:00"),
     *                      ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied"),
     * )
     */
    public function get_rank($game_id) {
        if($game_id > 0) {
          $database = new frontModel();
          $data = $database->get_rank_list(array('game_id' => $game_id));

          json(new resModel(200, $data));
      } else {
          json(new resModel(400, '比賽編號不符合規則!'));
      }

    }
    /**
     * @OA\Get(
     *     path="/api_test/f/rank/list", 
     *     tags={"前台"},
     *     summary="前台獲取排名顯示螢幕",
     *      @OA\Response(
     *          response="200", 
     *          description="獲取排名",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="7"),
     *                      @OA\Property(property="rank", type="array",
     *                          @OA\Items(type="object",
     *                              @OA\Property(property="player_id", type="int(11)", example="1"),
     *                              @OA\Property(property="name", type="string(128)", example="王小明"),
     *                              @OA\Property(property="score", type="int", example="[7,6,5,2,1,8,8]"),
     *                              @OA\Property(property="totalScore", type="tinyint(3)", example="28"),
     *                              @OA\Property(property="unit", type="string(128)", example="台中一中"),
     *                          )
     *                      ),
     *                      @OA\Property(property="last_rank", type="object",
     *                          @OA\Property(property="player_id", type="int(11)", example="1"),
     *                          @OA\Property(property="name", type="string(128)", example="王小明"),
     *                          @OA\Property(property="score", type="int", example="[7,6,5,2,1,8,8]"),
     *                          @OA\Property(property="totalScore", type="tinyint(3)", example="28"),
     *                          @OA\Property(property="unit", type="string(128)", example="台中一中"),
     *                          @OA\Property(property="last_updated", type="string(20)", example="2022-05-26 17:55:00"),
     *                      ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied"),
     * )
     */
    public function rank_list() {
            $database = new frontModel();
            $data = $database->get_rank_list();

            json(new resModel(200, $data));

            // $data = '{
            //       "game_id": 1,
            //       "name": "滑輪板街式賽_男子選手組",
            //       "type": 7,
            //       "rank": [
            //         {"player_id": 1,"name": "王小明","score": [7, 6, 5, 2, 1, 8, 8],"totalScore": 28},
            //         {"player_id": 2,"name": "陳小明","score": [9, 6, 5, 2, 1, 8, 8],"totalScore": 30},
            //         {"player_id": 3,"name": "李小明","score": [7, 6, 5, 2, 8, 8, 8],"totalScore": 31},
            //         {"player_id": 4,"name": "謝小明","score": [7, 6, 5, 2, 9, 8, 8],"totalScore": 32},
            //         {"player_id": 5,"name": "林小明","score": [7, 6, 5, 2, 10, 8, 8],"totalScore": 33},
            //         {"player_id": 6,"name": "魏小明","score": [7, 6, 5, 2, 10, 10, 8],"totalScore": 35},
            //         {"player_id": 7,"name": "蕭小明","score": [7, 6, 5, 2, 5, 5, 5],"totalScore": 22},
            //         {"player_id": 8,"name": "黎小明","score": [7, 6, 5, 2, 6, 6, 6],"totalScore": 25},
            //         {"player_id": 9,"name": "邱小明","score": [7, 6, 5, 2, 10, 10, 10],"totalScore": 37}
            //       ]
            //     }';
            // json(new resModel(200, json_decode($data)));
        }

    /**
     * @OA\Get(
     *     path="/api_test/game/list/{judger_id}", 
     *     tags={"前台"},
     *     summary="裁判獲取比賽列表",
     *     security={{"Authorization":{}}}, 
     *     @OA\Parameter(
     *          name="judger_id",
     *          description="裁判id",
     *          in = "path",
     *          required=true,
     *          @OA\Schema(type="integer") 
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="獲取比賽列表",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="7"),
     *                      @OA\Property(property="content", type="string(1)", example="成人組比賽"),
     *                      @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied"),
     * )
     */
    public function game_list() {
      // 獲取judger_id
      $token = JWT::getHeaders();
      $payload = JWT::verifyToken($token);
      $judger_id = $payload['judger_id'];

      $database = new frontModel();
      $data = $database->get_game_list(array('judger_id' => $judger_id));

      json(new resModel(200, $data));

        // $data = '[
        //     {"game_id": 1,"name": "滑輪板街式賽_男子選手組","type": 7,"content": "成人組比賽","date": "2022-05-05"},
        //     {"game_id": 2,"name": "滑輪板街式賽_女子選手組","type": 7,"content": "成人組比賽","date": "2022-05-05"},
        //     {"game_id": 3,"name": "滑輪板街式賽_高中選手組","type": 2,"content": "學生組比賽","date": "2022-05-06"}
        //   ]';
        // json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Get(
     *     path="/api_test/game/detail/{game_id}", 
     *     tags={"前台"},
     *     summary="裁判獲取比賽詳細資訊(選手、已評分資料)(獲取的scores以judger_id做分類)",
     *     security={{"Authorization":{}}}, 
     *     @OA\Parameter(
     *          name="game_id",
     *          description="比賽 id",
     *          in = "path",
     *          required=true,
     *          @OA\Schema(type="integer") 
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="獲取比賽詳細資訊(選手、已評分資料)",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="7"),
     *                      @OA\Property(property="content", type="string(1)", example="成人組比賽"),
     *                      @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                      @OA\Property(property="players", type="array",
     *                          @OA\Items(type="object",
     *                              @OA\Property(property="player_id", type="int(11)", example="1"),
     *                              @OA\Property(property="name", type="string(128)", example="王小明"),
     *                              @OA\Property(property="unit", type="string(1)", example="國立台中一中"),
     *                              @OA\Property(property="comment", type="string(1)", example="詳細備註"),
     *                              @OA\Property(property="scores", type="array",
     *                                  @OA\Items(type="object",
     *                                      @OA\Property(property="score_id", type="int(11)", example="1"),
     *                                      @OA\Property(property="score", type="float(2,2)", example="8.5"),
     *                                      @OA\Property(property="round", type="string(1)", example="1~7(第幾輪評分)"),
     *                                  )
     *                              ),
     *                          )
     *                      ),

     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function game_detail($game_id) {
        if($game_id > 0) {
            // 獲取judger_id
            $token = JWT::getHeaders();
            $payload = JWT::verifyToken($token);
            $judger_id = $payload['judger_id'];

            $database = new frontModel();
            $data = $database->get_game_detail(
                array('game_id' => $game_id, 'judger_id' => $judger_id)
            );

            json(new resModel(200, $data));
        } else {
            json(new resModel(400, '比賽編號不符合規則!'));
        }
        // $data = '[
        //     {
        //       "game_id": 1,
        //       "name": "滑輪板街式賽_男子選手組",
        //       "type": 7,
        //       "content": "成人組比賽",
        //       "date": "2022-05-05",
        //       "players": [
        //         {
        //           "player_id": 1,
        //           "name": "王小明",
        //           "unit": "國立台中一中",
        //           "comment": "詳細備註",
        //           "scores": [
        //             {"score_id": 1,"score": 8.5,"round": "1"},
        //             {"score_id": 2,"score": 1.5,"round": "2"},
        //             {"score_id": 0,"score": -1,"round": "3"},
        //             {"score_id": 0,"score": -1,"round": "4"},
        //             {"score_id": 0,"score": -1,"round": "5"},
        //             {"score_id": 0,"score": -1,"round": "6"},
        //             {"score_id": 0,"score": -1,"round": "7"}
        //           ]
        //         },
        //         {
        //           "player_id": 2,
        //           "name": "陳小明",
        //           "unit": "國立台中二中",
        //           "comment": "詳細備註",
        //           "scores": [
        //             {"score_id": 3,"score": 9.5,"round": "1"},
        //             {"score_id": 4,"score": 5.5,"round": "2"},
        //             {"score_id": 0,"score": -1,"round": "3"},
        //             {"score_id": 0,"score": -1,"round": "4"},
        //             {"score_id": 0,"score": -1,"round": "5"},
        //             {"score_id": 0,"score": -1,"round": "6"},
        //             {"score_id": 0,"score": -1,"round": "7"}
        //           ]
        //         }
        //       ]
        //     }
        //   ]';
        // json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Post(
     *     path="/api_test/game/insert/score", 
     *     tags={"前台"},
     *     summary="裁判評分選手",
     *     security={{"Authorization":{}}}, 
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"player_id", "game_id", "score", "round"},
     *                  @OA\Property(property="player_id", type="int(11)", example="1"),
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="score", type="float(2,2)", example="8.5"),
     *                  @OA\Property(property="round", type="string(1)", example="1 ~ 7"),
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
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function game_insert_score() {
      // 獲取judger_id
      $token = JWT::getHeaders();
      $payload = JWT::verifyToken($token);
      $judger_id = $payload['judger_id'];
      $judger_name = $payload['name'];
       
      $post = array();
      $post = post_json();
      $post['judger_id'] = $judger_id;
      $post['judger_name'] = $judger_name;
      $post['round'] = (string)$post['round'];

      //Validation
      $v = new Validator();
      $v->validate(
          array(
            '比賽編號' => $post['game_id'],
            '選手編號' => $post['player_id'],
            '分數' => $post['score'],
            'ROUND' => $post['round']
          ),
          array(
            '比賽編號' => array('required', 'maxLen' => 11),
            '選手編號' => array('required', 'maxLen' => 11),
            '分數' => array('required', 'maxLen' => 10),
            'ROUND' => array('required', 'maxLen' => 1)
          )
      );


      if($v->error()) {
          json(new resModel(401, $v->error(), '提交格式有誤'));
          return;
      }

      $database = new frontModel();
      $data = $database->insert_score($post);
      if($data == 2) {
        json(new resModel(400, "新增失敗，該輪分數已存在!"));
      } else if($data == 1) {
        json(new resModel(400, "新增失敗，請勿跳輪評分!"));
      } else {
        json(new resModel(200, "新增成功"));
      }

      // json(new resModel(200, "新增成功"));
    }

    /**
     * @OA\Get(
     *     path="/api_test/score/game/list", 
     *     tags={"前台"},
     *     summary="裁判長獲取比賽列表",
     *     security={{"Authorization":{}}}, 
     *      @OA\Response(
     *          response="200", 
     *          description="獲取比賽列表",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="7"),
     *                      @OA\Property(property="content", type="string(1)", example="成人組比賽"),
     *                      @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function score_game_list() {
      $database = new frontModel();
      $data = $database->score_game_list();

      json(new resModel(200, $data));
    }

    /**
     * @OA\Get(
     *     path="/api_test/score/list/{game_id}/round/{round}", 
     *     tags={"前台"},
     *     summary="裁判長獲取比賽評分資料(獲取的scores以round做分類)",
     *     security={{"Authorization":{}}}, 
     *     @OA\Parameter(
     *          name="game_id",
     *          description="比賽id",
     *          in = "path",
     *          required=true,
     *          @OA\Schema(type="integer") 
     *      ),
     *      @OA\Parameter(
     *          name="round",
     *          description="第N輪",
     *          in = "path",
     *          required=true,
     *          @OA\Schema(type="integer") 
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="裁判長獲取比賽評分資料",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                  @OA\Property(property="type", type="string(1)", example="7"),
     *                  @OA\Property(property="content", type="string(1)", example="成人組比賽"),
     *                  @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                  @OA\Property(property="round", type="string(1)", example="1~7(第幾輪評分)"),
     *                  @OA\Property(property="nedd_confirm", type="boolean", example="true"),
     *                  @OA\Property(property="players", type="array",
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="player_id", type="int(11)", example="1"),
     *                          @OA\Property(property="name", type="string(128)", example="王小明"),
     *                          @OA\Property(property="unit", type="string(1)", example="國立台中一中"),
     *                          @OA\Property(property="comment", type="string(1)", example="詳細備註"),
     *                          @OA\Property(property="scores", type="array",
     *                              @OA\Items(type="object",
     *                                  @OA\Property(property="score_id", type="int(11)", example="1"),
     *                                  @OA\Property(property="score", type="float(2,2)", example="8.5"),
     *                                  @OA\Property(property="judger_id", type="int(11)", example="1"),
     *                                  @OA\Property(property="name", type="string(128)", example="王裁判"),
     *                              )
     *                          ),
     *                      )
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function score_list($game_id, $round) {
        if($game_id > 0 || !isset($round)) {
            $database = new frontModel();
            $data = $database->get_score_list(array('game_id' => $game_id, 'round' => (string)$round));
            if($data == 1) {
              json(new resModel(400, '輪數不可以大於該場比賽!'));
            } else {
              json(new resModel(200, $data));
            }
        } else {
            json(new resModel(400, '比賽編號或輪數不符合規則!'));
        }
        // $data = '[
        //     {
        //       "game_id": 1,
        //       "name": "滑輪板街式賽_男子選手組",
        //       "type": 7,
        //       "content": "成人組比賽",
        //       "date": "2022-05-05",
        //       "round": "1",
        //       "players": [
        //         {
        //           "player_id": 1,
        //           "name": "王小明",
        //           "unit": "國立台中一中",
        //           "comment": "詳細備註",
        //           "scores": [
        //             {"score_id": 1,"score": 8.5,"judger_id": 1,"name": "王裁判"},
        //             {"score_id": 2,"score": 1.5,"judger_id": 2,"name": "陳裁判"},
        //             {"score_id": 0,"score": 0,"judger_id": 3,"name": "張裁判"},
        //             {"score_id": 0,"score": 0,"judger_id": 4,"name": "林裁判"},
        //             {"score_id": 0,"score": 0,"judger_id": 5,"name": "梨裁判"}
        //           ]
        //         },
        //         {
        //           "player_id": 2,
        //           "name": "陳小明",
        //           "unit": "國立台中二中",
        //           "comment": "詳細備註",
        //           "scores": [
        //             {"score_id": 3,"score": 9.5,"judger_id": 1,"name": "王裁判"},
        //             {"score_id": 4,"score": 5.5,"judger_id": 2,"name": "陳裁判"},
        //             {"score_id": 0,"score": 0,"judger_id": 3,"name": "張裁判"},
        //             {"score_id": 0,"score": 0,"judger_id": 4,"name": "林裁判"},
        //             {"score_id": 0,"score": 0,"judger_id": 5,"name": "梨裁判"}
        //           ]
        //         }
        //       ]
        //     }
        //   ]';
        // json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Post(
     *     path="/api_test/score/confirm", 
     *     tags={"前台"},
     *     summary="裁判長確認評分資料(將score_id回傳，後端自動將分數整理成rank，必須是同一round的才可以確認!!!)",
     *     security={{"Authorization":{}}}, 
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"score_id"},
     *                  @OA\Property(property="score_id", type="int", example="[1,2,3,4,5]"),
     *                  @OA\Property(property="update", type="array", 
     *                    @OA\Items(type="object",
     *                      @OA\Property(property="score_id", type="int", example="1"),
     *                      @OA\Property(property="score", type="string(4)", example="8.5"),
     *                    )
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="確認成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="確認成功"),
     *              @OA\Property(property="data", example="null"),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="確認失敗"),
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function score_confirm() {
      $post = array();
      $post = post_json();
      
      //Validation
      if(count($post['score_id']) != 5) {
          json(new resModel(401, '請提交5筆評分編號!'));
          return;
      }

      $database = new frontModel();
      $data = $database->score_confirm($post);

      if($data == 4) {
        json(new resModel(400, "操作發生錯誤，請稍後再試!"));
      } else if($data == 3) {  
        json(new resModel(400, "Score_id有無效值!"));
      } else if($data == 2) {
        json(new resModel(400, "Score超過輪數!"));
      } else if($data == 1) {
        json(new resModel(400, "請按照順序確認!"));
      } else {
        json(new resModel(200, "確認成功"));
      }
        // json(new resModel(200, "確認成功"));
    }

    /**
     * @OA\Get(
     *     path="/api_test/rank/status", 
     *     tags={"前台"},
     *     summary="裁判長或ADMIN查看現在排行榜是哪場比賽",
     *     security={{"Authorization":{}}}, 
     *      @OA\Response(
     *          response="200", 
     *          description="裁判長或ADMIN查看現在排行榜是哪場比賽",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="object",
     *                @OA\Property(property="games", type="array",
     *                  @OA\Items(type="object",
     *                    @OA\Property(property="game_id", type="int(11)", example="1"),
     *                    @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                    @OA\Property(property="type", type="string(1)", example="7"),
     *                    @OA\Property(property="content", type="string(1)", example="成人組比賽"),
     *                    @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                  )
     *                ),
     *                @OA\Property(property="rank_sratus", type="object",
     *                  @OA\Property(property="game_id", type="int(11)", example="1"),
     *                  @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                  @OA\Property(property="type", type="string(1)", example="7"),
     *                  @OA\Property(property="content", type="string(1)", example="成人組比賽"),
     *                  @OA\Property(property="date", type="string(10)", example="2022-05-05"),
     *                ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function rank_status() {
        $database = new frontModel();
        $data = $database->get_rank_status();
        json(new resModel(200, $data));

        // $data = '{
        //   "games": [
        //     {
        //       "game_id": 1,
        //       "name": "滑輪板街式賽_男子選手組",
        //       "type": 7,
        //       "content": "成人組比賽",
        //       "date": "2022-05-05"
        //     },
        //     {
        //       "game_id": 2,
        //       "name": "滑輪板街式賽_女子選手組",
        //       "type": 7,
        //       "content": "成人組比賽",
        //       "date": "2022-05-05"
        //     }
        //   ],
        //   "rank_status": {
        //     "game_id": 1,
        //     "name": "滑輪板街式賽_男子選手組",
        //     "type": 7,
        //     "content": "成人組比賽",
        //     "date": "2022-05-05"
        //   }
        // }';
        // json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Post(
     *     path="/api_test/rank/confirm", 
     *     tags={"前台"},
     *     summary="裁判長或ADMIN決定排行榜顯示哪場比賽",
     *     security={{"Authorization":{}}}, 
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"game_id"},
     *                  @OA\Property(property="game_id", type="int", example="1"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="確認成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="確認成功"),
     *              @OA\Property(property="data", example="null"),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="確認失敗"),
     *      @OA\Response(response="403", description="Permission denied(如果JWT的權限不夠就會顯示)"),
     * )
     */
    public function rank_confirm() {
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

      $database = new frontModel();
      $data = $database->rank_confirm(array('game_id' => $post['game_id']));
      if($data == 1) {
        json(new resModel(400, "確認失敗，比賽不存在!"));
      } else {
        json(new resModel(200, "確認成功"));
      }
      

      // json(new resModel(200, "確認成功"));
    }
}