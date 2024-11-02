@if(count($slides) > 0)
<!-- Slider main container -->
<div class="swiper-container">
    <!-- Additional required wrapper -->
    <div class="swiper-wrapper">
        <!-- Slides -->
        @foreach($slides as $slide)
            <div class="swiper-slide">
                <img src="{{ Storage::url($slide->image) }}" alt="{{ $slide->name }}">
            </div>
        @endforeach


    </div>
    <!-- If we need pagination -->
    <div class="swiper-pagination"></div>

    <!-- If we need navigation buttons -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>

    <!-- If we need scrollbar -->
    <div class="swiper-scrollbar"></div>
</div>
@endif
