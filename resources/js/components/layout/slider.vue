<template>


    <carousel v-if="(['spot'].indexOf($route.name) > -1) " :navigationEnabled="false" :autoplay="true" :perPage="1" :paginationEnabled="false">
        <slide v-for="item in items">
            <img :src="item.src" alt="">
        </slide>

    </carousel>


</template>

<script>
import { Carousel, Slide } from 'vue-carousel';
    export default {
        components: {
            Carousel,
            Slide
        },
        data() {
            return {
                items:[
                    {
                        src: '/img/slide1.jpg'
                    },
                    {
                        src: '/img/slide2.jpg'
                    },
                    {
                        src: '/img/slide3.jpg'
                    },
                    {
                        src: '/img/slide4.jpg'
                    },
                    {
                        src: '/img/slide5.jpg'
                    }
                ]
            }
        }
    }
</script>


