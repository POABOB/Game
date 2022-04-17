import request from '@/utils/request'
export function getJudger() {
  return request({
    url: '/admin/judger',
    method: 'get'
  })
}

export function insertJudger(data) {
  return request({
    url: '/admin/judger',
    method: 'post',
    data
  })
}

export function updateJudger(data) {
  return request({
    url: '/admin/judger',
    method: 'patch',
    data
  })
}

export function updateJudgerPassword(data) {
  return request({
    url: '/admin/judger/password',
    method: 'patch',
    data
  })
}

export function deleteJudger(data) {
  return request({
    url: '/admin/judger',
    method: 'delete',
    data
  })
}
