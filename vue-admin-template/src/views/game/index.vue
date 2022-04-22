<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增比賽</el-button>
      </el-col>
      <el-col>
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item label="搜尋">
            <el-input v-model="searchMap.word" placeholder="名稱 日期..." />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" icon="el-icon-search" @click="Search">查詢</el-button>
          </el-form-item>
        </el-form>

      </el-col>
    </el-row>

    <el-table
      v-loading="listLoading"
      :data="list.slice((page[0].currpage - 1) * page[0].pagesize, page[0].currpage * page[0].pagesize)"
      element-loading-text="Loading"
      border
      fit
      highlight-current-row
    >
      <el-table-column
        align="center"
        label="#"
        width="50"
      >
        <template slot-scope="scope">
          {{ (scope.$index + (page[0].currpage - 1) * page[0].pagesize) + 1 }}
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="game_id"
        label="比賽編號"
      />
      <el-table-column
        align="center"
        prop="name"
        label="名稱"
      />
      <el-table-column
        align="center"
        prop="date"
        label="日期"
      />
      <el-table-column
        align="center"
        prop="content"
        label="內容"
        type="textarea"
        :rows="8"
      />
      <el-table-column
        class-name="status-col"
        label="類型"
        width="110"
        align="center"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.type | statusFilter">{{ (scope.row.type === "2") ? '兩輪' : '兩輪+五大招' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
        width="250"
      >
        <template slot-scope="scope">
          <el-button
            type="info"
            icon="el-icon-edit"
            circle
            @click="Edit(scope.row.game_id)"
          />
          <el-button
            type="primary"
            icon="el-icon-user-solid"
            circle
            @click="EditPlayer(scope.row.game_id)"
          />
          <el-button
            type="warning"
            icon="el-icon-s-order"
            circle
            @click="EditJudger(scope.row.game_id)"
          />
          <el-button
            type="danger"
            icon="el-icon-delete"
            circle
            @click="Delete(scope.row.game_id)"
          />
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      background
      layout="prev, pager, next, sizes, total, jumper"
      align="center"
      :page-sizes="[5, 10, 15, 20]"
      :page-size="page[0].pagesize"
      :total="list.length"
      @current-change="(val) => handleCurrentChange(val, 0)"
      @size-change="(val) => handleSizeChange(val, 0)"
    />

    <!-- Form -->
    <el-dialog title="比賽管理" :visible.sync="dialogFormVisible[0].B">
      <el-form ref="form" :model="form" :rules="rules">
        <el-form-item label="名稱" prop="name">
          <el-input
            v-model="form.name"
            autocomplete="off"
            placeholder="名稱"
          />
        </el-form-item>
        <el-form-item label="日期" prop="date">
          <el-date-picker
            v-model="form.date"
            type="date"
            value-format="yyyy-MM-dd"
            placeholder="日期"
          />
        </el-form-item>
        <el-form-item label="內容" prop="content">
          <el-input
            v-model="form.content"
            autocomplete="off"
            type="textarea"
            placeholder="內容"
            :rows="8"
          />
        </el-form-item>
        <el-form-item label="類型">
          <el-select v-model="form.type" placeholder="類型">
            <el-option label="兩輪" value="2" />
            <el-option label="兩輪+五大招" value="7" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button
          @click="dialogFormVisible[0].B = false"
        >
          取 消
        </el-button>
        <el-button
          v-show="
            mode === 'add'"
          type="info"
          @click="Insert"
        >
          確 定
        </el-button>
        <el-button
          v-show=" mode === 'edit'"
          type="info"
          @click="Update"
        >
          更新
        </el-button>
      </div>
    </el-dialog>

    <!-- PlayerForm -->
    <el-dialog title="選手管理" :visible.sync="dialogFormVisible[1].B" top="2vh">
      <h3>比賽名稱：{{ game.name }}，比賽編號：{{ game.game_id }}</h3>
      <h3>已加入選手</h3>
      <el-table
        :data="PlayerInGame.slice((page[1].currpage - 1) * page[1].pagesize, page[1].currpage * page[1].pagesize)"
        element-loading-text="Loading"
        border
        fit
        highlight-current-row
        size="mini"
      >
        <el-table-column
          align="center"
          label="#"
          width="50"
        >
          <template slot-scope="scope">
            {{ (scope.$index + (page[1].currpage - 1) * page[1].pagesize) + 1 }}
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          prop="name"
          label="名稱"
        />
        <el-table-column
          align="center"
          prop="unit"
          label="單位"
        />
        <el-table-column
          align="center"
          label="操作"
          width="60"
        >
          <template slot-scope="scope">
            <el-button
              type="danger"
              icon="el-icon-delete"
              circle
              size="mini"
              @click="DeletePlayer(scope.row.player_id)"
            />
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        background
        layout="prev, pager, next"
        align="center"
        :page-sizes="[5, 10]"
        :page-size="page[1].pagesize"
        :total="PlayerInGame.length"
        @current-change="(val) => handleCurrentChange(val, 1)"
        @size-change="(val) => handleSizeChange(val, 1)"
      />
      <el-divider />
      <h3>未加入選手</h3>
      <el-table
        :data="PlayerNotInGame.slice((page[2].currpage - 1) * page[2].pagesize, page[2].currpage * page[2].pagesize)"
        element-loading-text="Loading"
        border
        fit
        highlight-current-row
        size="mini"
      >
        <el-table-column
          align="center"
          label="#"
          width="50"
        >
          <template slot-scope="scope">
            {{ (scope.$index + (page[2].currpage - 1) * page[2].pagesize) + 1 }}
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          prop="name"
          label="名稱"
          height="50"
        />
        <el-table-column
          align="center"
          prop="unit"
          label="單位"
        />
        <el-table-column
          align="center"
          label="操作"
          width="60"
        >
          <template slot-scope="scope">
            <el-button
              type="primary"
              icon="el-icon-plus"
              circle
              size="mini"
              @click="InsertPlayer(scope.row.player_id)"
            />
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        background
        layout="prev, pager, next"
        align="center"
        :page-sizes="[5, 10]"
        :page-size="page[2].pagesize"
        :total="PlayerNotInGame.length"
        @current-change="(val) => handleCurrentChange(val, 2)"
        @size-change="(val) => handleSizeChange(val, 2)"
      />
    </el-dialog>

    <!-- JudgerForm -->
    <el-dialog title="裁判管理" :visible.sync="dialogFormVisible[2].B" top="2vh">
      <h3>比賽名稱：{{ game.name }}，比賽編號：{{ game.game_id }}</h3>
      <h3>已加入裁判</h3>
      <el-table
        :data="JudgerInGame.slice((page[3].currpage - 1) * page[3].pagesize, page[3].currpage * page[3].pagesize)"
        element-loading-text="Loading"
        border
        fit
        highlight-current-row
        size="mini"
      >
        <el-table-column
          align="center"
          label="#"
          width="50"
        >
          <template slot-scope="scope">
            {{ (scope.$index + (page[3].currpage - 1) * page[3].pagesize) + 1 }}
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          prop="name"
          label="名稱"
        />
        <el-table-column
          align="center"
          prop="ID"
          label="ID"
        />
        <el-table-column
          align="center"
          label="操作"
          width="60"
        >
          <template slot-scope="scope">
            <el-button
              type="danger"
              icon="el-icon-delete"
              circle
              size="mini"
              @click="DeleteJudger(scope.row.judger_id)"
            />
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        background
        layout="prev, pager, next"
        align="center"
        :page-sizes="[5, 10]"
        :page-size="page[3].pagesize"
        :total="JudgerInGame.length"
        @current-change="(val) => handleCurrentChange(val, 3)"
        @size-change="(val) => handleSizeChange(val, 3)"
      />
      <el-divider />
      <h3>未加入裁判</h3>
      <el-table
        :data="JudgerNotInGame.slice((page[4].currpage - 1) * page[4].pagesize, page[4].currpage * page[4].pagesize)"
        element-loading-text="Loading"
        border
        fit
        highlight-current-row
        size="mini"
      >
        <el-table-column
          align="center"
          label="#"
          width="50"
        >
          <template slot-scope="scope">
            {{ (scope.$index + (page[4].currpage - 1) * page[4].pagesize) + 1 }}
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          prop="name"
          label="名稱"
          height="50"
        />
        <el-table-column
          align="center"
          prop="ID"
          label="ID"
        />
        <el-table-column
          align="center"
          label="操作"
          width="60"
        >
          <template slot-scope="scope">
            <el-button
              type="primary"
              icon="el-icon-plus"
              circle
              size="mini"
              @click="InsertJudger(scope.row.judger_id)"
            />
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        background
        layout="prev, pager, next"
        align="center"
        :page-sizes="[5, 10]"
        :page-size="page[4].pagesize"
        :total="JudgerNotInGame.length"
        @current-change="(val) => handleCurrentChange(val, 4)"
        @size-change="(val) => handleSizeChange(val, 4)"
      />
    </el-dialog>
  </div>
