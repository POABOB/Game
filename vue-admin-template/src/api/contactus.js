import request from '@/utils/request'
export function getContactUS() {
  return request({
    url: '/admin/contactus/list',
    method: 'get'
  })
}

export function insertContactUS(data) {
  return request({
    url: '/admin/contactus/add',
    method: 'post',
    data
  })
}

export function updateContactUS(data) {
  return request({
    url: '/admin/contactus/update',
    method: 'patch',
    data
  })
}

export function deleteContactUS(data) {
  return request({
    url: '/admin/contactus/delete',
    method: 'delete',
    data
  })
}
