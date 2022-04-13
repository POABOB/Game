<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增團隊成員</el-button>
      </el-col>
      <el-col>
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item label="搜尋">
            <el-input v-model="searchMap.word" placeholder="職稱 名字..." />
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
        prop="position"
        label="職稱"
      />
      <el-table-column
        prop="name"
        label="名字"
        align="center"
      />
      <el-table-column
        prop="avatar"
        label="照片"
        align="center"
      >
        <template slot-scope="scope">
          <div class="demo-image__preview">
            <el-image
              style="width: 90px; height: 135px"
              :src="staticPath+scope.row.avatar"
              :preview-src-list="[staticPath+scope.row.avatar]"
            />
          </div>
        </template>
      </el-table-column>
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
    <el-dialog title="團隊管理" :visible.sync="dialogFormVisible">
      <el-form ref="form" :model="form" :top="true" :rules="rules">
        <el-form-item label="職稱" prop="position">
          <el-input
            v-model="form.position"
            autocomplete="off"
            placeholder="職稱"
          />
        </el-form-item>
        <el-form-item label="名字" prop="name">
          <el-input
            v-model="form.name"
            autocomplete="off"
            placeholder="名字"
          />
        </el-form-item>
        <el-form-item label="照片" prop="avatar">
          <el-input
            v-model="form.avatar"
            autocomplete="off"
            :disabled="fileList.length === 1"
            placeholder="照片"
          />
          <el-upload
            class="upload-demo"
            :action="uploadURL + type"
            name="upload"
            :on-success="handleSuccess"
            :on-remove="handleRemove"
            :before-remove="beforeRemove"
            :limit="1"
            :file-list="fileList"
            :headers="headers"
          >
            <div slot="tip" class="el-upload__tip">只能上傳jpg/png文件，大小比例建議為90 x 135</div>
            <el-button slot="trigger" size="small" type="primary">選取照片</el-button>
          </el-upload>
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
import { getAboutUSMember,
  insertAboutUSMember,
  updateAboutUSMember,
  deleteAboutUSMember,
  removeAboutUSMemberFile } from '@/api/aboutus'
import { clearCache } from '@/api/clear'
import { getToken } from '@/utils/auth'
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
      listLength: 0,
      fullList: [],
      localFile: [],
      listLoading: true,
      pagesize: 5,
      currpage: 1,
      searchMap: {
        word: null
      },
      form: {
        id: '',
        position: '',
        name: '',
        avatar: '',
        orders: 0,
        status: 'published'
      },
      dialogFormVisible: false,
      mode: 'add',
      fileList: [],
      type: 'aboutus_member',
      uploadURL: '/api/admin/file/upload?type=',
      headers: {
        'X-Token': getToken()
      },
      staticPath: process.env.VUE_APP_STATIC_PATH,
      rules: {
        position: [
          { required: true, message: '請輸入服務項目!', trigger: 'blur' },
          { max: 64, message: '長度不能超過64個字!', trigger: 'blur' }
        ],
        name: [
          { required: true, message: '請輸入標準!', trigger: 'blur' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        avatar: [
          { required: true, message: '請輸入或上傳頭像!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
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
      getAboutUSMember().then(response => {
        const data = (response.data === null) ? [] : JSON.parse(JSON.stringify(response.data))
        clearCache()
        this.list = data[0]
        this.fullList = data[0]
        this.localFile = data[1]
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
      if (this.searchMap.name !== null) {
        this.list = this.fullList.filter(array => array.position.match(this.searchMap.word) || array.name.match(this.searchMap.word))
      } else {
        this.list = this.fullList
      }
      this.listLoading = false
    },
    handleSuccess(response, file, fileList) {
      this.form.avatar = response.data.path
      this.fileList = fileList
    },
    handleRemove(file, fileList) {
      const form = {}
      form['file'] = this.form.avatar
      removeAboutUSMemberFile(form).then(res => {
        if (res.code === 200) {
          if (this.mode === 'edit') {
            const l = this.localFile.length
            for (let i = 0; i < l; i++) {
              if (this.localFile[i].path === this.form.avatar) {
                this.localFile.splice(i, 1)
              }
            }
          }
          this.form.avatar = ''
          this.fileList = []
        }
      })
    },
    beforeRemove(file, fileList) {
      return this.$confirm(`do you really want to delete ${file.name}？`)
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
        this.form = { id: '', position: '', name: '', avatar: '', status: 'published' }
        this.fileList = []
      }
      this.Switch()
    },
    Edit(id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { id: '', position: '', name: '', avatar: '', status: 'published' }
      this.fileList = []
      this.Switch()
      const form = this.list.filter(array => {
        return parseInt(array.id) === parseInt(id)
      })[0]
      this.form = JSON.parse(JSON.stringify(form))

      const file = this.localFile.filter(array => {
        return array.path === this.form.avatar
      })[0]
      if (file) {
        this.fileList.push(file)
      }
    },
    Insert() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch()
          insertAboutUSMember(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', position: '', name: '', avatar: '', status: 'published' }
              this.fileList = []
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
          updateAboutUSMember(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', position: '', name: '', avatar: '', status: 'published' }
              this.fileList = []
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
          deleteAboutUSMember(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: '', position: '', name: '', avatar: '', status: 'published' }
              this.fileList = []
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
