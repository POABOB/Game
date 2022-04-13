<template>
  <div class="app-container">
    <!-- <el-row :gutter="20">
      <el-col>
        <el-button type="info" @click="Add">新增圖片</el-button>
      </el-col>
    </el-row> -->

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
        prop="img"
        label="圖片1"
        align="center"
      >
        <template slot-scope="scope">
          <div class="demo-image__preview">
            <el-image
              style="width: 100px; height: 100px"
              :src="staticPath+scope.row.img"
              :preview-src-list="[staticPath+scope.row.img]"
            />
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="img"
        label="圖片2"
        align="center"
      >
        <template slot-scope="scope">
          <div v-show="scope.row.img2.length > 5" class="demo-image__preview">
            <el-image
              style="width: 100px; height: 100px"
              :src="staticPath+scope.row.img2"
              :preview-src-list="[staticPath+scope.row.img2]"
            />
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="small_img"
        label="圖片(mobile)"
        align="center"
      >
        <template slot-scope="scope">
          <div v-show="scope.row.small_img.length > 5" class="demo-image__preview">
            <el-image
              style="width: 100px; height: 100px"
              :src="staticPath+scope.row.small_img"
              :preview-src-list="[staticPath+scope.row.small_img]"
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
          <!-- <el-button
            type="danger"
            icon="el-icon-delete"
            circle
            @click="Delete(scope.row.id)"
          /> -->
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-show="false"
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
    <el-dialog title="首頁管理" :visible.sync="dialogFormVisible">
      <el-alert
        title="列對應首頁圖片區塊，圖片1 就是橫幅大圖圖片1 ＋ 圖片2 是左右兩欄，圖片 mobile 是手機版圖片（非必要）"
        type="info"
        :closable="false"
      />
      <el-form ref="form" :model="form" :top="true" :rules="rules">
        <el-form-item label="圖片1" prop="img">
          <el-input
            v-model="form.img"
            autocomplete="off"
            :disabled="fileList.length === 1"
            placeholder="圖片1"
          />
          <el-upload
            class="upload-demo"
            :action="uploadURL+type"
            name="upload"
            :on-success="handleSuccess"
            :on-remove="handleRemove"
            :before-remove="beforeRemove"
            :limit="1"
            :file-list="fileList"
            :headers="headers"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片2" prop="img2">
          <el-input
            v-model="form.img2"
            autocomplete="off"
            :disabled="fileList2.length === 1"
            placeholder="圖片2"
          />
          <el-upload
            class="upload-demo"
            :action="uploadURL+type"
            name="upload"
            :on-success="handleSuccess2"
            :on-remove="handleRemove2"
            :before-remove="beforeRemove"
            :limit="1"
            :file-list="fileList2"
            :headers="headers"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片(mobile)" prop="small_img">
          <el-input
            v-model="form.small_img"
            autocomplete="off"
            :disabled="fileList3.length === 1"
            placeholder="圖片(mobile)"
          />
          <el-upload
            class="upload-demo"
            :action="uploadURL+type"
            name="upload"
            :on-success="handleSuccess3"
            :on-remove="handleRemove3"
            :before-remove="beforeRemove"
            :limit="1"
            :file-list="fileList3"
            :headers="headers"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
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
import { getHome,
  insertHome,
  updateHome,
  deleteHome,
  removeHomeFile } from '@/api/home'
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
      fullList: [],
      localFile: [],
      listLoading: true,
      pagesize: 5,
      currpage: 1,
      form: {
        id: 0,
        img: '',
        img2: '',
        small_img: '',
        status: 'published'
      },
      dialogFormVisible: false,
      mode: 'add',
      fileList: [],
      fileList2: [],
      fileList3: [],
      type: 'home',
      uploadURL: '/api/admin/file/upload?type=',
      headers: {
        'X-Token': getToken()
      },
      staticPath: process.env.VUE_APP_STATIC_PATH,
      rules: {
        img: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        small_img: [
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ]
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      getHome().then(response => {
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
    handleSuccess(response, file, fileList) {
      this.form.img = response.data.path
      this.fileList = fileList
    },
    handleRemove(file, fileList) {
      const form = {}
      form['file'] = this.form.img
      removeHomeFile(form).then(res => {
        if (res.code === 200) {
          if (this.mode === 'edit') {
            const l = this.localFile.length
            for (let i = 0; i < l; i++) {
              if (this.localFile[i].path === this.form.img) {
                this.localFile.splice(i, 1)
              }
            }
          }
          this.form.img = ''
          this.fileList = []
        }
      })
    },
    handleSuccess2(response, file, fileList) {
      this.form.img2 = response.data.path
      this.fileList2 = fileList
    },
    handleRemove2(file, fileList) {
      const form = {}
      form['file'] = this.form.img2
      removeHomeFile(form).then(res => {
        if (res.code === 200) {
          if (this.mode === 'edit') {
            const l = this.localFile.length
            for (let i = 0; i < l; i++) {
              if (this.localFile[i].path === this.form.img2) {
                this.localFile.splice(i, 1)
              }
            }
          }
          this.form.img2 = ''
          this.fileList2 = []
        }
      })
    },
    handleSuccess3(response, file, fileList) {
      this.form.small_img = response.data.path
      this.fileList3 = fileList
    },
    handleRemove3(file, fileList) {
      const form = {}
      form['file'] = this.form.small_img
      removeHomeFile(form).then(res => {
        if (res.code === 200) {
          if (this.mode === 'edit') {
            const l = this.localFile.length
            for (let i = 0; i < l; i++) {
              if (this.localFile[i].path === this.form.small_img) {
                this.localFile.splice(i, 1)
              }
            }
          }
          this.form.small_img = ''
          this.fileList3 = []
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
        this.form = { id: 0, img: '', img2: '', small_img: '', status: 'published' }
        this.fileList = []
        this.fileList2 = []
        this.fileList3 = []
      }
      this.Switch()
    },
    Edit(id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { id: 0, img: '', img2: '', small_img: '', status: 'published' }
      this.fileList = []
      this.fileList2 = []
      this.fileList3 = []
      this.Switch()
      const form = this.list.filter(array => {
        return parseInt(array.id) === parseInt(id)
      })[0]
      this.form = JSON.parse(JSON.stringify(form))

      const file = this.localFile.filter(array => {
        return array.path === this.form.img
      })[0]
      if (file) {
        this.fileList.push(file)
      }

      const file2 = this.localFile.filter(array => {
        return array.path === this.form.img2
      })[0]
      if (file2) {
        this.fileList2.push(file2)
      }

      const file3 = this.localFile.filter(array => {
        return array.path === this.form.small_img
      })[0]
      if (file3) {
        this.fileList3.push(file3)
      }
    },
    Insert() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch()
          insertHome(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: 0, img: '', img2: '', small_img: '', status: 'published' }
              this.fileList = []
              this.fileList2 = []
              this.fileList3 = []
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
          updateHome(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: 0, img: '', img2: '', small_img: '', status: 'published' }
              this.fileList = []
              this.fileList2 = []
              this.fileList3 = []
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
          deleteHome(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: 0, img: '', img2: '', small_img: '', status: 'published' }
              this.fileList = []
              this.fileList2 = []
              this.fileList3 = []
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
