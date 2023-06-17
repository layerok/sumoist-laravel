<?php

if(!function_exists('perRequestCache')) {
    function perRequestCache() {
        return cache()->store('array');
    }
}
