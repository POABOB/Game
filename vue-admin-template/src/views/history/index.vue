<template>
  <div class="app-container">
    <el-row :gutter="20">
      <!-- <el-col>
        <el-button type="info" class="m" @click="Add">新增比賽</el-button>
      </el-col> -->
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
        width="115"
      >
        <template slot-scope="scope">
          <el-button
            type="info"
            icon="el-icon-s-order"
            circle
            @click="Views(scope.row.game_id, scope.row.name)"
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

  </div>
</template>

<script>
import { getGameHistory } from '@/api/game'

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
      listLoading: true,
      page: [
        { pagesize: 5, currpage: 1 }
      ],
      searchMap: {
        word: null
      },
      url: process.env.VUE_APP_BASE_URL,
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
      getGameHistory().then(response => {
        const data = (response.data === null) ? [] : JSON.parse(JSON.stringify(response.data))
        this.list = data
        this.fullList = data

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
    Views(game_id, name) {
      window.open(`http://35.194.213.39${this.url}/rank/${game_id}`, name, '_blank')
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
