<template>
  <div class="app-container">
    <el-form ref="form" :model="form" :rules="rules">
      <el-form-item label="關於我們" prop="content">
        <el-input
          v-model="form.content"
          autocomplete="off"
          type="textarea"
          placeholder="關於我們"
          :rows="20"
        />
      </el-form-item>
    </el-form>
    <el-button type="primary" @click="Update">提交</el-button>
  </div>
</template>

<script>
import { getAbout,
  updateAbout } from '@/api/aboutus'
import { clearCache } from '@/api/clear'
export default {
  data() {
    return {
      form: {
        id: 0,
        content: ''
      },
      rules: {
        content: [
          { required: true, message: '請輸入關於我們!', trigger: 'blur' },
          { max: 2056, message: '長度不能超過2056個字!', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      getAbout().then(response => {
        if (response.data !== null) {
          this.form = JSON.parse(JSON.stringify(response.data))
          clearCache()
        }
      })
    },
    Update() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          updateAbout(this.form).then(res => {
            if (res.code === 200) {
              this.resSuccess(res.message)
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

<style scoped>
.line{
    text-align: center;
}
</style>
