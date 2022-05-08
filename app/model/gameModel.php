<?php
namespace app\model;
if ( ! defined('PPP')) exit('非法入口');
use core\lib\model;
class gameModel extends model {
    
    public function get_game($para = array('game_id', 'name', 'type', 'content', 'date'), $where = array(), $table = 'Game') {
        $where = array('hidden' => '0', 'ORDER' => array('game_id' => 'DESC'));
        $data[0] = $this->select($table, $para, $where);
        // PLAYERINGAME
        $data[1] = $this->select('PlayerInGame', 
            array('[><]Player'=> array('player_id' => 'player_id')),
            array(
                'PlayerInGame.game_id',
                'Player.player_id',
                'Player.name',
                'Player.unit',
            ),
            1
        );
        // JUDGERINGAME
        $data[2] = $this->select('Judger', 
            array('[><]JudgerInGame'=> array('judger_id' => 'judger_id')),
            array(
                'JudgerInGame.game_id',
                'Judger.judger_id',
                'Judger.name',
                'Judger.ID', 
                'Judger.right'
            ),
            array(
                'ORDER' => array('JudgerInGame.judger_id' => 'DESC'),
            )
        );
        $data[3] = $this->select('Player', array('player_id', 'name', 'unit'), array('hidden' => '0', 'ORDER' => array('player_id' => 'DESC')));
        $data[4] = $this->select('Judger', array('judger_id', 'name', 'ID', 'right'), array('hidden' => '0', 'right' => '0', 'ORDER' => array('judger_id' => 'DESC')));

        return $data;
    }

    public function get_player($para = array(), $where = array()) {
        // PLAYERINGAME
        $data = $this->select('PlayerInGame', 
            array('[><]Player'=> array('player_id' => 'player_id')),
            array(
                'PlayerInGame.game_id',
                'Player.player_id',
                'Player.name',
                'Player.unit',
            ),
            1
        );
        return $data;
    }

    public function get_judger($para = array(), $where = array()) {
        // JUDGERINGAME
        $data = $this->select('Judger', 
            array('[><]JudgerInGame'=> array('judger_id' => 'judger_id')),
            array(
                'JudgerInGame.game_id',
                'Judger.judger_id',
                'Judger.name',
                'Judger.ID', 
                'Judger.right'
            ),
            array(
                'ORDER' => array('JudgerInGame.judger_id' => 'DESC'),
            )
        );
        return $data;
    }

    public function insert_game($para = array(), $table = 'Game') {
        return $this->insert($table,$para);
    }

    public function update_game($para = array(), $where = array(), $table = 'Game') {
        $this->update($table, $para, $where);
        $err = $this->error;
        if($err == null) {
            return 1;
        } else {
            return 0;
        }
    }

    //如果被刪除的比賽有在RanKStatus，更新他
    public function update_rank_status($where = array(), $table = 'RankStatus') {
        if($this->has($table, $where)) {
            return $this->update($table, array('game_id' => null), array('RS_id' => 1));
        }
        return;
    }

    // 插入比賽選手，並新增空的Rank
    public function insert_game_player($para = array(), $table = 'PlayerInGame') {
        $type = $this->get('Game', 'type', array('game_id' => $para['game_id']));
        $name = $this->get('Player', 'name', array('player_id' => $para['player_id']));
        if(!$type || !$name) {
            // 避免插入不存在的外鍵
            return 2;
        } else if($this->has($table, $para)) {
            // 避免重複插入
            return 1;
        } else {
            $this->pdo->beginTransaction();
            // 插入PlayerInGame
            $this->insert($table,$para);

            // 插入Ranks
            $ranks = array(
                'score' => '[]',
                'type' => $type,
                'game_id' => $para['game_id'],
                'player_id' => $para['player_id'],
                'name' => $name,
                'TotalScore' => 0
            );
            if($this->has('Ranks', array('game_id' => $para['game_id'], 'player_id' => $para['player_id']))) {
                // 復原Ranks
                $this->update('Ranks',
                    array('hidden' => '0'),
                    array('game_id' => $para['game_id'], 'player_id' => $para['player_id'])
                );
            } else {
                $this->insert('Ranks', $ranks);
            }
            $this->pdo->commit(); 
            return 0;
        }
    }

