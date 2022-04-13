<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增收費標準</el-button>
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
        prop="item"
        label="項目"
      />
      <el-table-column
        align="center"
        prop="standard"
        label="標準"
      />
      <el-table-column
        class-name="status-col"
        label="狀態"
        width="110"
        align="center"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.status | statusFilter">{{ scope.row.status }}</el-tag>
        </template>
      </el-table-column>
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
            @click="Edit(scope.row.id)"
          />
          <el-button
            type="danger"
            icon="el-icon-delete"
            circle
            @click="Delete(scope.row.id)"
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
    <el-dialog title="收費標準管理" :visible.sync="dialogFormVisible">
      <el-form ref="form" :model="form" :rules="rules">
        <el-form-item label="項目" prop="item">
          <el-input
            v-model="form.item"
            autocomplete="off"
            placeholder="項目"
          />
        </el-form-item>
        <el-form-item label="標準" prop="standard">
          <el-input
            v-model="form.standard"
            autocomplete="off"
            type="textarea"
            placeholder="標準"
            :rows="8"
          />
        </el-form-item>
        <el-form-item label="狀態">
          <el-select v-model="form.status" placeholder="狀態">
            <el-option label="發布" value="published" />
            <el-option label="草稿" value="draft" />
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
  </div>
</template>

<script>
import { getService,
  insertService,
  updateService,
  deleteService } from '@/api/service'
import { clearCache } from '@/api/clear'

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
        id: '',
        item: '',
        standard: '',
        orders: 0,
        status: 'published'
      },
      dialogFormVisible: false,
      mode: 'add',
      rules: {
        item: [
          { required: true, message: '請輸入服務項目!', trigger: 'blur' },
          { max: 64, message: '長度不能超過64個字!', trigger: 'blur' }
        ],
        standard: [
          { required: true, message: '請輸入標準!', trigger: 'blur' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'blur' }
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
      getService().then(response => {
        const data = (response.data === null) ? [] : JSON.parse(JSON.stringify(response.data))
        clearCache()
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
        this.list = this.fullList.filter(array => array.item.match(this.searchMap.word) || array.standard.match(this.searchMap.word))
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
        this.form = { id: '', item: '', standard: '', orders: 0, status: 'published' }
      }
      this.Switch()
    },
    Edit(id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { id: '', item: '', standard: '', orders: 0, status: 'published' }
      this.Switch()
      const form = this.list.filter(array => {
        return array.id === id
      })[0]
      this.form = JSON.parse(JSON.stringify(form))
    },
    Insert() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch()
          insertService(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', item: '', standard: '', orders: 0, status: 'published' }
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
          updateService(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', item: '', standard: '', orders: 0, status: 'published' }
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
    Delete(id) {
      this.$confirm(`確定要刪除嗎？`)
        .then(() => {
          this.form = this.list.filter(array => {
            return array.id === id
          })[0]
          deleteService(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', item: '', standard: '', orders: 0, status: 'published' }
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
