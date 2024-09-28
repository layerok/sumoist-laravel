<div class="ph3-l ph2">
    @php
        $sort_array = [
              1 => [
                  'text' => 'По умолчанию'
              ],
              2 => [
                  'text' => 'Самые дорогие'
              ],
              3 => [
                  'text' => 'Самые дешевые'
              ],
              4 => [
                  'text' => 'Сначала новые'
              ],
               5 => [
                  'text' => 'Сначала старые'
              ]
        ];
    @endphp


    <form class="mb2 mt1 pt3 mt2-ml pt4-ml w5-ns w-auto" method="post" action="#products" id="sort_form">
        @csrf
        <div class="w-100  bg-transparent ba b--orange br2 pointer f6 h40 pl3 pr3 relative ease-in-fast flex items-center nested-list-reset select"
             style="box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset">
            <input type="hidden" name="catalog_sort_type" value="" data-submit-on-change="true">
            <div class="flex justify-between w-100">
                <div class="current-option">{{ $sort_array[$_SESSION['catalog_sort_type']]['text'] }}</div>
                <div class="h0 p0 pa1 b--white bw1 bb br bl-0 bt-0 rotate-45 check-mark ease-in-fast"></div>
            </div>
            <ul class="ease-in-fast scale0 o-0 top-100 ba b--orange br2 list shadow-2 bg-black mt1 pv1 o-100 overflow-hidden absolute z-999 left-0 right-0" style="max-height:300px">
                @foreach($sort_array as $key => $value)

                    <li class="ease-in-fast flex items-center h40 tc ph3 pv2 hover-bg-near-black  @if ($_SESSION['catalog_sort_type'] == $key) fw6 bg-near-black @endif "
                        data-value="{{ $key }}">
                        {{ $value['text'] }}
                    </li>

                @endforeach

            </ul>
        </div>


    </form>
</div>
