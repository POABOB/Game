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

            return $data;
        } else {
            $data[0] = false;
            return $data;
        }

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
                $this->update($table2,
                    array(
                        'score' => $rank['score'],
                        'totalScore' => $totalScore
                    )
                );
                return 0;
            }
        }
    }
    // score_confirm() END
}
