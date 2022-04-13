import request from '@/utils/request'
export function getService() {
  return request({
    url: '/admin/service/list',
    method: 'get'
  })
}

export function insertService(data) {
  return request({
    url: '/admin/service/add',
    method: 'post',
    data
  })
}

export function updateService(data) {
  return request({
    url: '/admin/service/update',
    method: 'patch',
    data
  })
}

export function deleteService(data) {
  return request({
    url: '/admin/service/delete',
    method: 'delete',
    data
  })
}
