import request from '@/utils/request'
export function getHome() {
  return request({
    url: '/admin/index/list',
    method: 'get'
  })
}

export function insertHome(data) {
  return request({
    url: '/admin/index/add',
    method: 'post',
    data
  })
}

export function updateHome(data) {
  return request({
    url: '/admin/index/update',
    method: 'patch',
    data
  })
}

export function deleteHome(data) {
  return request({
    url: '/admin/index/delete',
    method: 'delete',
    data
  })
}

export function removeHomeFile(data) {
  return request({
    url: '/admin/index/file/remove',
    method: 'delete',
    data
  })
}
