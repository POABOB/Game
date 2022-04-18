<?php
if ( ! defined('PPP')) exit('非法入口');
use core\common\auth;
//DOCS: https://github.com/bramus/router

if(!isset($_SESSION['user'])) $_SESSION['user'] = false;


// $router->get("/doc", function() { require(PPP . '/static/doc/index.html'); });
// $router->get("/swagger", function() {
//     $openapi = \OpenApi\Generator::scan([APP . '/controller']);
//     header('Content-Type: application/json');
//     echo $openapi->toJSON();
// });

$router->options('/.*', function() {});
// 登入登出
$router->post('/login', 'loginController@login');
$router->post('/login/admin', 'loginController@admin_login');
$router->get('/logout', 'loginController@logout');

$router->get('/f/rank/list', 'frontController@rank_list');

// 以下需要JWT驗證
$router->before('GET|POST', '/score.*', function() { auth::factory()->user1(); });
$router->before('GET|POST', '/rank.*', function() { auth::factory()->user1(); });

// 裁判長
$router->get('/score/list', 'frontController@score_list');//
$router->post('/score/confirm', 'frontController@score_confirm');//
$router->get('/rank/status', 'frontController@rank_status');
$router->post('/rank/confirm', 'frontController@rank_confirm');

// 裁判
$router->before('GET|POST', '/game.*', function() { auth::factory()->user0(); });
$router->get('/game/list/(\d+)', 'frontController@game_list');
$router->get('/game/detail/(\d+)', 'frontController@game_detail');//
$router->post('/game/insert/score', 'frontController@game_insert_score');

// ADMIN
$router->before('GET|POST', '/admin.*', function() {
    auth::factory()->admin('Session 過期，請重新再登入');
});

$router->get('/admin/judger', 'judgerController@index_');
$router->post('/admin/judger', 'judgerController@insert_');
$router->patch('/admin/judger', 'judgerController@update_');
$router->delete('/admin/judger', 'judgerController@delete_');
$router->patch('/admin/judger/back', 'judgerController@delete_back');
$router->patch('/admin/judger/password', 'judgerController@update_password');

$router->get('/admin/player', 'playerController@index_');
$router->post('/admin/player', 'playerController@insert_');
$router->patch('/admin/player', 'playerController@update_');
$router->delete('/admin/player', 'playerController@delete_');

$router->get('/admin/game', 'gameController@index_');
$router->post('/admin/game', 'gameController@insert_');
$router->patch('/admin/game', 'gameController@update_');
$router->delete('/admin/game', 'gameController@delete_');

$router->get('/admin/game/player', 'gameController@get_player');
$router->get('/admin/game/judger', 'gameController@get_judger');
$router->post('/admin/game/player', 'gameController@insert_player');
$router->post('/admin/game/judger', 'gameController@insert_judger');
$router->delete('/admin/game/player', 'gameController@delete_player');
$router->delete('/admin/game/judger', 'gameController@delete_judger');
//info OK
$router->get('/info', function() { auth::factory()->user_info('Session 過期，請重新再登入'); });


