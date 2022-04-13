
const tokens = {
    nchuadmin: {
        token: 'admin-token'
    },
    editor: {
        token: 'editor-token'
    }
}

const users = {
  'admin-token': {
    roles: ['admin'],
    introduction: 'I am a super administrator',
    avatar: 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
    account: 'Super Admin'
  },
  'editor-token': {
    roles: ['editor'],
    introduction: 'I am an editor',
    avatar: 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
    account: 'Normal Editor'
  }
}

export default [
  // user login
  {
    url: '/user/login',
    type: 'post',
    response: config => {
      const { account } = config.body
      const token = tokens[account]

      // mock error
      if (!token) {
        return {
          status: 204,
          message: 'Account and password are incorrect.'
        }
      }

        return {
            status: 200,
            data: token,
            flag: true
        }
    }
  },

  // get user info
  {
    url: '/user/info\.*',
    type: 'get',
    response: config => {
      const { token } = config.query
      const info = users[token]

      // mock error
      if (!info) {
        return {
            status: 508,
            message: 'Login failed, unable to get user details.'
        }
      }

        return {
            status: 200,
            data: info,
            flag: true
        }
    }
  },

  // user logout
  {
    url: '/user/logout',
    type: 'post',
    response: _ => {
        return {
            status: 200,
            data: 'success',
            flag: true
        }
    }
  }
]
