import request from '@/utils/request'
export function getStatus() {
  return request({
    url: '/rank/status',
    method: 'get'
  })
}

export function confirm(data) {
  return request({
    url: '/rank/confirm',
    method: 'post',
    data
  })
}
