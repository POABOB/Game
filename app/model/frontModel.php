<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class frontModel extends model {
    
    // rank_list() START
    public function get_rank_list($where = array(), $para = array('score', 'player_id', 'name', 'TotalScore'), $table = 'Ranks') {
        if(count($where) == 0) {
            // 獲取當前RankStatus
            $game_id = $this->get('RankStatus', 'game_id', array('RS_id' => 1));
            $where = array('game_id' => $game_id);
        } else {
            $game_id = $where['game_id'];
        }
        
        if($game_id) {
            // 獲取比賽
            $data[0] = $this->get('Game', array('game_id', 'name', 'type', 'content', 'date'), $where);

            $where = array(
                'game_id' => $game_id,
                'hidden' => '0',
                'ORDER' => array('rank_id' => 'DESC')
            );
            // 獲取該比賽排行
            $data[1] = $this->select($table, $para, $where);
            $type = intval($data[0]['type']);
            foreach ($data[1] as $key => $value) {
                $data[1][$key]['score'] = json_decode($data[1][$key]['score']);
                
                // 補空值
                $score_nums = count($data[1][$key]['score']);
                for($i = $score_nums + 1; $i <= $type; $i++) {
                    $data[1][$key]['score'][] = null;
                }


                if($data[1][$key]['score'][0] == null) {
                    $data[1][$key]['highlight'][0] = 0;
                    $data[1][$key]['highlight'][1] = 0;
                } else if ($data[1][$key]['score'][1] != null && $data[1][$key]['score'][1] > $data[1][$key]['score'][0]) {
                    $data[1][$key]['highlight'][0] = 0;
                    $data[1][$key]['highlight'][1] = 1;
                } else {
                    $data[1][$key]['highlight'][0] = 1;
                    $data[1][$key]['highlight'][1] = 0;
                }

                if($type == 7) {
                    for($i = 3; $i <= $type; $i++) {
                        if($data[1][$key]['score'][$i - 1] !== null) {
                            $data[1][$key]['highlight'][$i - 1] = 1;
                        } else {
                            $data[1][$key]['highlight'][$i - 1] = 0;
                        }
                    }

                    if($score_nums == 6) {
                        $tmp = array_slice($data[1][$key]['score'], 2, 4);
                        $min_key = array_search(min($tmp), $data[1][$key]['score']);
                        $data[1][$key]['highlight'][$min_key] = 0;
                    } else if($score_nums == 7) {
                        $tmp = array_slice($data[1][$key]['score'], 2, 5);
                        asort($tmp);
                        $tmp = array_values($tmp);
                        $min_key = array_search($tmp[0], $data[1][$key]['score']);
                        $data[1][$key]['highlight'][$min_key] = 0;

                        $min_key = array_search($tmp[1], $data[1][$key]['score']);
                        $data[1][$key]['highlight'][$min_key] = 0;
                    }
                }


                $data[0]['rank'][] = $data[1][$key];
            }
            $data = $data[0];
        } else {
            $data = null;
        }
        return $data;
    }
    // rank_list() END

    // rank_status() START
    public function get_rank_status($para = '*', $where = array(), $table = 'Game') {
        $where = array('hidden' => '0', 'ORDER' => array('game_id' => 'DESC'));
        
        $data['games'] = $this->select($table, $para, $where);

        $game_id = $this->get('RankStatus', 'game_id', array('RS_id' => 1));
        
        $data['rank_status'] = array_find(
            $data['games'],
            function($val) use ($game_id) {
                return $val['game_id'] == $game_id;
            }
        );

        return $data;
    }
    // rank_status() END

    // rank_confirm() START
    public function rank_confirm($where = array(), $table = 'Game') {
        $where['hidden'] = '0';
        
        if(!$this->has($table, $where)) {
            return 1;
        } else {
            $this->update('RankStatus',
                array('game_id' => $where['game_id']),
                array('RS_id' => 1)
            );
            return 0;
        }
    }
    // rank_confirm() END

    // game_list() START
    public function get_game_list($where = array(), $table1 = 'Game', $table2 = 'JudgerInGame') {
        // 獲取當日後50筆比賽
        $date = date('Y-m-d');
        return $this->select($table1, 
            array('[><]'.$table2 => array('game_id' => 'game_id')),
            array(
                $table1.'.game_id',
                $table1.'.name',
                $table1.'.type', 
                $table1.'.content', 
                $table1.'.date'
            ),
            array(
                'ORDER' => array($table1.'.date' => 'ASC', $table1.".game_id" => "DESC"),
                'LIMIT' => 50,
                $table1.'.date[>=]' => $date,
                $table2.'.judger_id' => $where['judger_id'],
            )
        );
    }
    // game_list() END

    // score_game_list() START
    public function score_game_list($para = array('game_id', 'name', 'type', 'content', 'date'), $where = array(), $table = 'Game') {
        // 獲取當日後50筆比賽
        $date = date('Y-m-d');
        return $this->select($table, $para,
            array(
                'ORDER' => array('date' => 'ASC', "game_id" => "DESC"),
                'LIMIT' => 50,
                'date[>=]' => $date,
            )
        );
    }
    // score_game_list() END

    // game_insert_score() START
    public function insert_score($para = array(), $table1 = 'Game', $table2 = 'Score') {
        
        $score = $this->select($table2, '*', 
            array(
                'game_id' => $para['game_id'],
                'player_id' => $para['player_id'],
                'judger_id' => $para['judger_id'],
            )
        );

        $data = array_find(
            $score,
            function($val) use ($para) {
                return $val['round'] == $para['round'];
            }
        );
        if($data != null) {
            // 判斷該ROUND是否存在
            return 2;
        } else if(count($score) + 1 != intval($para['round'])) {
            // 判斷是否有跳ROUND
            return 1;
        } else {
            // 獲取Game type
            $type = $this->get($table1, 'type', array('game_id' => $para['game_id']));
            $player_name = $this->get('Player', 'name', array('player_id' => $para['player_id']));
            // 插入
            $para['player_name'] = $player_name;
            $this->insert($table2, $para);
            return 0;
        }
    }
    // game_insert_score() END

    // score_confirm() START
    public function score_confirm($where = array(), $table1 = 'Score', $table2 = 'Ranks') {
        // 如果有update array，先更新
        if(isset($where['update']) && count($where['update']) !== 0) {
            foreach ($where['update'] as $key => $value) {
                $this->update($table1, 
                    array('score' => $where['update'][$key]['score']), 
                    array('score_id' => $where['update'][$key]['score_id']), 
                );
            }
        }
        $where['ORDER'] = 'score';
        $score = $this->select($table1, '*', 
            array(
                'ORDER' => 'score',
                'score_id' => $where['score_id']
            )
        );

        if(count($score) != 5) {
            // 判斷score_id是否5筆皆有效
            return 3;
        }else {
            $newScore = round(($score[1]['score'] + $score[2]['score'] + $score[3]['score']) / 3, 2);

            $rank = $this->get($table2, '*',
                array(
                    'game_id' => $score[1]['game_id'],
                    'player_id' => $score[1]['player_id']
                )
            );

            $rank['score'] = json_decode($rank['score'], JSON_UNESCAPED_UNICODE);
            
            if(count($rank['score']) >= intval($rank['type'])) {
                // 判斷是否超過輪數
                return 2;
            } else if(count($rank['score']) + 1 != $score[1]['round']) {
                // 判斷是否照順序
                return 1;
            } else {
                $rank['score'][] = $newScore;

                // 重新計算totalScore
                $totalScore = 0;
                if(intval($rank['type']) == 7) {
                    if(count($rank['score']) <= 5) {
                        foreach ($rank['score'] as $key => $value) {
                            if($key <= 1) {
                                if(isset($rank['score'][1]) && $rank['score'][1] > $rank['score'][0]) {
                                    $totalScore = $rank['score'][1];
                                } else {
                                    $totalScore = $rank['score'][0];
                                }
                            } else {
                                $totalScore += $rank['score'][$key];
                            }
                        }
                    } else if(count($rank['score']) == 6) {
                        foreach ($rank['score'] as $key => $value) {
                            if($key <= 1) {
                                if(isset($rank['score'][1]) && $rank['score'][1] > $rank['score'][0]) {
                                    $totalScore = $rank['score'][1];
                                } else {
                                    $totalScore = $rank['score'][0];
                                }
                            } else {
                                $totalScore += $rank['score'][$key];
                            }
                        }
                        $tmp = array_slice($rank['score'], -4);
                        $totalScore -= min($tmp);
                    } else if(count($rank['score']) == 7) {
                        foreach ($rank['score'] as $key => $value) {
                            if($key <= 1) {
                                if(isset($rank['score'][1]) && $rank['score'][1] > $rank['score'][0]) {
                                    $totalScore = $rank['score'][1];
                                } else {
                                    $totalScore = $rank['score'][0];
                                }
                            } else {
                                $totalScore += $rank['score'][$key];
                            }
                        }
                        $tmp = array_slice($rank['score'], -5);
                        asort($tmp);
                        $tmp = array_values($tmp);
                        $totalScore -= $tmp[0];
                        $totalScore -= $tmp[1];
                    }
                } else {
                    if(isset($rank['score'][1]) && $rank['score'][1] > $rank['score'][0]) {
                        $totalScore = $rank['score'][1];
                    } else {
                        $totalScore = $rank['score'][0];
                    }
                }
                $rank['score'] = json_encode($rank['score'], true);
                
                $this->pdo->beginTransaction();
                // 更新Rank
                $this->update($table2,
                    array(
                        'score' => $rank['score'],
                        'totalScore' => $totalScore
                    ),
                    array('rank_id ' => $rank['rank_id'])
                );
                if($this->error) {
                    $this->pdo->rollBack();
                    return 4;
                }
                // 更新Score，confirm它
                $this->update($table1, 
                    array('confirm' => '1'),
                    array('score_id' => $where['score_id']) 
                );
                if($this->error) {
                    $this->pdo->rollBack();
                    return 4;
                }

                $this->pdo->commit(); 
                return 0;
            }
        }
    }
    // score_confirm() END

    // game_detail() START
    public function get_game_detail($where = array()) {
        // 獲取該比賽
        $data[0] = $this->get('Game', 
            array('game_id', 'name', 'type', 'content', 'date'), 
            array('game_id' => $where['game_id'])
        );
        $data[1] = $this->select('PlayerInGame', 
            array('[><]Player' => array('player_id' => 'player_id')),
            array(
                'Player.player_id',
                'Player.name',
                'Player.unit', 
                'Player.comment', 
            ),
            array(
                'ORDER' => array('PlayerInGame.game_id' => 'ASC'),
                'PlayerInGame.game_id' => $where['game_id']
            )
        );
        $data[1] = array_reverse($data[1]);
        $data[2] = $this->select('Score', 
            array('player_id', 'score_id', 'score', 'round'),
            array(
                'ORDER' => array('player_id' => 'ASC'),
                'game_id' => $where['game_id'],
                'judger_id' => $where['judger_id'],
            )
        );

        // 整理資料
        foreach ($data[1] as $key => $value) {
            $scores = array_filter(
                $data[2],
                function($val) use ($data, $key) {
                    return $val['player_id'] == $data[1][$key]['player_id'];
                }
            );

            $score_length = count($scores);
            $total_round = intval($data[0]['type']);
            if($score_length > 0) {
                $scores = array_values($scores);
                $data[1][$key]['scores'] = $scores;
                for($i = $score_length + 1; $i <= $total_round; $i++) {
                    $data[1][$key]['scores'][] = array('player_id' => $data[1][$key]['player_id'], 'score_id' => 0, 'score' => 0, 'round' => (string)$i);
                }
            } else {
                for($i = 1; $i <= $total_round; $i++) {
                    $data[1][$key]['scores'][] = array('player_id' => $data[1][$key]['player_id'], 'score_id' => 0, 'score' => 0, 'round' => (string)$i);
                }
            }
        }
        $data[0]['players'] = $data[1];
        $data = $data[0];

        return $data;
    }
    // game_detail() END

    // score_list() START
    public function get_score_list($where = array()) {
        // 獲取該比賽
        $data[0] = $this->get('Game', 
            array('game_id', 'name', 'type', 'content', 'date'), 
            array('game_id' => $where['game_id'])
        );

        $data[0]['round'] = $where['round'];
        if(intval($data[0]['round']) > intval($data[0]['type'])) {
            return 1;
        }
    
        // // 獲取最低round
        // $distinct_lowest_not_confirm_round = $this->get('Score', 
        //     array('@round'), 
        //     array('game_id' => $where['game_id'], 'confirm' => '0')
        // );

        // if($distinct_lowest_not_confirm_round == null) {
        //     // 目前沒有需要確認的ROUND
        //     // 抓最新的round
        //     $distinct_newest_confirm_round = $this->get('Score', 
        //         array('@round'), 
        //         array(
        //             'ORDER' => array('score_id' => 'DESC'), 
        //             'game_id' => $where['game_id'], 
        //             'confirm' => '1'
        //         )
        //     );
        //     if($distinct_newest_confirm_round == null) {
        //         $data[0]['round'] = "1";
        //     } else {
        //         $data[0]['round'] = $distinct_newest_confirm_round['round'];
        //     }
        // } else {
        //     $data[0]['round'] = $distinct_lowest_not_confirm_round['round'];
        // }

        
        // 獲取選手
        $data[1] = $this->select('PlayerInGame', 
            array('[><]Player' => array('player_id' => 'player_id')),
            array(
                'Player.player_id',
                'Player.name',
                'Player.unit', 
                'Player.comment', 
            ),
            array(
                // 'ORDER' => array('Player.player_id' => 'ASC'),
                'PlayerInGame.game_id' => $where['game_id']
            )
        );
        $data[1] = array_reverse($data[1]);

        $data[2] = $this->select('Score', 
            array('player_id', 'score_id', 'score', 'judger_id', 'judger_name'),
            array(
                // 'ORDER' => array('player_id' => 'ASC'),
                'game_id' => $where['game_id'],
                'round' => $data[0]['round'],
                'confirm' => '0'
            )
        );

        $data[3] = $this->select('JudgerInGame', 
            array('[><]Judger' => array('judger_id' => 'judger_id')),
            array('Judger.judger_id','Judger.name',),
            array(
                'ORDER' => array('Judger.judger_id' => 'ASC'),
                'JudgerInGame.game_id' => $where['game_id'],
                'LIMIT' => 5
            )
        );

        if(count($data[3]) !== 5) {
            $data[0]['enable'] = false;
        } else {
            $data[0]['enable'] = true;
        }

        // 判斷該輪是否可提交
        $data[4] = $this->select('Ranks', array('rank_id', 'score'), array('game_id' => $where['game_id'], 'hidden' => '0'));

        $need_confirm = false;
        $int_round = intval($data[0]['round']);

        
        foreach ($data[4] as $key => $value) {
            $data[4][$key]['score'] = json_decode($data[4][$key]['score']);
            if($int_round == 1) {
                if(!isset($data[4][$key]['score'][$int_round - 1])) {
                    $need_confirm = true;
                    break;
                }
            } else {
                if(!isset($data[4][$key]['score'][$int_round - 1])) {
                    $need_confirm = true;
                }

                if(!isset($data[4][$key]['score'][$int_round - 2])) {
                    $need_confirm = false;
                    break;
                }
            }
        }

        $data[0]['need_confirm'] = $need_confirm;

        // 整理資料
        foreach ($data[1] as $key => $value) {
            $data[1][$key]['scores'] = [];
            $scores = array_filter(
                $data[2],
                function($val) use ($data, $key) {
                    return $val['player_id'] == $data[1][$key]['player_id'];
                }
            );

            $score_length = count($scores);
            $total_round = intval($data[0]['type']);
            if($score_length > 0) {
                $data[1][$key]['confirm'] = 0;
                $scores = array_values($scores);
                for($i = 1; $i <= 5; $i++) {
                    $data[1][$key]['scores'][] = array(
                        'player_id' => $data[1][$key]['player_id'], 
                        'score_id' => 0, 
                        'score' => 0, 
                        'judger_id' => isset($data[3][$i - 1]['judger_id']) ? $data[3][$i - 1]['judger_id'] : 0, 
                        'judger_name' => isset($data[3][$i - 1]['name']) ? $data[3][$i - 1]['name'] : ''
                    );

                    $d = null;
                    $d = array_find(
                        $scores,
                        function($val) use ($data, $i) {
                            return $val['judger_id'] == (isset($data[3][$i - 1]['judger_id']) ? $data[3][$i - 1]['judger_id'] : -1);
                        }
                    );

                    if($d !== null) {
                        $data[1][$key]['scores'][$i-1]['score_id'] = $d['score_id']; 
                        $data[1][$key]['scores'][$i-1]['score'] = $d['score'];
                        // $data[1][$key]['scores'][] = $d;
                    } else {
                        
                    }
                }
            } else {
                // confirm = 1
                $confirm_data = $this->select('Score', 
                    array('player_id', 'score_id', 'score', 'judger_id', 'judger_name'),
                    array(
                        'game_id' => $where['game_id'],
                        'round' => $data[0]['round'],
                        'confirm' => '1',
                        'player_id' => $data[1][$key]['player_id'], 
                    )
                );
                if(count($confirm_data) != 0) {
                    $data[1][$key]['confirm'] = 1;
                    $data[1][$key]['scores'] = $confirm_data;
                } else {
                    // 空SCORE
                    for($i = 1; $i <= 5; $i++) {
                        $data[1][$key]['confirm'] = 0;
                        $data[1][$key]['scores'][] = array(
                            'player_id' => $data[1][$key]['player_id'], 
                            'score_id' => 0, 
                            'score' => 0, 
                            'judger_id' => isset($data[3][$i - 1]['judger_id']) ? $data[3][$i - 1]['judger_id'] : 0, 
                            'judger_name' => isset($data[3][$i - 1]['name']) ? $data[3][$i - 1]['name'] : ''
                        );
                    }
                }
            }
        }
        $data[0]['players'] = $data[1];
        $data[1] = $data[3];
        $return = array($data[0], $data[3]);
        return $return;
    }
    // score_list() END
}
