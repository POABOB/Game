<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class frontModel extends model {
    
    // rank_list() START
    public function get_rank_list($para = array('score', 'player_id', 'name', 'TotalScore'), $where = array(), $table = 'Ranks') {
        // 獲取當前RankStatus
        $game_id = $this->get('RankStatus', 'game_id', array('RS_id' => 1));
        $where = array('game_id' => $game_id);
        
        if($game_id) {
            // 獲取比賽
            $data[0] = $this->get('Game', array('game_id', 'name', 'type', 'content', 'date'), $where);

            // 獲取該比賽排行
            $data[1] = $this->select($table, $para, $where);
            $type = intval($data[0]['type']);
            foreach ($data[1] as $key => $value) {
                $data[1][$key]['score'] = json_decode($data[1][$key]['score']);
                $score_nums = count($data[1][$key]['score']);
                for($i = $score_nums + 1; $i <= $type; $i++) {
                    $data[1][$key]['score'][] = null;
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
        $where = array('hidden' => '0');
        
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
                'ORDER' => array($table1.'.date' => 'ASC'),
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
                'ORDER' => array('date' => 'ASC'),
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
        $where['ORDER'] = 'score';
        $score = $this->select($table1, '*', $where);

        if(count($score) != 5) {
            // 判斷score_id是否5筆皆有效
            return 3;
        }else {
            $newScore = round(($score[1]['score'] + $score[2]['score'] + $score[3]['score']) / 3, 1);

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
                            $totalScore += $rank['score'][$key];
                        }
                    } else if(count($rank['score']) == 6) {
                        foreach ($rank['score'] as $key => $value) {
                            $totalScore += $rank['score'][$key];
                        }
                        $tmp = array_slice($rank['score'], -4);
                        $totalScore -= min($tmp);
                    } else if(count($rank['score']) == 7) {
                        foreach ($rank['score'] as $key => $value) {
                            $totalScore += $rank['score'][$key];
                        }
                        $tmp = array_slice($rank['score'], -5);
                        asort($tmp);
                        $totalScore -= $tmp[0];
                        $totalScore -= $tmp[1];
                    }
                } else {
                    if(isset($rank['score'][0])) $totalScore += $rank['score'][0];
                    if(isset($rank['score'][1])) $totalScore += $rank['score'][1];
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
                // 更新Score，confirm它
                $this->update($table1, array('confirm' => '1'), $where);

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
                'ORDER' => array('Player.player_id' => 'ASC'),
                'PlayerInGame.game_id' => $where['game_id']
            )
        );
        $data[2] = $this->select('Score', 
            array('player_id', 'score', 'round'),
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
                    $data[1][$key]['scores'][] = array('score_id' => 0, 'score' => 0, 'round' => (string)$i);
                }
            } else {
                for($i = 1; $i <= $total_round; $i++) {
                    $data[1][$key]['scores'][] = array('score_id' => 0, 'score' => 0, 'round' => (string)$i);
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

        // 獲取最低round
        $round = $this->select('Score', 
            array('@round'), 
            array('game_id' => $where['game_id'], 'confirm' => '0')
        );
        p($round);exit;

        // $data[1] = $this->select('PlayerInGame', 
        //     array('[><]Player' => array('player_id' => 'player_id')),
        //     array(
        //         'Player.player_id',
        //         'Player.name',
        //         'Player.unit', 
        //         'Player.comment', 
        //     ),
        //     array(
        //         'ORDER' => array('Player.player_id' => 'ASC'),
        //         'PlayerInGame.game_id' => $where['game_id']
        //     )
        // );
        // $data[2] = $this->select('PlayerInGame', 
        //     array(
        //         '[><]Score' => array('game_id' => 'game_id'),
        //         '[><]Player' => array('player_id' => 'player_id')
        //     ),
        //     array(
        //         'Score.player_id',
        //         'Score.score',
        //         'Score.round',
        //     ),
        //     array(
        //         'ORDER' => array('Score.player_id' => 'ASC'),
        //         'Score.game_id' => $where['game_id'],
        //         'Score.round' => $round,
        //     )
        // );

        // // 整理資料
        // foreach ($data[1] as $key => $value) {
        //     $scores = array_filter(
        //         $data[2],
        //         function($val) use ($data) {
        //             return $val['player_id'] == $data[1][$key]['player_id'];
        //         }
        //     );

        //     $score_length = count($scores);
        //     $total_round = intval($data[0]['type']);
        //     if($score_length > 0) {
        //         array_push($data[1][$key]['scores'], $scores);
        //         for($i = $score_length + 1; $i <= $total_round; $i++) {
        //             $data[1][$key]['scores'][] = array('score_id' => 0, 'scores' => 0, 'round' => (string)$i);
        //         }
        //     } else {
        //         for($i = 1; $i <= $total_round; $i++) {
        //             $data[1][$key]['scores'][] = array('score_id' => 0, 'scores' => 0, 'round' => (string)$i);
        //         }
        //     }
        // }
        // $data[0]['players'] = $data[1];
        // $data = $data[0];

        return $data;
    }
    // score_list() END
}
