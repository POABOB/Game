import request from '@/utils/request'
// clear
export function clearCache() {
  return request({
    url: '/twig',
    method: 'get'
  })
}
