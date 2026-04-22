<?php

use App\Mail\RegistationCreated;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('home');

Route::get('/mailable', function () {
    $registration = App\Models\Registration::find(1);

    return new RegistationCreated($registration);
});
