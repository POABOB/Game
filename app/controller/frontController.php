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
     *     path="/api/f/rank/list", 
     *     tags={"前台"},
     *     summary="前台獲取排名顯示螢幕",
     *      @OA\Response(
     *          response="200", 
     *          description="獲取排名",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", example="null"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="game_id", type="int(11)", example="1"),
     *                      @OA\Property(property="name", type="string(128)", example="滑輪板街式賽_男子選手組"),
     *                      @OA\Property(property="type", type="string(1)", example="7"),
     *                      @OA\Property(property="rank", type="array",
     *                          @OA\Items(type="object",
     *                              @OA\Property(property="player_id", type="int(11)", example="1"),
     *                              @OA\Property(property="name", type="string(128)", example="王小明"),
     *                              @OA\Property(property="score", type="int", example="[7,6,5,2,1,8,8]"),
     *                              @OA\Property(property="totalScore", type="tinyint(3)", example="28"),
     *                          )
     *                      ),
     *                      
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="獲取失敗"),
     *      @OA\Response(response="403", description="Permission denied"),
     * )
     */
    public function rank_list() {
            // $database = new frontModel();
            // $data[0] = $database->get_game();
            // $data[1] = $database->get_rank();

            // $data[1] = array();
            // foreach ($data[0] as $key => $value) {
            //     if($data[0][$key]['img'] != "") {
            //         $data[1][] = array(
            //             'name' => $data[0][$key]['img'],
            //             'path' => $data[0][$key]['img']
            //         );
            //     }
                
            //     if($data[0][$key]['img2'] != "") {
            //         $data[1][] = array(
            //             'name' => $data[0][$key]['img2'],
            //             'path' => $data[0][$key]['img2']
            //         );
            //     }
    
            //     if($data[0][$key]['small_img'] != "") {
            //         $data[1][] = array(
            //             'name' => $data[0][$key]['small_img'],
            //             'path' => $data[0][$key]['small_img']
            //         );
            //     }
            // }
            // if(count($data[1]) == 0) {
            //     $data[1][0] = [];
            // }
            $data = '[
                {
                  "game_id": 1,
                  "name": "滑輪板街式賽_男子選手組",
                  "type": 7,
                  "rank": [
                    {"player_id": 1,"name": "王小明","score": [7, 6, 5, 2, 1, 8, 8],"totalScore": 28},
                    {"player_id": 2,"name": "陳小明","score": [9, 6, 5, 2, 1, 8, 8],"totalScore": 30},
                    {"player_id": 3,"name": "李小明","score": [7, 6, 5, 2, 8, 8, 8],"totalScore": 31},
                    {"player_id": 4,"name": "謝小明","score": [7, 6, 5, 2, 9, 8, 8],"totalScore": 32},
                    {"player_id": 5,"name": "林小明","score": [7, 6, 5, 2, 10, 8, 8],"totalScore": 33},
                    {"player_id": 6,"name": "魏小明","score": [7, 6, 5, 2, 10, 10, 8],"totalScore": 35},
                    {"player_id": 7,"name": "蕭小明","score": [7, 6, 5, 2, 5, 5, 5],"totalScore": 22},
                    {"player_id": 8,"name": "黎小明","score": [7, 6, 5, 2, 6, 6, 6],"totalScore": 25},
                    {"player_id": 9,"name": "邱小明","score": [7, 6, 5, 2, 10, 10, 10],"totalScore": 37}
                  ]
                }
              ]';
            json(new resModel(200, json_decode($data)));
        }

    /**
     * @OA\Get(
     *     path="/api/game/list/{judger_id}", 
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
    public function game_list($judger_id) {
        // if($judger_id > 0) {
        //     $database = new frontModel();
        //     $data = $database->get_game();

        //     json(new resModel(200, $data));
        // } else {
        //     json(new resModel(400, '獲取失敗'));
        // }
        $data = '[
            {"game_id": 1,"name": "滑輪板街式賽_男子選手組","type": 7,"content": "成人組比賽","date": "2022-05-05"},
            {"game_id": 2,"name": "滑輪板街式賽_女子選手組","type": 7,"content": "成人組比賽","date": "2022-05-05"},
            {"game_id": 3,"name": "滑輪板街式賽_高中選手組","type": 2,"content": "學生組比賽","date": "2022-05-06"}
          ]';
        json(new resModel(200, json_decode($data)));
    }

        /**
     * @OA\Get(
     *     path="/api/game/detail/{game_id}", 
     *     tags={"前台"},
     *     summary="裁判獲取比賽詳細資訊(選手、已評分資料)(獲取的scores以judger_id做分類)",
     *     security={{"Authorization":{}}}, 
     *     @OA\Parameter(
     *          name="game_id",
     *          description="裁判id",
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
     *                              @OA\Property(property="judger_id", type="int(11)", example="1"),
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
        // if($game_id > 0) {
        //     $database = new frontModel();
        //     $data = $database->get_game();

        //     json(new resModel(200, $data));
        // } else {
        //     json(new resModel(400, '獲取失敗'));
        // }
        $data = '[
            {
              "game_id": 1,
              "name": "滑輪板街式賽_男子選手組",
              "type": 7,
              "content": "成人組比賽",
              "date": "2022-05-05",
              "players": [
                {
                  "player_id": 1,
                  "name": "王小明",
                  "unit": "國立台中一中",
                  "comment": "詳細備註",
                  "judger_id": 1,
                  "scores": [
                    {"score_id": 1,"score": 8.5,"judger_id": 1,"name": "王裁判"},
                    {"score_id": 2,"score": 1.5,"judger_id": 2,"name": "陳裁判"}}
                  ]
                },
                {
                    "player_id": 2,
                    "name": "陳小明",
                    "unit": "國立台中二中",
                    "comment": "詳細備註",
                    "judger_id": 1,
                    "scores": [
                      {"score_id": 3,"score": 9.5,"judger_id": 1,"name": "王裁判"},
                      {"score_id": 4,"score": 5.5,"judger_id": 2,"name": "陳裁判"}
                    ]
                }
              ]
            }
          ]';
        json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Post(
     *     path="/api/game/insert/score", 
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

        // $database = new frontModel();
        // $data = $database->get_game();

        // json(new resModel(200, $data));

        json(new resModel(200, "新增成功"));
    }

    /**
     * @OA\Get(
     *     path="/api/score/list", 
     *     tags={"前台"},
     *     summary="裁判長獲取比賽評分資料(獲取的scores以round做分類)",
     *     security={{"Authorization":{}}}, 
     *      @OA\Response(
     *          response="200", 
     *          description="裁判長獲取比賽評分資料",
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
     *                              @OA\Property(property="round", type="string(1)", example="1~7(第幾輪評分)"),
     *                              @OA\Property(property="scores", type="array",
     *                                  @OA\Items(type="object",
     *                                      @OA\Property(property="score_id", type="int(11)", example="1"),
     *                                      @OA\Property(property="score", type="float(2,2)", example="8.5"),
     *                                      @OA\Property(property="judger_id", type="int(11)", example="1"),
     *                                      @OA\Property(property="name", type="string(128)", example="王裁判"),
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
    public function score_list() {
        $data = '[
            {
              "game_id": 1,
              "name": "滑輪板街式賽_男子選手組",
              "type": 7,
              "content": "成人組比賽",
              "date": "2022-05-05",
              "players": [
                {
                  "player_id": 1,
                  "name": "王小明",
                  "unit": "國立台中一中",
                  "comment": "詳細備註",
                  "round": "1",
                  "scores": [
                    {"score_id": 1,"score": 8.5,"round": "1"},
                    {"score_id": 2,"score": 1.5,"round": "2"}
                  ]
                },
                {
                    "player_id": 2,
                    "name": "陳小明",
                    "unit": "國立台中二中",
                    "comment": "詳細備註",
                    "round": "1",
                    "scores": [
                      {"score_id": 3,"score": 9.5,"judger_id": 1,"name": "王裁判"},
                      {"score_id": 4,"score": 5.5,"round": "2"}
                    ]
                }
              ]
            }
          ]';
        json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Post(
     *     path="/api/score/confirm", 
     *     tags={"前台"},
     *     summary="裁判長確認評分資料(將score_id回傳，後端自動將分數整理成rank，必須是同一round的才可以確認!!!)",
     *     security={{"Authorization":{}}}, 
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"score_id"},
     *                  @OA\Property(property="score_id", type="int", example="[1,2,3,4,5]"),
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
        json(new resModel(200, "確認成功"));
    }

    /**
     * @OA\Get(
     *     path="/api/rank/status", 
     *     tags={"前台"},
     *     summary="裁判長或ADMIN查看現在排行榜是哪場比賽",
     *     security={{"Authorization":{}}}, 
     *      @OA\Response(
     *          response="200", 
     *          description="裁判長或ADMIN查看現在排行榜是哪場比賽",
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
    public function rank_status() {
        $data = '[
            {
              "game_id": 1,
              "name": "滑輪板街式賽_男子選手組",
              "type": 7,
              "content": "成人組比賽",
              "date": "2022-05-05"
            },
            {
                "game_id": 2,
                "name": "滑輪板街式賽_女子選手組",
                "type": 7,
                "content": "成人組比賽",
                "date": "2022-05-05"
              }
          ]';
        json(new resModel(200, json_decode($data)));
    }

    /**
     * @OA\Post(
     *     path="/api/rank/confirm", 
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
        json(new resModel(200, "確認成功"));
    }
}