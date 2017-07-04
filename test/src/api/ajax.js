function params(data){
  var arr = [];
  for(var i in data){
     arr.push(encodeURIComponent(i)+'='+encodeURIComponent(data[i]))
  }
  return arr.join('&')
}

export  const ajax = function(obj){
    var xhr = new XMLHttpRequest();

    obj.url=obj.url + '?rand='+Math.random();
    obj.data = params(obj.data);
    if(obj.method === 'get')obj.url += obj.url.indexOf('?')==-1 ? '?' +obj.data :  '&' +obj.data;
      console.log(obj.url)
   if(obj.async){
     xhr.onreadystatechange = () => {
       if(xhr.readyState === 4){
         if(xhr.status === 200){
          obj.success(xhr.responseText) //回调传参
         }else {
           alert('获取数据错误！错误代号：'+xhr.status+'错误信息：'+xhr.statusText)
         }
       }
     }
   }
  xhr.open(obj.method,obj.url,obj.async);
  if(obj.method === 'post'){
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
    xhr.send(obj.data)
  }else{
      xhr.send(null)
  }
  if(obj.async === false){
    if(xhr.status === 200){
      obj.success(xhr.responseText) //回调传参
    }else {
      alert('获取数据错误！错误代号：'+xhr.status+'错误信息：'+xhr.statusText)
    }
  }
}
