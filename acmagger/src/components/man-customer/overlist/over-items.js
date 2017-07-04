const parentMixin = {
 mounted() {
    this.updateIndex()
  },  
  computed:{
  },
  methods: {
    updateIndex () {
      if (!this.$children) return
      let children = this.$children 
      for (let i = 0; i < children.length; i++) {
        children[i].index = i
        if (children[i].selected) {
          this.index = i
        }
      }
    },
    getChecked(){ 
        let children =this.$children,_this=this;
        var checkedGroup=[];
        for (let i = 0; i < children.length; i++) {
          //如果客户数组的长度大于0，则存入checkedGroup数组里
          if(children[i].selectedIteam.customerId.length>0){
            checkedGroup.push(children[i].selectedIteam)
        }     
      }
       return checkedGroup;
      
    },
    // 分配客户
      assign(){
          this.allGroup.saleGroup=this.saleGroup;
          this.allGroup.customerGroup=this.getChecked();
            console.log( this.allGroup.saleGroup +'____'+ this.allGroup.customerGroup)
        // 判断是否有选择客户        
        if(this.allGroup.saleGroup.length<=0 && this.allGroup.customerGroup.length<=0){
            this.la()
        }else{   
          // this.$router.push({ path: '/customer/assign/overdue' },)
        }
      }
  },
 watch: {
    index (val, oldVal) {
      //老大值 大于-1 并且有这个子项，那么这个子项select为false
      oldVal > -1 && this.$children[oldVal] && (this.$children[oldVal].selected = false)
      val > -1 && (this.$children[val].selected = true)
    }
  },
  data () {
    return {
      index:-1,
      allGroup:{
          saleGroup:[],
          customerGroup:[]
      }
    }
  }
}





const childMixin = {
 mounted() {
    this.$parent.updateIndex()
  },
  beforeDestroy () {
    const $parent = this.$parent
    this.$nextTick(() => {
      $parent.updateIndex()
    })
  },

methods: {
    onItemClick () {
      if (typeof this.disabled === 'undefined' || this.disabled === false) {
        this.selected=!this.selected;
        
        if(this.selected){
        	 this.param=this.saleItem.teamer
           if(this.team.length==0){this.getData(this.param,this.isChecked)}
        	 
        }
        this.$parent.index = this.index
        this.$emit('on-item-click'); 
      }
    },
    //传参数调用数据 
    getData(param,flag){
      var _this=this;
      setTimeout(function(){
        _this.team=[

           {
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                id:14
              },
              {
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                 id:17
              },{
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                 id:17
              },{
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                 id:17
              },
              {
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                 id:17
              },{
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                 id:17
              },{
                name:param,
                tel:184568485,
                days:62,
                checked:flag,
                 id:17
              }
      ]},300)
    	
    },
    loadMore() {
        if(this.selected){
          alert('sdf')
          this.getData(this.param,this.isChecked)
        }
      }

  },
 
  watch: {
    selected (val) {
      this.mySelected=val;
      if (val) {
        this.$parent.index = this.index
        
      }

    },
    
  },
  data () {
    return {
      index: -1,
      selected:false,
      team:[],
      customerList:[]
    }
  }
}

export {
  parentMixin,
  childMixin
}

