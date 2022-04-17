import request from '@/utils/request'
export function getPlayer() {
  return request({
    url: '/admin/player',
    method: 'get'
  })
}

export function insertPlayer(data) {
  return request({
    url: '/admin/player',
    method: 'post',
    data
  })
}

export function updatePlayer(data) {
  return request({
    url: '/admin/player',
    method: 'patch',
    data
  })
}

export function deletePlayer(data) {
  return request({
    url: '/admin/player',
    method: 'delete',
    data
  })
}
