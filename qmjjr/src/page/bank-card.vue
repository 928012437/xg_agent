<template>
    <div class="content">
        <Top></Top>
        <div class="content margin-top margin-bottom">
            <ul>
                <li v-for='(card,index) in cards'>
                    <div class="card">
                        <div class="card-content" @click="changecard(index)">
                            <div class="card-content-inner" :style="'background:'+card.color">
                                <div>{{card.bankname}}</div>
                                <div>{{card.num}}</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <label :for='card.id' class="card-btn" @click='changedef(card.id)'>
                                <input type="radio" :value='card.id' name='card' :id='card.id' :checked='card.isdef'/>
                                <p class="button">
                                    默认银行卡
                                </p>
                            </label>
                            <div @click='delCard(index,card.id)'>
                                删除
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <button class="button add-btn  button-big" @click='show=!show'>添加银行卡</button>
            <popup v-model='show' height='400px' @on-show='resetForm'>
                <card-info :card='card'></card-info>
                <button class="bar bar-tab bottom-btn" @click='addCard'>
                    保存
                </button>
            </popup>
        </div>


    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    import CardInfo from '../components/cardInfo.vue'
    import Popup from '../components/popup/index.vue'
    import axios from 'axios';
    import zepto from 'n-zepto'

    export default{
        components: {
            Popup,
            Top,
            CardInfo
        },
        created(){
            this.$store.commit('footer', false);
            this.getbankcardlist();
        },
        data(){
            return {
                show: false,
                card: {
                    name: '',
                    num: '',
                    bankname: '',
                    zhihang: '',
                    city: '',
                    isdef: false,
                    imgurl: '',
                    color: '',
                    id: ''
                },
                cards: [
                    {}
                ],
                cardId: []
            }
        },
        methods: {
            getbankcardlist(){
                var that = this;
                axios.post(urlstr + 'qmjjr.getbankcardlist').then(function (res) {
                    if (res.data.code == 3) {
                        that.cards = res.data.data;
                    }
                });
            },
            changecard(index){
                this.card = {
                    name: this.cards[index].name,
                    bankname: this.cards[index].bankname,
                    num: this.cards[index].num,
                    zhihang: this.cards[index].zhihang,
                    city: this.cards[index].city,
                    isdef: this.cards[index].isdef,
                    imgurl: this.cards[index].imgurl,
                    color: this.cards[index].color,
                    id: this.cards[index].id
                },
                        this.show = !this.show

            },
            addCard(){
                var that = this;
                var url = urlstr + 'qmjjr.bankcardpost';
                var fromdata = {
                    'name': this.card.name,
                    'num': this.card.num,
                    'bankname': this.card.bankname,
                    'zhihang': this.card.zhihang,
                    'city': this.card.city,
                    'isdef': this.card.isdef,
                    'imgurl': this.card.imgurl,
                    'color': this.card.color,
                    'id': this.card.id
                };
                zepto.post(url, fromdata, function (res) {
                    if (res == 1) {
                        that.show = !that.show
                        that.getbankcardlist();
                    }
                });
            },
            delCard(index, id){
                var that = this;
                axios.post(urlstr + 'qmjjr.delbankcard&id=' + id).then(function (res) {
                    if (res.data.code == 4) {
                        if (res.data.status == 1) {
                            that.cards.splice(index, 1);
                        } else if (res.data.status == 0) {
                            alert('默认银行卡不可删除！');
                        }
                    }
                });
            },
            changedef(id){
                var that = this;
                axios.post(urlstr + 'qmjjr.changedef&id=' + id).then(function (res) {

                });
            },
            resetForm(){
                document.getElementById('card-form').reset();
            }
        }
    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .card-content-inner {
        color: #fff;
    }

    .card-btn {
        display: block;
        input[type="radio"] {
            display: none;
            position: absolute;
            z-index: 99
        }
        input[type="radio"]:checked + p.button {
            background: @color-primary;
            color: #fff;
        }
    }

    .add-btn {
        background: @color-primary;
        color: #fff;
        width: 70%;
        margin: 0 auto;
    }


</style>
