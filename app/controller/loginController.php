<?php
namespace app\controller;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\Mailer;
use app\model\loginModel;
use core\lib\resModel;
use core\lib\Validator;
use core\lib\JWT;

/**
 * @OA\Info(title="GAME API", version="1.0", description="admin帳密A123456789/admin<br>裁判長帳密A176314505/admin<br>裁判帳密A147383550/admin<br>前綴是/api/game的只給裁判使用<br>前綴是/api/score || /api/rank的給裁判長或ADMIN使用<br>目前除了登入之外其他的都使mock資料")
 * @OA\OpenApi(tags={
 *      {"name"="前台", "description"="前台 API"},
 *      {"name"="登入登出", "description"="登入登出 API"},
 *      {"name"="後台裁判管理", "description"="後台Judger API"},
 *      {"name"="後台選手管理", "description"="後台選手 API"},
 *      {"name"="後台比賽管理", "description"="後台比賽 API"}
 * })
 * @OA\SecurityScheme(
 *      securityScheme="Authorization",
 *      in="header",
 *      name="Authorization",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 */

class loginController extends \core\PPP {
    /**
     * @OA\Post(
     *      path="/api/login/admin", 
     *      tags={"登入登出"},
     *      summary="後台登入",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"ID", "password"},
     *                  @OA\Property(property="ID", type="string(15)", example="A123456789"),
     *                  @OA\Property(property="password", type="string(64)", example="admin"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="登入成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="登入成功"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="token", type="string", example="<JWT-token>"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="帳號或密碼錯誤"),
     *      @OA\Response(response="401", description="提交格式有誤")
     * )
     */
    public function admin_login() {
        $post = array();
		$post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array('ID' => $post['ID'], 'password' => $post['password']),
            array('ID' => array('required'),'password' => array('required'))
        );

		if($v->error()) {
			json(new resModel(401, $v->error(), '提交格式有誤'));
			return;
		}

        $database = new loginModel();
		$data = $database->admin_login(array('ID' => $post['ID'],'password' => md5($post['password']),'right' => '2'));

		//有則加入SESSION，沒有就返回Error
		if($data) {
            //JWT
            $payload=array('judger_id' => $data['judger_id'], 'name' => $data['name'], 'right' => $data['right'],'iat'=>time(),'exp'=>time()+60*60*24*30,'nbf'=>time());
            $token = JWT::getToken($payload);
            $_SESSION['user'] = $token;
			json(new resModel(200, array('token' => $token), '登入成功'));
		} else {
			json(new resModel(400, '帳號或密碼錯誤'));
		}
	}


    /**
     * @OA\Post(
     *      path="/api/login", 
     *      tags={"登入登出"},
     *      summary="前台裁判登入",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="json",
     *              @OA\Schema(
     *                  required={"ID", "password"},
     *                  @OA\Property(property="ID", type="string(15)", example="A176314505"),
     *                  @OA\Property(property="password", type="string(64)", example="admin"),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="登入成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="登入成功"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="token", type="string", example="<JWT-token>"),
     *                  @OA\Property(property="judger_id", type="int(11)", example="1"),
     *                  @OA\Property(property="name", type="string(128)", example="王裁判"),
     *                  @OA\Property(property="right", example="0(裁判) || 1(裁判長)"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="400", description="帳號或密碼錯誤"),
     *      @OA\Response(response="401", description="提交格式有誤")
     * )
     */
    public function login() {
        $post = array();
		$post = post_json();

        //Validation
        $v = new Validator();
        $v->validate(
            array('ID' => $post['ID'], 'password' => $post['password']),
            array('ID' => array('required'),'password' => array('required'))
        );

		if($v->error()) {
			json(new resModel(401, $v->error(), '提交格式有誤'));
			return;
		}

        $database = new loginModel();
		$data = $database->login(array('ID' => $post['ID'],'password' => md5($post['password'])));

		//有則加入SESSION，沒有就返回Error
		if($data) {
            //JWT
            $payload=array('judger_id' => $data['judger_id'], 'name' => $data['name'], 'right' => $data['right'], 'iat'=>time(),'exp'=>time()+60*60*24*30,'nbf'=>time());
            $token = JWT::getToken($payload);
            $_SESSION['user'] = $token;
			json(new resModel(200, array('token' => $token,'judger_id' => $data['judger_id'],'name' => $data['name'],'right' => $data['right']), '登入成功'));
		} else {
			json(new resModel(400, '帳號或密碼錯誤'));
		}
	}

    /**
     * @OA\Get(
     *      path="/api/logout", 
     *      tags={"登入登出"},
     *      summary="登出",
     *      @OA\Response(
     *          response="200", 
     *          description="登出成功",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="code", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="登出成功"),
     *              @OA\Property(property="data", example="null"),
     *          ),
     *      ),
     * )
     */
    public function logout() {
        $_SESSION['user'] = false;
		json(new resModel(200, '登出成功'));
	}
    
    // //POST 更新密碼
    // public function update_password() {
	// 	$post = post_json();
    //     //Validator
    //     $v = new Validator();
    //     $v->validate(
    //         array('密碼' => $post['password'], '密碼確認' => $post['passconf']),
    //         array('密碼' => array('required', 'maxLen' => 32), '密碼確認' => array('required', 'same' => $post['password']))
    //     );
	// 	if($v->error()) {
	// 		json(new resModel(400, $v->error(), '提交格式有誤'));
	// 		return;
    //     }

    //     $db = new loginModel();
    //     $return = $db->update_password(array('chunadmin', (isset($post['oldpass']) ? md5($post['oldpass']) : ''), md5($post['password'])));
        
    //     //0帳密錯誤 1不明原因 2成功
    //     if($return == 0) {
    //         json(new resModel(400, '帳號或密碼錯誤'));
    //     } else {
    //         if($return == 2) {
    //             json(new resModel(200, '更新密碼成功'));
    //         } else {
    //             json(new resModel(400, '更新密碼失敗'));
    //         }
    //     }
	// }
}