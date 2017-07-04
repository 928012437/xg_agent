import axios from 'axios'

export defautl {
  dataGetDay:function(fn){
    axios.get('/data/getDay').then(function(res){
       fn(res.data.data.data)
    })
  }
}
