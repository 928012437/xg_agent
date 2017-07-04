var Mock = require('mockjs');
var Random = Mock.Random;
Random.natural();
Random.cname();
Random.date('dd');
Random.date('y-MM-dd');


// 数据模板中的每个属性由 3 部分构成：属性名、生成规则、属性值

Mock.mock(
	'/data/getDay',
	{ 
		"data":{
			"code":"1000",
			"data":{
				"data|20":["@natural(60, 100)"],
				"date|20":["@date"]
			}
			
		}
	}

).mock(
	'/data/getMounth',
	{ 
		"mounth":{
			"code":"1000",
			"data":{
				"data|20":["@natural(1, 30)"],
				"date|20":["@date('y-MM-dd')"]
			}			
		}
	}

).mock(
 '/date/Cusdata',
function(options){
	var str=options.body;
	var arr=str.split(':');
	var num =arr[1].slice(0,3)
	if(num=='111'){
		return {
			"cusdata":{
				"cd":[
						{"num|1-30":1,"source":'新客户来访'},
						{"num|1-30":1,"source":'老客户来访'},
						{"num|1-30":1,"source":'新客户来电'},
						{"num|1-30":1,"source":'去电'}
					]
				}
			}
		}
	}
).mock(
	'/list/customer',
	{
		"customer":{
				
		}
	}

)
