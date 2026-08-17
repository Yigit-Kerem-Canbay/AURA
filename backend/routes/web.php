<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\WebsiteAuditController;
use App\Http\Controllers\WebAuthController;

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [WebAuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->isAdmin()) {
            $documentCount = \App\Models\Document::count();
            $websiteCount = \App\Models\Website::count();
            $auditCount = \App\Models\WebsiteAudit::count();
            $aiQuestionCount = \App\Models\Message::where('role', 'user')->count();
        } else {
            $documentCount = $user->documents()->count();
            $websiteCount = $user->websites()->count();
            // Since audits belong to websites, get audits for user's websites
            $auditCount = \App\Models\WebsiteAudit::whereHas('website', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();
            $aiQuestionCount = \App\Models\Message::where('role', 'user')
                ->whereHas('conversation', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count();
        }

        return view('dashboard.index', compact('documentCount', 'aiQuestionCount', 'websiteCount', 'auditCount'));
    })->name('dashboard');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'ask'])->name('chat.ask');
    Route::delete('/chat/{id}', [App\Http\Controllers\ChatController::class, 'destroy'])->name('chat.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit', 'update']);
    });

    Route::get('/audit', [WebsiteAuditController::class, 'index'])->name('audit.index');
    Route::post('/audit', [WebsiteAuditController::class, 'store'])->name('audit.store');
    Route::get('/audit/{id}', [WebsiteAuditController::class, 'show'])->name('audit.show');
    Route::delete('/audit/{id}', [WebsiteAuditController::class, 'destroy'])->name('audit.destroy');
});
