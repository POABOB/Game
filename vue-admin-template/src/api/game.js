import request from '@/utils/request'
export function getGame() {
  return request({
    url: '/admin/game',
    method: 'get'
  })
}

export function insertGame(data) {
  return request({
    url: '/admin/game',
    method: 'post',
    data
  })
}

export function updateGame(data) {
  return request({
    url: '/admin/game',
    method: 'patch',
    data
  })
}

export function deleteGame(data) {
  return request({
    url: '/admin/game',
    method: 'delete',
    data
  })
}

export function insertGamePlayer(data) {
  return request({
    url: '/admin/game/player',
    method: 'post',
    data
  })
}

export function insertGameJudger(data) {
  return request({
    url: '/admin/game/judger',
    method: 'post',
    data
  })
}

export function deleteGamePlayer(data) {
  return request({
    url: '/admin/game/player',
    method: 'delete',
    data
  })
}

export function deleteGameJudger(data) {
  return request({
    url: '/admin/game/judger',
    method: 'delete',
    data
  })
}

export function getGamePlayer() {
  return request({
    url: '/admin/game/player',
    method: 'get'
  })
}

export function getGameJudger() {
  return request({
    url: '/admin/game/judger',
    method: 'get'
  })
}

export function getGameHistory() {
  return request({
    url: '/admin/game/history',
    method: 'get'
  })
}
