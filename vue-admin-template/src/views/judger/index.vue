<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增裁判</el-button>
      </el-col>
      <el-col>
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item label="搜尋">
            <el-input v-model="searchMap.word" placeholder="項目 標準..." />
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
        prop="ID"
        label="ID"
      />
      <el-table-column
        align="center"
        prop="phone"
        label="電話"
      />
      <el-table-column
        class-name="status-col"
        label="角色"
        width="110"
        align="center"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.right | statusFilter">{{ (scope.row.right === "1") ? '裁判長' : '裁判' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
        width="180"
      >
        <template slot-scope="scope">
          <el-button
            type="info"
            icon="el-icon-edit"
            circle
            @click="Edit(scope.row.judger_id)"
          />
          <el-button
            type="warning"
            icon="el-icon-key"
            circle
            @click="EditPassword(scope.row.judger_id)"
          />
          <el-button
            type="danger"
            icon="el-icon-delete"
            circle
            @click="Delete(scope.row.judger_id)"
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
    <el-dialog title="裁判管理" :visible.sync="dialogFormVisible">
      <el-form ref="form" :model="form" :rules="rules">
        <el-form-item label="姓名" prop="name">
          <el-input
            v-model="form.name"
            autocomplete="off"
            placeholder="姓名"
          />
        </el-form-item>
        <el-form-item label="ID" prop="ID">
          <el-input
            v-model="form.ID"
            placeholder="ID"
          />
        </el-form-item>
        <el-form-item label="電話" prop="phone">
          <el-input
            v-model="form.phone"
            placeholder="電話"
          />
        </el-form-item>
        <el-form-item v-show="mode === 'add'" label="密碼" prop="password">
          <el-input
            v-model="form.password"
            show-password
            placeholder="密碼"
          />
        </el-form-item>
        <el-form-item v-show="mode === 'add'" label="密碼確認" prop="passconf">
          <el-input
            v-model="form.passconf"
            show-password
            placeholder="密碼確認"
          />
        </el-form-item>
        <el-form-item label="角色">
          <el-select v-model="form.right" placeholder="角色">
            <el-option label="裁判" value="0" />
            <el-option label="裁判長" value="1" />
          </el-select>
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

    <!-- passwordForm -->
    <el-dialog title="密碼管理" :visible.sync="dialogFormVisible2">
      <el-form ref="form" :model="form" :rules="rules">
        <el-form-item label="姓名" prop="name">
          <el-input
            v-model="form.name"
            autocomplete="off"
            placeholder="姓名"
            disabled
          />
        </el-form-item>

        <el-form-item label="密碼" prop="password">
          <el-input
            v-model="form.password"
            show-password
            placeholder="密碼"
          />
        </el-form-item>
        <el-form-item label="密碼確認" prop="passconf">
          <el-input
            v-model="form.passconf"
            show-password
            placeholder="密碼確認"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button
          @click="dialogFormVisible2 = false"
        >
          取 消
        </el-button>
        <el-button
          type="info"
          @click="UpdatePassword"
        >
          更新
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getJudger,
  insertJudger,
  updateJudger,
  deleteJudger,
  updateJudgerPassword } from '@/api/judger'

export default {
  filters: {
    statusFilter(status) {
      const statusMap = { '0': 'success', '1': 'gray' }
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
        judger_id: '',
        name: '',
        ID: '',
        password: '',
        passconf: '',
        phone: '',
        right: '0'
      },
      dialogFormVisible: false,
      dialogFormVisible2: false,
      mode: 'add',
      rules: {
        name: [
          { required: true, message: '請輸入姓名!', trigger: 'blur' },
          { max: 128, message: '長度不能超過128個字!', trigger: 'blur' }
        ],
        ID: [
          { required: true, message: '請輸入ID!', trigger: 'blur' },
          { max: 10, message: '長度不能超過10個字!', trigger: 'blur' }
        ],
        phone: [
          { required: true, message: '請輸入電話!', trigger: 'blur' },
          { max: 15, message: '長度不能超過15個字!', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '請輸入密碼!', trigger: 'blur' },
          { max: 64, message: '長度不能超過64個字!', trigger: 'blur' }
        ],
        passconf: [
          { required: true, message: '請輸入密碼確認!', trigger: 'blur' },
          { max: 64, message: '長度不能超過64個字!', trigger: 'blur' }
        ],
        right: [
          { required: true, message: '請選擇角色!', trigger: 'blur' }
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
      getJudger().then(response => {
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
        this.list = this.fullList.filter(array => array.name.match(this.searchMap.word) || array.ID.match(this.searchMap.word) || array.phone.match(this.searchMap.word))
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
    Switch2() {
      if (this.dialogFormVisible2) {
        this.dialogFormVisible2 = false
      } else {
        this.dialogFormVisible2 = true
      }
    },
    Add() {
      if (this.mode === 'edit') {
        this.mode = 'add'
        this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
      }
      this.Switch()
    },
    Edit(judger_id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
      this.Switch()
      const form = this.list.filter(array => {
        return array.judger_id === judger_id
      })[0]
      this.form = JSON.parse(JSON.stringify(form))
      this.form.password = '0'
      this.form.passconf = '0'
    },
    EditPassword(judger_id) {
      this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
      this.Switch2()
      const form = this.list.filter(array => {
        return array.judger_id === judger_id
      })[0]
      this.form = JSON.parse(JSON.stringify(form))
    },
    Insert() {
      if (this.form.password === this.form.passconf) {
        this.$refs.form.validate((valid) => {
          if (valid) {
            this.Switch()
            insertJudger(this.form).then(res => {
              if (res.code === 200) {
                this.resSuccess(res.message)
                this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
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
      } else {
        this.resError('密碼確認不相符')
      }
    },
    Update() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch()
          updateJudger(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
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
    UpdatePassword() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch2()
          updateJudgerPassword(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
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
    Delete(judger_id) {
      this.$confirm(`確定要刪除嗎？`)
        .then(() => {
          this.form = this.list.filter(array => {
            return array.judger_id === judger_id
          })[0]
          deleteJudger(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { judger_id: '', name: '', ID: '', phone: '', password: '', passconf: '', right: '0' }
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
