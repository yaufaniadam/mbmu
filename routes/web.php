<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\SelfRegistration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $posts = \App\Models\Post::where('status', 'published')
        ->orderBy('id', 'desc')
        ->take(4)
        ->get();
    
    $sliders = \App\Models\HomeSlider::where('is_active', true)
        ->orderBy('order')
        ->get();
    
    $features = \App\Models\HomeFeature::where('is_active', true)
        ->orderBy('order')
        ->get();
    
    return view('welcome', compact('posts', 'sliders', 'features'));
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



Route::get('/panduan', function () {
    return view('public.guide');
})->name('guide.public');

Route::get('/tim', function () {
    $teamMembers = \App\Models\TeamMember::where('is_active', true)
        ->orderBy('order')
        ->get();
    
    return view('public.team', compact('teamMembers'));
})->name('team.public');

Route::controller(\App\Http\Controllers\PublicSppgController::class)->group(function () {
    Route::get('/daftar-sppg', 'index')->name('sppg.public.index');
    Route::get('/daftar-sppg/{sppg}', 'show')->name('sppg.public.show');
});

Route::get('/menu', [\App\Http\Controllers\PublicMenuController::class, 'index'])->name('menu.public.index');

Route::get('/artikel', [\App\Http\Controllers\PublicBlogController::class, 'index'])->name('blog.public.index');
Route::get('/artikel/{post:slug}', [\App\Http\Controllers\PublicBlogController::class, 'show'])->name('blog.public.show');

// Self Registration Routes
Route::get('/daftar', SelfRegistration::class)->name('register.self');
Route::get('/daftar/{role}/{token}', SelfRegistration::class)->name('register.self.token');

// require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/instructions/{instruction}/attachment', [App\Http\Controllers\InstructionAttachmentController::class, 'download'])
        ->name('instructions.attachment.download');
});

Route::get('/instructions/{instruction}/attachment/signed', [App\Http\Controllers\InstructionAttachmentController::class, 'downloadSigned'])
    ->name('instructions.attachment.signed')
    ->middleware('signed');

// Delivery Role Routes
Route::middleware('guest')->group(function () {
    Route::get('/delivery/login', \App\Livewire\Delivery\Login::class)->name('delivery.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/delivery/dashboard', \App\Livewire\Delivery\Dashboard::class)->name('delivery.dashboard');
    Route::post('/delivery/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('delivery.login');
    })->name('delivery.logout');
});
