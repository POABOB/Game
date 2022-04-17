<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增選手</el-button>
      </el-col>
      <el-col>
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item label="搜尋">
            <el-input v-model="searchMap.word" placeholder="姓名 單位..." />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" icon="el-icon-search" @click="Search">查詢</el-button>
          </el-form-item>
        </el-form>

      </el-col>
    </el-row>

    <el-table
      v-loading="listLoading"
      :data="list.slice((currpage - 1) * pagesize, currpage * pagesize)"
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
          {{ (scope.$index + (currpage - 1) * pagesize) + 1 }}
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="name"
        label="姓名"
      />
      <el-table-column
        align="center"
        prop="unit"
        label="單位"
      />
      <el-table-column
        align="center"
        prop="comment"
        label="備註"
      />
      <el-table-column
        align="center"
        label="操作"
        width="115"
      >
        <template slot-scope="scope">
          <el-button
            type="info"
            icon="el-icon-edit"
            circle
            @click="Edit(scope.row.player_id)"
          />
          <el-button
            type="danger"
            icon="el-icon-delete"
            circle
            @click="Delete(scope.row.player_id)"
          />
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      background
      layout="prev, pager, next, sizes, total, jumper"
      align="center"
      :page-sizes="[5, 10, 15, 20]"
      :page-size="pagesize"
      :total="list.length"
      @current-change="handleCurrentChange"
      @size-change="handleSizeChange"
    />

    <!-- Form -->
    <el-dialog title="選手管理" :visible.sync="dialogFormVisible">
      <el-form ref="form" :model="form" :rules="rules">
        <el-form-item label="姓名" prop="name">
          <el-input
            v-model="form.name"
            autocomplete="off"
            placeholder="姓名"
          />
        </el-form-item>
        <el-form-item label="單位" prop="unit">
          <el-input
            v-model="form.unit"
            autocomplete="off"
            placeholder="單位"
          />
        </el-form-item>
        <el-form-item label="備註" prop="comment">
          <el-input
            v-model="form.comment"
            autocomplete="off"
            type="textarea"
            placeholder="備註"
            :rows="8"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button
          @click="dialogFormVisible = false"
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
  </div>
</template>

<script>
import { getPlayer,
  insertPlayer,
  updatePlayer,
  deletePlayer } from '@/api/player'
export default {
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'gray'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      list: [],
      fullList: [],
      listLoading: true,
      pagesize: 5,
      currpage: 1,
      searchMap: {
        word: null
      },
      form: {
        player_id: '',
        name: '',
        unit: '',
        comment: ''
      },
      dialogFormVisible: false,
      mode: 'add',
      rules: {
        name: [
          { required: true, message: '請輸入姓名!', trigger: 'blur' },
          { max: 128, message: '長度不能超過128個字!', trigger: 'blur' }
        ],
        unit: [
          { required: true, message: '請輸入單位!', trigger: 'blur' },
          { max: 128, message: '長度不能超過128個字!', trigger: 'blur' }
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
      getPlayer().then(response => {
        const data = (response.data === null) ? [] : JSON.parse(JSON.stringify(response.data))
        this.list = data
        this.fullList = data
        this.listLoading = false
      }).catch(error => {
        alert(error)
        this.listLoading = false
      })
    },
    handleCurrentChange(cpage) {
      this.currpage = cpage
    },
    handleSizeChange(psize) {
      this.pagesize = psize
    },
    Search() {
      this.listLoading = true
      if (this.searchMap.word !== null) {
        this.list = this.fullList.filter(array => array.name.match(this.searchMap.word) || array.unit.match(this.searchMap.word))
      } else {
        this.list = this.fullList
      }
      this.listLoading = false
    },
    Switch() {
      if (this.dialogFormVisible) {
        this.dialogFormVisible = false
      } else {
        this.dialogFormVisible = true
      }
    },
    Add() {
      if (this.mode === 'edit') {
        this.mode = 'add'
        this.form = { player_id: '', name: '', unit: '', comment: '' }
      }
      this.Switch()
    },
    Edit(player_id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { player_id: '', name: '', unit: '', comment: '' }
      this.Switch()
      const form = this.list.filter(array => {
        return array.player_id === player_id
      })[0]
      this.form = JSON.parse(JSON.stringify(form))
    },
    Insert() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch()
          insertPlayer(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { player_id: '', name: '', unit: '', comment: '' }
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
          this.Switch()
          updatePlayer(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { player_id: '', name: '', unit: '', comment: '' }
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
    Delete(player_id) {
      this.$confirm(`確定要刪除嗎？`)
        .then(() => {
          this.form = this.list.filter(array => {
            return array.player_id === player_id
          })[0]
          console.log(this.form)
          deletePlayer(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { player_id: '', name: '', unit: '', comment: '' }
              this.fetchData()
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
</style>
