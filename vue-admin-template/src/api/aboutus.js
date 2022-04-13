import request from '@/utils/request'
// intro
export function getAbout() {
  return request({
    url: '/admin/aboutus/intro/list',
    method: 'get'
  })
}

export function updateAbout(data) {
  return request({
    url: '/admin/aboutus/intro/update',
    method: 'patch',
    data
  })
}

// member
export function getAboutUSMember() {
  return request({
    url: '/admin/aboutus/member/list',
    method: 'get'
  })
}

export function insertAboutUSMember(data) {
  return request({
    url: '/admin/aboutus/member/add',
    method: 'post',
    data
  })
}

export function updateAboutUSMember(data) {
  return request({
    url: '/admin/aboutus/member/update',
    method: 'patch',
    data
  })
}

export function deleteAboutUSMember(data) {
  return request({
    url: '/admin/aboutus/member/delete',
    method: 'delete',
    data
  })
}

export function removeAboutUSMemberFile(data) {
  return request({
    url: '/admin/aboutus/member/file/remove',
    method: 'delete',
    data
  })
}
