<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Public Pages
Route::get('/profil', function () {
    return view('public.profile');
})->name('profile.public');

Route::get('/kontak', function () {
    return view('public.contact');
})->name('contact.public');

Route::get('/tim', function () {
    return view('public.team');
})->name('team.public');

Route::get('/daftar-sppg', function () {
    $sppgs = \App\Models\Sppg::where('is_active', true)->paginate(15);
    return view('sppg.index', compact('sppgs'));
})->name('sppg.public.index');

// require __DIR__.'/auth.php';
