import Mock from 'mockjs'

const data_news = Mock.mock({
    'items|30': [{
        id: '@id',
        title: '@sentence(10, 20)',
        link: '@sentence(10, 20)',
        'status|1': ['published', 'draft', 'deleted'],
        published_at: '@datetime'
    }]
})

const data_activities = Mock.mock({
    'items|30': [{
        id: '@id',
        title: '@sentence(10, 20)',
        'img|10': ["@image('200x200','red','#fff','avatar')"],
        'status|1': ['published', 'draft', 'deleted'],
        published_at: '@datetime'
    }]
})

export default [
    {
        url: '/news/list',
        type: 'get',
        response: config => {
            const items = data_news.items
            return {
                status: 200,
                data: {
                    total: items.length,
                    items: items
                }
            }
        }
    },

    {
        url: '/news/insert',
        type: 'post',
        response: config => {
            return {
                status: 200,
                flag: true,
                message: '新增成功'
            }
        }
    },

    {
        url: '/news/update',
        type: 'post',
        response: config => {
            return {
                status: 200,
                flag: true,
                message: '更新成功',
            }
        }
    },

    {
        url: '/news/delete',
        type: 'post',
        response: config => {
            return {
                status: 200,
                flag: true,
                message: '刪除成功',
            }
        }
    },
]
