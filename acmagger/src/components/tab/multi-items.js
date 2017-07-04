const parentMixin = {
 mounted() {
    this.updateIndex()
  },
  methods: {
    updateIndex () {
      if (!this.$children) return
      this.number = this.$children.length
      let children = this.$children
      children[0].selected=true
      for (let i = 0; i < children.length; i++) {
        children[i].index = i

        if (children[i].selected) {
          this.index = i
        }
      }
    }
  },
 /* props: {
    index: {
      type: Number,
      default: -1
    }
  },*/
  watch: {
    index (val, oldVal) {
      //老大值 大于-1 并且有这个子项，那么这个子项select为false
      oldVal > -1 && this.$children[oldVal] && (this.$children[oldVal].selected = false)
      val > -1 && (this.$children[val].selected = true)
    }
  },
  data () {
    return {
      number: this.$children.length,
      index:-1
    }
  }
}

const childMixin = {
  props: {
    /*selected: {
      type: Boolean,
      default: false
    }*/
  },
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
        this.selected=true;

        // 新添加
        // this.mySelected = true
        this.$parent.index = this.index
        this.$emit('on-item-click')
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
     selected(val){
       this.$emit('show',val)
     }
    // 新添加
    /*mySelected(val){
       this.$emit('on-seclec-change',val)
    }*/
  },
  data () {
    return {
      index: -1,
      // selected因为不能再内部改变，data中创建一个副本mySelected变量，初始值为props属性result的值，
      //同时在组件内所有需要调用props的地方调用这个data对象mySelected
      // mySelected:this.selected
       selected:false


    }
  }
}

export {
  parentMixin,
  childMixin
}

