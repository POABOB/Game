import request from '@/utils/request'
export function getProjectsType() {
  return request({
    url: '/admin/projects/type/list',
    method: 'get'
  })
}

export function insertProjectsType(data) {
  return request({
    url: '/admin/projects/type/add',
    method: 'post',
    data
  })
}

export function updateProjectsType(data) {
  return request({
    url: '/admin/projects/type/update',
    method: 'patch',
    data
  })
}

export function deleteProjectsType(data) {
  return request({
    url: '/admin/projects/type/delete',
    method: 'delete',
    data
  })
}

export function removeProjectsTypeFile(data) {
  return request({
    url: '/admin/projects/type/file/remove',
    method: 'delete',
    data
  })
}

export function getProjectsDetail() {
  return request({
    url: '/admin/projects/detail/list',
    method: 'get'
  })
}

export function insertProjectsDetail(data) {
  return request({
    url: '/admin/projects/detail/add',
    method: 'post',
    data
  })
}

export function updateProjectsDetail(data) {
  return request({
    url: '/admin/projects/detail/update',
    method: 'patch',
    data
  })
}

export function deleteProjectsDetail(data) {
  return request({
    url: '/admin/projects/detail/delete',
    method: 'delete',
    data
  })
}

export function removeProjectsDetailFile(data) {
  return request({
    url: '/admin/projects/detail/file/remove',
    method: 'delete',
    data
  })
}
