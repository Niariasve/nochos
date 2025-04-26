<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{n1?}/{n2?}', function() {
    return view('Vue.vue');
});