</template>

<script>
import { getGame,
  insertGame,
  updateGame,
  deleteGame,
  getGamePlayer,
  insertGamePlayer,
  deleteGamePlayer,
  getGameJudger,
  insertGameJudger,
  deleteGameJudger } from '@/api/game'

export default {
  filters: {
    statusFilter(status) {
      const statusMap = { '2': 'success', '7': 'gray' }
      return statusMap[status]
    }
  },
  data() {
    return {
      list: [],
      fullList: [],
      PlayerInGame: [],
      JudgerInGame: [],
      FullPlayerInGame: [],
      FullJudgerInGame: [],
      PlayerNotInGame: [],
      JudgerNotInGame: [],
      FullPlayerNotInGame: [],
      FullJudgerNotInGame: [],
      Player: [],
      Judger: [],
      game: {
        name: '',
        game_id: ''
      },
      listLoading: true,
      page: [
        { pagesize: 5, currpage: 1 },
        { pagesize: 5, currpage: 1 },
        { pagesize: 5, currpage: 1 },
        { pagesize: 5, currpage: 1 },
        { pagesize: 5, currpage: 1 }
      ],
      searchMap: {
        word: null
      },
      form: {
        game_id: '',
        name: '',
        date: '',
        content: '',
        type: '2'
      },
      dialogFormVisible: [{ B: false }, { B: false }, { B: false }],
      mode: 'add',
      rules: {
        name: [
          { required: true, message: '請輸入名稱!', trigger: 'blur' },
          { max: 128, message: '長度不能超過128個字!', trigger: 'blur' }
        ],
        date: [
          { required: true, message: '請選擇日期!', trigger: 'blur' }
        ],
        content: [
          { max: 256, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        type: [
          { required: true, message: '請選擇類型!', trigger: 'blur' }
        ]
      }
    }
  },
  watch: {
    'searchMap.word': {
      handler: function() {
        if (this.searchMap.word === '' || this.searchMap.word === null) {
          this.Search()
        }
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      getGame().then(response => {
        const data = (response.data[0] === null) ? [] : JSON.parse(JSON.stringify(response.data[0]))
        this.list = data
        this.fullList = data
        // PLAYERINGAME
        let tmp = (response.data[1] === null) ? [] : JSON.parse(JSON.stringify(response.data[1]))
        this.PlayerInGame = tmp
        this.FullPlayerInGame = tmp
        // JUDGERINGAME
        tmp = (response.data[2] === null) ? [] : JSON.parse(JSON.stringify(response.data[2]))
        this.JudgerInGame = tmp
        this.FullJudgerInGame = tmp
        // PLAYER
        this.Player = (response.data[3] === null) ? [] : JSON.parse(JSON.stringify(response.data[3]))
        // JUDGER
        this.Judger = (response.data[4] === null) ? [] : JSON.parse(JSON.stringify(response.data[4]))

        this.listLoading = false
      }).catch(error => {
        alert(error)
        this.listLoading = false
      })
    },
    handleCurrentChange(cpage, index) {
      this.page[index].currpage = cpage
    },
    handleSizeChange(psize, index) {
      this.page[index].pagesize = psize
    },
    Search() {
      this.listLoading = true
      if (this.searchMap.word !== null) {
        this.list = this.fullList.filter(array => array.name.match(this.searchMap.word) || array.date.match(this.searchMap.word))
      } else {
        this.list = this.fullList
      }
      this.listLoading = false
    },
    Switch(index = 0) {
      if (this.dialogFormVisible[index].B) {
        this.dialogFormVisible[index].B = false
      } else {
        this.dialogFormVisible[index].B = true
      }
    },
    Add() {
      if (this.mode === 'edit') {
        this.mode = 'add'
        this.form = { game_id: '', name: '', date: '', content: '', type: '2' }
      }
      this.Switch(0)
    },
    Edit(game_id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { game_id: '', name: '', date: '', content: '', type: '2' }
      this.Switch(0)
      const form = this.list.filter(array => {
        return array.game_id === game_id
      })[0]
      this.form = JSON.parse(JSON.stringify(form))
    },
    Insert() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch(0)
          insertGame(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { game_id: '', name: '', date: '', content: '', type: '2' }
              this.fetchData()
            } else {
              this.resError(res.message)
            }
          }).catch(error => {
            alert(error)
          })
        } else {
          this.resError('請注意表單格式!')
          return false
        }
      })
    },
    Update() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch(0)
          updateGame(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { game_id: '', name: '', date: '', content: '', type: '2' }
              this.fetchData()
            } else {
              this.resError(res.message)
            }
          }).catch(error => {
            alert(error)
          })
        } else {
          this.resError('請注意表單格式!')
          return false
        }
      })
    },
    Delete(game_id) {
      this.$confirm(`確定要刪除嗎？`)
        .then(() => {
          this.form = this.list.filter(array => {
            return array.game_id === game_id
          })[0]
          deleteGame(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { game_id: '', name: '', date: '', content: '', type: '2' }
              this.fetchData()
            } else {
              this.resError(res.message)
            }
          })
        }).catch(() => {
        })
    },
    EditPlayer(game_id) {
      // 分頁歸零
      this.page[1].currpage = 1
      this.page[2].currpage = 1

      // PLAYERNOTINGAME
      const game = this.fullList.find(d => parseInt(d.game_id) === parseInt(game_id))
      this.game.game_id = game.game_id
      this.game.name = game.name

      this.PlayerInGame = this.FullPlayerInGame.filter(d => parseInt(d.game_id) === parseInt(game_id))
      this.PlayerNotInGame = []
      if (this.PlayerInGame.length > 0) {
        this.Player.forEach(item => {
          const _in = this.PlayerInGame.find(d => parseInt(d.player_id) === parseInt(item.player_id))

          if (_in === undefined) {
            this.PlayerNotInGame.push(item)
          }
        })
      } else {
        this.PlayerNotInGame = JSON.parse(JSON.stringify(this.Player))
      }
      this.Switch(1)
    },
    getPlayer() {
      getGamePlayer().then(res => {
        if (res.code === 200) {
          const tmp = (res.data === null) ? [] : JSON.parse(JSON.stringify(res.data))
          this.FullPlayerInGame = tmp

          this.PlayerInGame = this.FullPlayerInGame.filter(d => parseInt(d.game_id) === parseInt(this.game.game_id))
          this.PlayerNotInGame = []
          if (this.PlayerInGame.length > 0) {
            this.Player.forEach(item => {
              const _in = this.PlayerInGame.find(d => parseInt(d.player_id) === parseInt(item.player_id))
              if (_in === undefined) {
                this.PlayerNotInGame.push(item)
              }
            })
          } else {
            this.PlayerNotInGame = JSON.parse(JSON.stringify(this.Player))
          }
        } else {
          this.resError(res.message)
        }
      })
    },
    InsertPlayer(player_id) {
      this.$confirm(`確定要新增到該比賽嗎？`)
        .then(() => {
          const form = {
            player_id: player_id,
            game_id: this.game.game_id
          }
          insertGamePlayer(form).then(res => {
            console.log(1)
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.getPlayer()
            } else {
              this.resError(res.message)
            }
          })
        }).catch(() => {
        })
    },
    DeletePlayer(player_id) {
      this.$confirm(`確定要刪除比賽選手嗎？`)
        .then(() => {
          const form = {
            player_id: player_id,
            game_id: this.game.game_id
          }
          deleteGamePlayer(form).then(res => {
            console.log(1)
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.getPlayer()
            } else {
              this.resError(res.message)
            }
          })
        }).catch(() => {
        })
    },
    EditJudger(game_id) {
      this.page[3].currpage = 1
      this.page[4].currpage = 1
      // JUDGERNOTINGAME
      const game = this.fullList.find(d => parseInt(d.game_id) === parseInt(game_id))
      this.game.game_id = game.game_id
      this.game.name = game.name

      this.JudgerInGame = this.FullJudgerInGame.filter(d => parseInt(d.game_id) === parseInt(game_id))
      this.JudgerNotInGame = []
      if (this.JudgerInGame.length > 0) {
        this.Judger.forEach(item => {
          const _in = this.JudgerInGame.find(d => parseInt(d.judger_id) === parseInt(item.judger_id))
          if (_in === undefined) {
            this.JudgerNotInGame.push(item)
          }
        })
      } else {
        this.JudgerNotInGame = JSON.parse(JSON.stringify(this.Judger))
      }

      this.Switch(2)
    },
    getJudger() {
      getGameJudger().then(res => {
        if (res.code === 200) {
          const tmp = (res.data === null) ? [] : JSON.parse(JSON.stringify(res.data))
          this.FullJudgerInGame = tmp

          this.JudgerInGame = this.FullJudgerInGame.filter(d => parseInt(d.game_id) === parseInt(this.game.game_id))
          this.JudgerNotInGame = []
          if (this.JudgerInGame.length > 0) {
            this.Judger.forEach(item => {
              const _in = this.JudgerInGame.find(d => parseInt(d.judger_id) === parseInt(item.judger_id))
              if (_in === undefined) {
                this.JudgerNotInGame.push(item)
              }
            })
          } else {
            this.JudgerNotInGame = JSON.parse(JSON.stringify(this.Judger))
          }
        } else {
          this.resError(res.message)
        }
      })
    },
    InsertJudger(judger_id) {
      this.$confirm(`確定要新增到該比賽嗎？`)
        .then(() => {
          if (this.JudgerInGame.length < 5) {
            const form = {
              judger_id: judger_id,
              game_id: this.game.game_id
            }
            insertGameJudger(form).then(res => {
              if (res.code === 200) {
                this.resSuccess(res.message)
                this.getJudger()
              } else {
                this.resError(res.message)
              }
            })
          } else {
            this.resError('裁判新增不可超過五位!')
          }
        }).catch(() => {
        })
    },
    DeleteJudger(judger_id) {
      this.$confirm(`確定要刪除比賽裁判嗎？`)
        .then(() => {
          const form = {
            judger_id: judger_id,
            game_id: this.game.game_id
          }
          deleteGameJudger(form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.getJudger()
            } else {
              this.resError(res.message)
            }
          })
        }).catch(() => {
        })
    },
    resSuccess(title, message = '') {
      this.$notify({
        title: title,
        message: message,
        type: 'success',
        duration: 1500
      })
    },
    resError(title, message) {
      this.$notify({
        title: title,
        message: message,
        type: 'error',
        duration: 1500
      })
    }
  }
}
</script>

<style lang="css">
.el-table .cell {
  white-space: pre-line;
}
.el-dialog__body {
    padding: 10px 20px;
}
</style>
