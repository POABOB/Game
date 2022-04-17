<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col>
        <p v-if="rank_status === null">當前排行榜尚未被選擇! </p>
        <p v-else>當前排行榜顯示：{{ rank_status.name }}，比賽日期： {{ rank_status.date }}，比賽編號： {{ rank_status.game_id }}</p>
      </el-col>
    </el-row>

    <el-table
      v-loading="listLoading"
      :data="list.slice((currpage - 1) * pagesize, currpage * pagesize)"
      element-loading-text="Loading"
      border
      fit
      highlight-current-row
      :row-class-name="tableRowClassName"
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
        prop="game_id"
        label="比賽編號"
      />
      <el-table-column
        align="center"
        prop="date"
        label="日期"
      />
      <el-table-column
        align="center"
        prop="name"
        label="比賽名稱"
      />
      <el-table-column
        align="center"
        prop="content"
        label="比賽內容"
      />
      <el-table-column
        class-name="status-col"
        label="類型"
        align="center"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.type | statusFilter">{{ scope.row.type === '2' ? '兩輪' : '兩輪+五大招' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="套用"
        width="115"
      >
        <template slot-scope="scope">
          <el-button
            type="danger"
            icon="el-icon-check"
            circle
            @click="Edit(scope.row.game_id)"
          />
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-show="true"
      background
      layout="prev, pager, next, sizes, total, jumper"
      align="center"
      :page-sizes="[5, 10, 15, 20]"
      :page-size="pagesize"
      :total="list.length"
      @current-change="handleCurrentChange"
      @size-change="handleSizeChange"
    />
  </div>
</template>

<script>
import { getStatus,
  confirm } from '@/api/home'
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
      rank_status: null,
      listLoading: true,
      pagesize: 5,
      currpage: 1
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      getStatus().then(response => {
        const data = response.data
        console.log()
        this.list = (data['games'] === null) ? [] : data['games']
        this.rank_status = data['rank_status']
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
    Edit(game_id) {
      const form = {
        game_id: game_id
      }
      confirm(form).then(res => {
        if (res.code === 200) {
          this.resSuccess(res.message)
          this.fetchData()
        } else {
          this.resError(res.message)
        }
      }).catch(error => {
        alert(error)
      })
    },
    tableRowClassName({ row, rowIndex }) {
      if (this.rank_status === null) {
        return ''
      }
      if (this.rank_status.game_id === row.game_id) {
        return 'success-row'
      }
      return ''
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
<style>
  .el-table .warning-row {
    background: oldlace;
  }

  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
