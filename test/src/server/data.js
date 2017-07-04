var Mock = require('mockjs');
  Mock.mock(/\.json/, {
      'list|1-10': [{
          'id|+1': 1,
          'email': '@EMAIL'
      }]
  })
export default Mock;
