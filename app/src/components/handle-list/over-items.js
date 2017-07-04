import axios from 'axios';

const parentMixin = {
 mounted() {
    this.updateIndex()
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
  },
 watch: {
    index (val, oldVal) {
      oldVal > -1 && this.$children[oldVal] && (this.$children[oldVal].selected = false)
      val > -1 && (this.$children[val].selected = true)
    }
  },
  data () {
    return {
      index:-1,
      
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
  /* ****点击加载数据**** */
    onItemClick () {
      if (typeof this.disabled === 'undefined' || this.disabled === false) {
        this.selected=!this.selected;        
        if(this.selected){
          //1、接收参数，
        	 this.param=this.dataItem
           if(this.cusList.length==0){
            // 加载
               this.count++
            this.getData(this.param)
          }
        	 
        }
        this.$parent.index = this.index
        this.$emit('on-item-click'); 
      }
    },
    //传参数调用数据 
    getData(param,flag){
      var _this=this;
        axios.post(urlstr + 'user.counselors.getgenjinlist&type='+param.type+'&status='+param.status).then(function (res) {
            if (res.data.code == 3) {
                _this.cusList=res.data.data
            }
        });
    	
    },
    loadMore() {
        if(this.selected){
           alert(this.count++)
          this.getData(this.param)
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
      cusList:[],
      count:0,
    }
  }
}

export {
  parentMixin,
  childMixin
}

