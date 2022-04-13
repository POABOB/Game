<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <el-button type="info" class="m" @click="Add">新增專案</el-button>
      </el-col>
      <el-col>
        <el-form :inline="true" class="demo-form-inline">
          <el-form-item label="搜尋">
            <el-input v-model="searchMap.word" placeholder="專案名稱 描述" />
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
        label="專案名稱"
      />
      <el-table-column
        label="專案類型"
        align="center"
      >
        <template slot-scope="scope">
          {{ getType(scope.row.type_id) }}
        </template>
      </el-table-column>
      <el-table-column
        prop="description"
        label="描述"
        align="center"
      />
      <el-table-column
        prop="cover"
        label="封面"
        align="center"
      >
        <template slot-scope="scope">
          <div class="demo-image__preview">
            <el-image
              style="width: 150px; height: 100px"
              :src="staticPath+scope.row.cover"
              :preview-src-list="[staticPath+scope.row.cover]"
            />
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="img1"
        label="圖片"
        align="center"
      >
        <template slot-scope="scope">
          <div class="demo-image__preview">
            <el-image
              style="width: 150px; height: 100px"
              :src="staticPath+scope.row.img1"
              :preview-src-list="[staticPath+scope.row.img1,staticPath+scope.row.img2,staticPath+scope.row.img3,staticPath+scope.row.img4,staticPath+scope.row.img5,staticPath+scope.row.img6,staticPath+scope.row.img7,staticPath+scope.row.img8,staticPath+scope.row.img9,staticPath+scope.row.img10,staticPath+scope.row.img11]"
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
    <el-dialog title="專案管理" :visible.sync="dialogFormVisible">
      <el-form ref="form" :model="form" :rules="rules" :inline="true" label-position="top">
        <el-form-item label="專案名稱" prop="name">
          <el-input
            v-model="form.name"
            autocomplete="off"
            placeholder="專案名稱"
          />
        </el-form-item>
        <el-form-item label="描述" prop="description">
          <el-input
            v-model="form.description"
            autocomplete="off"
            placeholder="描述"
          />
        </el-form-item>
        <el-form-item label="專案類別">
          <el-select v-model="form.type_id" placeholder="專案類別">
            <el-option v-for="item in projectsType" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="封面" prop="cover">
          <el-input
            v-model="form.cover"
            autocomplete="off"
            :disabled="fileList[0].length === 1"
            placeholder="封面"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 0)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 0)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[0]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="Banner" prop="img1">
          <el-input
            v-model="form.img1"
            autocomplete="off"
            :disabled="fileList[1].length === 1"
            placeholder="Banner"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 1)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 1)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[1]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片1" prop="img2">
          <el-input
            v-model="form.img2"
            autocomplete="off"
            :disabled="fileList[2].length === 1"
            placeholder="圖片1"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 2)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 2)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[2]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片2" prop="img3">
          <el-input
            v-model="form.img3"
            autocomplete="off"
            :disabled="fileList[3].length === 1"
            placeholder="圖片2"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 3)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 3)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[3]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片3" prop="img4">
          <el-input
            v-model="form.img4"
            autocomplete="off"
            :disabled="fileList[4].length === 1"
            placeholder="圖片3"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 4)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 4)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[4]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片4" prop="img5">
          <el-input
            v-model="form.img5"
            autocomplete="off"
            :disabled="fileList[5].length === 1"
            placeholder="圖片4"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 5)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 5)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[5]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片5" prop="img6">
          <el-input
            v-model="form.img6"
            autocomplete="off"
            :disabled="fileList[6].length === 1"
            placeholder="圖片5"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 6)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 6)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[6]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片6" prop="img7">
          <el-input
            v-model="form.img7"
            autocomplete="off"
            :disabled="fileList[7].length === 1"
            placeholder="圖片6"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 7)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 7)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[7]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片7" prop="img8">
          <el-input
            v-model="form.img8"
            autocomplete="off"
            :disabled="fileList[8].length === 1"
            placeholder="圖片7"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 8)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 8)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[8]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片8" prop="img9">
          <el-input
            v-model="form.img9"
            autocomplete="off"
            :disabled="fileList[9].length === 1"
            placeholder="圖片8"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 9)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 9)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[9]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片9" prop="img10">
          <el-input
            v-model="form.img10"
            autocomplete="off"
            :disabled="fileList[10].length === 1"
            placeholder="圖片9"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 10)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 10)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[10]"
          >
            <el-button slot="trigger" size="small" type="primary">選取圖片</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="圖片10" prop="img11">
          <el-input
            v-model="form.img11"
            autocomplete="off"
            :disabled="fileList[11].length === 1"
            placeholder="圖片10"
          />
          <el-upload
            :action="uploadURL + type"
            name="upload"
            :on-success="(response, file, fileList) => {handleSuccess(response, file, fileList, 11)}"
            :on-remove="(file, fileList) => {handleRemove(file, fileList, 11)}"
            :before-remove="beforeRemove"
            :limit="1"
            :headers="headers"
            :file-list="fileList[11]"
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
import { getProjectsDetail,
  insertProjectsDetail,
  updateProjectsDetail,
  deleteProjectsDetail,
  removeProjectsDetailFile } from '@/api/projects'
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
      projectsType: [],
      listLoading: true,
      pagesize: 10,
      currpage: 1,
      searchMap: {
        date_start: null,
        date_end: null,
        title: null
      },
      form: {
        id: 0,
        name: '',
        type_id: '',
        description: '',
        cover: '',
        img1: '',
        img2: '',
        img3: '',
        img4: '',
        img5: '',
        img6: '',
        img7: '',
        img8: '',
        img9: '',
        img10: '',
        img11: '',
        orders: 0,
        status: 'published'
      },
      dialogFormVisible: false,
      mode: 'add',
      fileList: [[], [], [], [], [], [], [], [], [], [], [], [], []],
      type: 'projects_detail',
      uploadURL: '/api/admin/file/upload?type=',
      headers: {
        'X-Token': getToken()
      },
      staticPath: process.env.VUE_APP_STATIC_PATH,
      rules: {
        name: [
          { required: true, message: '請輸入專案名稱!', trigger: 'blur' },
          { max: 64, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        description: [
          { max: 1024, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        cover: [
          { required: true, message: '請輸入或上傳封面!', trigger: 'blur' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'blur' }
        ],
        img1: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img2: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img3: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img4: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img5: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img6: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img7: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img8: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img9: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img10: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ],
        img11: [
          { required: true, message: '請輸入或上傳圖片!', trigger: 'change' },
          { max: 256, message: '長度不能超過256個字!', trigger: 'change' }
        ]
      },
      imgs: ['cover', 'img1', 'img2', 'img3', 'img4', 'img5', 'img6', 'img7', 'img8', 'img9', 'img10', 'img11']
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
      getProjectsDetail().then(response => {
        const data = (response.data === null) ? [] : JSON.parse(JSON.stringify(response.data))
        clearCache()
        this.list = data[0]
        this.fullList = data[0]
        this.localFile = data[1]
        this.projectsType = data[2]
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
        this.list = this.fullList.filter(array => array.description.match(this.searchMap.word) || array.name.match(this.searchMap.word))
      } else {
        this.list = this.fullList
      }
      this.listLoading = false
    },
    handlePictureCardPreview(file, index) {
    },
    handleSuccess(response, file, fileList, index) {
      if (response.data) {
        this.form[this.imgs[parseInt(index)]] = response.data.path
      }
      this.fileList[parseInt(index)] = fileList
    },
    handleRemove(file, fileList, index) {
      const form = {}
      form['file'] = this.form[this.imgs[parseInt(index)]]
      removeProjectsDetailFile(form).then(res => {
        if (res.code === 200) {
          // if (this.mode === 'edit') {
          //   const l = this.localFile.length
          //   for (let i = 0; i < l; i++) {
          //     if (this.localFile[i].name === form['file']) {
          //       this.localFile.splice(i, 1)
          //     }
          //   }
          // }
          this.form[this.imgs[parseInt(index)]] = ''
          this.fileList[parseInt(index)] = []
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
        this.form = { id: 0, name: '', type_id: '', description: '', cover: '', img1: '', img2: '', img3: '', img4: '', img5: '', img6: '', img7: '', img8: '', img9: '', img10: '', img11: '', orders: 0, status: 'published' }
        this.fileList = [[], [], [], [], [], [], [], [], [], [], [], [], []]
      }
      this.Switch()
    },
    Edit(id) {
      if (this.mode === 'add') {
        this.mode = 'edit'
      }
      this.form = { id: 0, name: '', type_id: '', description: '', cover: '', img1: '', img2: '', img3: '', img4: '', img5: '', img6: '', img7: '', img8: '', img9: '', img10: '', img11: '', orders: 0, status: 'published' }
      this.fileList = [[], [], [], [], [], [], [], [], [], [], [], [], []]
      this.Switch()
      const form = this.list.filter(array => {
        return parseInt(array.id) === parseInt(id)
      })[0]
      this.form = JSON.parse(JSON.stringify(form))

      this.imgs.forEach((item, index) => {
        const file = this.localFile.filter(array => {
          return array.path === this.form[this.imgs[parseInt(index)]]
        })[0]
        if (file) {
          this.fileList[parseInt(index)].push(file)
        }
      })
    },
    Insert() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.Switch()
          insertProjectsDetail(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: 0, name: '', type_id: '', description: '', cover: '', img1: '', img2: '', img3: '', img4: '', img5: '', img6: '', img7: '', img8: '', img9: '', img10: '', img11: '', orders: 0, status: 'published' }
              this.fileList = [[], [], [], [], [], [], [], [], [], [], [], [], []]
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
          updateProjectsDetail(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: 0, name: '', type_id: '', description: '', cover: '', img1: '', img2: '', img3: '', img4: '', img5: '', img6: '', img7: '', img8: '', img9: '', img10: '', img11: '', orders: 0, status: 'published' }
              this.fileList = [[], [], [], [], [], [], [], [], [], [], [], [], []]
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
          deleteProjectsDetail(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
              this.form = { id: 0, name: '', type_id: '', description: '', cover: '', img1: '', img2: '', img3: '', img4: '', img5: '', img6: '', img7: '', img8: '', img9: '', img10: '', img11: '', orders: 0, status: 'published' }
              this.fileList = [[], [], [], [], [], [], [], [], [], [], [], [], []]
              this.fetchData()
            } else {
              this.resError(res.message)
            }
          })
        }).catch(() => {
        })
    },
    getType(type_id) {
      const type = this.projectsType.find(d => parseInt(d.id) === parseInt(type_id))
      if (type !== undefined) {
        return type.name
      } else {
        return '類別不存在'
      }
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
