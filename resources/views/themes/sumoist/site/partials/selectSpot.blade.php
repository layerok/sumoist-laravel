<div x-data="getOptions()">

    <div class="inline-flex flex-column">
        <div class="pointer" @click="isOpened=true">
            <div class="di red">Ресторан:</div>
            <div  class=" di" x-text="name"></div>
            <div class="ml2 dib w0 h0 p0 pa1 b--white bw1 bb br bl-0 bt-0 rotate-45  check-mark ease-in-fast mb1"></div>
        </div>

        <div @click.away="isOpened=false" class="ba br2 b--red inline-flex flex-column" x-show="isOpened">
            <template x-for="child in children" :key="child.id">
                <div class="ph2 pv2">
                    <a class="white no-underline" x-text="child.name" :href="child.link"></a>
                </div>

            </template>
        </div>
    </div>



</div>

<script>
    function getOptions(){
        return {
            id: 1,
            name: 'Жемчужная 9, жк 49 жемчужина',
            isOpened: false,
            selected: 1,
            children: [
                {
                    id: 2,
                    name: 'Маршала Малиновского, 55',
                    link: 'http://kador.sumoist.com.ua'
                }
            ]
        }

    }
</script>
