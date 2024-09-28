<script>
    const APP_THEME = "{{ \App\Models\Setting::where('key', '=', 'theme')->value('value') }}";
    const PATH_TO_THEME_ASSETS = "/storage/themes/" + APP_THEME + "/assets/";
</script>

<script src="{{ Asset::load('frontend/js/jquery.min.js') }}" type="text/javascript"></script>

<script src="{{ Asset::load('frontend/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/jquery.form.min.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/jquery.tmpl.min.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/jquery.mask.min.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script src="{{ Asset::load('frontend/js/notify.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/layerok.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/gsap/gsap.min.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/gsap/ScrollTrigger.min.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/animations.js') }}" type="text/javascript"></script>

{{--<script src="{{ asset('frontend/plugins/fancybox/fancybox.min.js') }}" type="text/javascript"></script>--}}
<script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iScroll/5.2.0/iscroll.min.js" integrity="sha512-wstvQlySDtT//3yKfbxpy8AS5b4UQ0tnItav8dGCVLGO3u/Ymb6mUEgVzDL8a/DXdtiuhsTj2ElDsXQ+E2cDYA==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/drawer/3.2.2/js/drawer.min.js" integrity="sha512-icvnRDv6b/z3NXPKA29EK1SRGYD17s9Q18MrES3/2YKve8OvYzzWWuXhOdXKtdQMsE3IFbcKCodJtHN0lELTtQ==" crossorigin="anonymous"></script>
<script src="{{ Asset::load('frontend/js/cart.js') }}" type="text/javascript"></script>
<script src="{{ Asset::load('frontend/js/script.js') }}" type="text/javascript"></script>





@stack('scripts')
