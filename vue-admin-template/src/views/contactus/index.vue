<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增公司</el-button>
      </el-col>
      <el-col>
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item label="搜尋">
            <el-input v-model="searchMap.word" placeholder="公司名稱 統編..." />
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
        prop="department"
        label="公司"
      />
      <el-table-column
        align="center"
        prop="tel"
        label="電話"
      />
      <el-table-column
        align="center"
        prop="tax"
        label="傳真"
      />
      <el-table-column
        align="center"
        prop="addr"
        label="地址"
      />
      <el-table-column
        align="center"
        prop="addr_map"
        label="地址連結"
        width="200"
      >
        <template slot-scope="scope">
          <el-link type="info" :href="scope.row.addr_map" target="_blank">{{ scope.row.addr_map }}</el-link>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="email"
        label="email"
      />
      <el-table-column
        align="center"
        prop="tax_id"
        label="統編"
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
    <el-dialog title="公司管理" :visible.sync="dialogFormVisible">
      <el-form ref="form" :model="form" :inline="true" label-position="top" :rules="rules">
        <el-form-item label="公司" prop="department">
          <el-input
            v-model="form.department"
            autocomplete="off"
            placeholder="公司"
          />
        </el-form-item>
        <el-form-item label="電話" prop="tel">
          <el-input
            v-model="form.tel"
            autocomplete="off"
            placeholder="電話"
          />
        </el-form-item>
        <el-form-item label="傳真" prop="tax">
          <el-input
            v-model="form.tax"
            autocomplete="off"
            placeholder="傳真"
          />
        </el-form-item>
        <el-form-item label="地址" prop="addr">
          <el-input
            v-model="form.addr"
            autocomplete="off"
            placeholder="地址"
          />
        </el-form-item>
        <el-form-item label="地址連結" prop="addr_map">
          <el-input
            v-model="form.addr_map"
            autocomplete="off"
            placeholder="地址連結"
          />
        </el-form-item>
        <el-form-item label="信箱" prop="email">
          <el-input
            v-model="form.email"
            autocomplete="off"
            placeholder="信箱"
          />
        </el-form-item>
        <el-form-item label="統編" prop="tax_id">
          <el-input
            v-model="form.tax_id"
            autocomplete="off"
            placeholder="統編"
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
import { getContactUS,
  insertContactUS,
  updateContactUS,
  deleteContactUS } from '@/api/contactus'
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
        department: '',
        tel: '',
        tax: '',
        addr: '',
        addr_map: '',
        email: '',
        tax_id: '',
        orders: 0,
        status: 'published'
      },
      dialogFormVisible: false,
      mode: 'add',
      rules: {
        department: [
          { required: true, message: '請輸入公司!', trigger: 'blur' },
          { max: 64, message: '長度不能超過64個字!', trigger: 'blur' }
        ],
        tel: [
          { required: true, message: '請輸入公司電話!', trigger: 'blur' },
          { max: 15, message: '長度不能超過15個字!', trigger: 'blur' }
        ],
        tax: [
          { required: true, message: '請輸入公司傳真!', trigger: 'blur' },
          { max: 20, message: '長度不能超過20個字!', trigger: 'blur' }
        ],
        addr: [
          { required: true, message: '請輸入公司地址!', trigger: 'blur' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        addr_map: [
          { max: 256, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        email: [
          { required: true, message: '請輸入公司Email!', trigger: 'blur' },
          { max: 128, message: '長度不能超過128個字!', trigger: 'blur' }
        ],
        tax_id: [
          { required: true, message: '請輸入公司統編!', trigger: 'blur' },
          { max: 20, message: '長度不能超過20個字!', trigger: 'blur' }
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
      getContactUS().then(response => {
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
        this.list = this.fullList.filter(array => array.department.match(this.searchMap.word) || array.tax_id.match(this.searchMap.word))
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
        this.form = { id: '', department: '', tel: '', tax: '', addr: '', addr_map: '', email: '', tax_id: '', orders: 0, status: 'published' }
      }
      this.Switch()
    },
    Edit(id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { id: '', department: '', tel: '', tax: '', addr: '', addr_map: '', email: '', tax_id: '', orders: 0, status: 'published' }
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
          insertContactUS(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', department: '', tel: '', tax: '', addr: '', addr_map: '', email: '', tax_id: '', orders: 0, status: 'published' }
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
          updateContactUS(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', department: '', tel: '', tax: '', addr: '', addr_map: '', email: '', tax_id: '', orders: 0, status: 'published' }
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
          deleteContactUS(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', department: '', tel: '', tax: '', addr: '', addr_map: '', email: '', tax_id: '', orders: 0, status: 'published' }
              this.fetchData()
            } else {
              this.resError(res.message)
            }
          }).catch(error => {
            alert(error)
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
