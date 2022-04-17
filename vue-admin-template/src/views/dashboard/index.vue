<template>
  <div class="dashboard-container">
    <div class="dashboard-text">帳號: {{ account }}</div>
    <el-form :model="form">
      <el-form-item label="舊密碼">
        <el-input
          v-model="form.oldpass"
          autocomplete="off"
          type="password"
          placeholder="舊密碼"
        />
      </el-form-item>
      <el-form-item label="新密碼">
        <el-input
          v-model="form.password"
          autocomplete="off"
          type="password"
          placeholder="新密碼"
        />
      </el-form-item>
      <el-form-item label="密碼確認">
        <el-input
          v-model="form.passconf"
          autocomplete="off"
          type="password"
          placeholder="密碼確認"
        />
      </el-form-item>
      <el-form-item>
        <el-button
          type="info"
          @click="Update"
        >
          更新
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { updateJudgerPassword } from '@/api/judger'
export default {
  name: 'Dashboard',
  data() {
    return {
      form: {
        oldpass: '',
        password: '',
        passconf: ''
      }
    }
  },
  computed: {
    ...mapGetters([
      'account'
    ])
  },
  methods: {
    Update() {
      if (!(this.form.oldpass === '' || this.form.password === '' || this.form.passconf === '')) {
        if (!(this.form.oldpass.length < 4 || this.form.password.length < 4 || this.form.passconf.length < 4)) {
          if (this.form.password === this.form.passconf) {
            const form = {
              judger_id: 1,
              password: this.form.password
            }
            updateJudgerPassword(form).then(res => {
              if (res.code === 200) {
                this.resSuccess('更新成功')
                this.Clear()
              } else {
                this.resError(res.message)
              }
            })
          } else {
            this.resError('密碼確認不相符')
          }
        } else {
          this.resError('欄位長度不能小於4')
        }
      } else {
        this.resError('欄位不能為空')
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
    },
    Clear() {
      this.form = {
        oldpass: '',
        password: '',
        passconf: ''
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard {
  &-container {
    margin: 30px;
  }
  &-text {
    font-size: 30px;
    line-height: 46px;
  }
}
</style>
