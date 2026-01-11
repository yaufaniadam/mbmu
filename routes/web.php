<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\SelfRegistration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $posts = \App\Models\Post::where('status', 'published')
        ->latest('published_at')
        ->take(4)
        ->get();
    return view('welcome', compact('posts'));
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

Route::controller(\App\Http\Controllers\PublicSppgController::class)->group(function () {
    Route::get('/daftar-sppg', 'index')->name('sppg.public.index');
    Route::get('/daftar-sppg/{sppg:kode_sppg}', 'show')->name('sppg.public.show');
});

Route::get('/artikel/{post:slug}', [\App\Http\Controllers\PublicBlogController::class, 'show'])->name('blog.public.show');

// Self Registration Routes
Route::get('/daftar', SelfRegistration::class)->name('register.self');
Route::get('/daftar/{role}/{token}', SelfRegistration::class)->name('register.self.token');

// require __DIR__.'/auth.php';

