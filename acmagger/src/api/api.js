import axios from 'axios';

export  default {
	dataGetDay:function (cb) {
	        axios.get('/data/getDay').then(function (res) {
	            if(res.data.data.code ==1000){
	                setTimeout(function () {  	
	                    cb(res.data.data.data);

	                },3000)
	            }
	        });
	    },

 
 dataGetMounth:function(cb) {
	        axios.get('/data/getMounth').then(function (res) {
	            if(res.data.mounth.code ==1000){
	                setTimeout(function () {  	
	                    cb(res.data.mounth.data);
	                },300)
	            }
	        });
	    },

 dataCustomer:function(cb,id){
 	/*axios.get('/date/Cusdata',{
    	cs:'3',
     
    }
 	).then(function(res){

 		setTimeout(function () {  	
 		    cb(res.data.cusdata);
 		},300)
 	})*/
 	axios({ method: 'get',
  url: '/date/Cusdata',
  data: {
    f: id,
   
  }}).then(function(res){
  		cb(res.data.cusdata.cd)
  })
 }

}