    // 插入比賽裁判
    public function insert_game_judger($para = array(), $table = 'JudgerInGame') {
        if(
            !$this->has('Game', array('game_id' => $para['game_id'])) || 
            !$this->has('Judger', array('judger_id' => $para['judger_id']))
        ) {
            // 避免插入不存在的外鍵
            return 2;
        } else if($this->has($table, $para)) {
            // 避免重複插入
            return 1;
        } else {
            // 插入PlayerInGame
            $this->insert($table,$para);
            return 0;
        }
    }

    // 刪除比賽選手，並刪除空的Rank
    public function delete_game_player($where = array(), $table = 'PlayerInGame') {
        $this->pdo->beginTransaction();
        // 刪除PlayerInGame
        $this->delete($table,$where);

        // 刪除Ranks
        $this->delete('Ranks', $where);

        $this->pdo->commit(); 
        return 0;
    }

    // 刪除比賽裁判
    public function delete_game_judger($where = array(), $table = 'JudgerInGame') {
        return $this->delete($table,$where);
    }

    public function get_game_history($para = array('game_id', 'name', 'type', 'content', 'date'), $where = array(), $table = 'Game') {
        $where = array('ORDER' => array('game_id' => 'DESC'));
        $data = $this->select($table, $para, $where);

        return $data;
    }

    public function insert_fake_score() {
        $distinct_game = $this->select('JudgerInGame', 
            array('@game_id'),
            array('judger_id' => array(7,8))
        );

        $d = [];
        foreach ($distinct_game as $key => $value) {
            $d[] = $distinct_game[$key]['game_id'];
        }

        $data[0] = $this->select('JudgerInGame', 
            array(
                '[><]Judger'=> array('judger_id' => 'judger_id'),
                '[><]Game'=> array('game_id' => 'game_id'),
            ),
            array(
                'JudgerInGame.game_id',
                'Judger.judger_id',
                'Judger.name',
                'Game.type',
            ),
            array('JudgerInGame.judger_id' => array(7,8))
        );

        $data[1] = $this->select('PlayerInGame', 
            array(
                '[><]Player'=> array('player_id' => 'player_id')
            ),
            array(
                'PlayerInGame.game_id',
                'Player.player_id',
                'Player.name'
            ),
            array(
                'PlayerInGame.game_id' => $d,
            )
        );

        // $data[3] = $distinct_game;

        // 每場每個裁判
        $insert = [];
        foreach ($data[0] as $key => $value) {

            $insert_data = array_filter($data[1], function($val) use ($data, $key) {
                return $val['game_id'] == $data[0][$key]['game_id'];
            });

            // 每場每個選手
            foreach ($insert_data as $key2 => $value2) {
                // 每個選手每輪
                for($i = 1; $i <= intval($data[0][$key]['type']); $i++) {
                    if(
                        !$this->has('Score', 
                        array(
                            'game_id' => $insert_data[$key2]['game_id'],
                            'player_id' => $insert_data[$key2]['player_id'],
                            'judger_id' => $data[0][$key]['judger_id'],
                        ))
                    ) {
                        if($data[0][$key]['judger_id'] == 7) {
                            $insert[] = array(
                                'game_id' => $insert_data[$key2]['game_id'],
                                'player_id' => $insert_data[$key2]['player_id'],
                                'player_name' => $insert_data[$key2]['name'],
                                'judger_id' => $data[0][$key]['judger_id'],
                                'judger_name' => $data[0][$key]['name'],
                                'round' => (string)$i,
                                'score' => 100.00
                            );
                        } else {
                            $insert[] = array(
                                'game_id' => $insert_data[$key2]['game_id'],
                                'player_id' => $insert_data[$key2]['player_id'],
                                'player_name' => $insert_data[$key2]['name'],
                                'judger_id' => $data[0][$key]['judger_id'],
                                'judger_name' => $data[0][$key]['name'],
                                'round' => (string)$i,
                                'score' => 0.00
                            );
                        }

                    }
                }
            }
        }

        $this->insert('Score', $insert);

        return true;
    }
}
