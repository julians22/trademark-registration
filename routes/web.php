<?php

use App\Mail\RegistationCreated;
use App\Livewire\TrademarkRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/private/trademark-file/{path}', function (Request $request, string $path) {
    abort_unless($request->hasValidSignature(), 403);

    $disk = Storage::disk('local');
    abort_unless($disk->exists($path), 404);

    $absolutePath = $disk->path($path);

    if ($request->boolean('download')) {
        return response()->download($absolutePath, basename($path));
    }

    return response()->file($absolutePath);
})->where('path', '.*')->name('private.trademark.file');

Route::get('/', TrademarkRegistration::class)->name('home');
Route::livewire('/enquiry-legacy', 'pages::index')->name('enquiry.legacy');

Route::get('/mailable', function () {
    $registration = App\Models\Registration::find(1);

    return new RegistationCreated($registration);
});
