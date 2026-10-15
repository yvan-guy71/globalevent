<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CompetitionCandidateController;
use App\Http\Controllers\CompetitionVoteController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FundraiserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicCheckInController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/evenements', [EventController::class, 'index'])->name('events.index');
Route::get('/evenements/creer', [EventController::class, 'create'])->middleware('auth')->name('events.create');
Route::post('/evenements', [EventController::class, 'store'])->middleware('auth')->name('events.store');
Route::get('/mes-evenements', [EventController::class, 'mine'])->middleware('auth')->name('events.mine');
Route::get('/evenements/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/evenements/{event}/modifier', [EventController::class, 'edit'])->middleware('auth')->name('events.edit');
Route::put('/evenements/{event}', [EventController::class, 'update'])->middleware('auth')->name('events.update');
Route::delete('/evenements/{event}', [EventController::class, 'destroy'])->middleware('auth')->name('events.destroy');

Route::prefix('/evenements/{event}/candidats')->name('competitions.')->group(function () {
    Route::get('/', [CompetitionCandidateController::class, 'index'])->name('index');
    Route::post('/', [CompetitionCandidateController::class, 'store'])->middleware('auth')->name('candidates.store');
    Route::put('/{candidate}', [CompetitionCandidateController::class, 'update'])->middleware('auth')->name('candidates.update');
    Route::delete('/{candidate}', [CompetitionCandidateController::class, 'destroy'])->middleware('auth')->name('candidates.destroy');
    Route::post('/{candidate}/vote', [CompetitionVoteController::class, 'store'])->middleware('auth')->name('vote');
    Route::delete('/{candidate}/vote', [CompetitionVoteController::class, 'destroy'])->middleware('auth')->name('vote.destroy');
});

Route::prefix('/evenements/{event}/billets')->name('ticket-types.')->middleware('auth')->group(function () {
    Route::get('/', [TicketTypeController::class, 'index'])->name('index');
    Route::post('/', [TicketTypeController::class, 'store'])->name('store');
    Route::put('/{ticketType}', [TicketTypeController::class, 'update'])->name('update');
    Route::delete('/{ticketType}', [TicketTypeController::class, 'destroy'])->name('destroy');
});

Route::get('/evenements/{event}/reserver', [OrderController::class, 'create'])->middleware('auth')->name('orders.create');
Route::post('/evenements/{event}/reserver', [OrderController::class, 'store'])->middleware('auth')->name('orders.store');

Route::get('/mes-commandes', [OrderController::class, 'index'])->middleware('auth')->name('orders.index');
Route::get('/commandes/{order}', [OrderController::class, 'show'])->middleware('auth')->name('orders.show');

Route::get('/mes-billets', [TicketController::class, 'index'])->middleware('auth')->name('tickets.index');
Route::get('/billets/{ticket}', [TicketController::class, 'show'])->middleware('auth')->name('tickets.show');

Route::get('/evenements/{event}/check-in', [CheckInController::class, 'show'])->middleware('auth')->name('checkin.show');
Route::post('/evenements/{event}/check-in', [CheckInController::class, 'process'])->middleware('auth')->name('checkin.process');
Route::post('/evenements/{event}/check-in/partage', [CheckInController::class, 'enableShare'])->middleware('auth')->name('checkin.share');
Route::delete('/evenements/{event}/check-in/partage', [CheckInController::class, 'disableShare'])->middleware('auth')->name('checkin.unshare');

Route::get('/scan/{event}/{token}', [PublicCheckInController::class, 'show'])->name('scan.show');
Route::post('/scan/{event}/{token}', [PublicCheckInController::class, 'process'])->name('scan.process');

Route::get('/cagnottes', [FundraiserController::class, 'index'])->name('fundraisers.index');
Route::get('/cagnottes/creer', [FundraiserController::class, 'create'])->middleware('auth')->name('fundraisers.create');
Route::post('/cagnottes', [FundraiserController::class, 'store'])->middleware('auth')->name('fundraisers.store');
Route::get('/mes-cagnottes', [FundraiserController::class, 'mine'])->middleware('auth')->name('fundraisers.mine');
Route::get('/cagnottes/{fundraiser}', [FundraiserController::class, 'show'])->name('fundraisers.show');
Route::get('/cagnottes/{fundraiser}/modifier', [FundraiserController::class, 'edit'])->middleware('auth')->name('fundraisers.edit');
Route::put('/cagnottes/{fundraiser}', [FundraiserController::class, 'update'])->middleware('auth')->name('fundraisers.update');
Route::delete('/cagnottes/{fundraiser}', [FundraiserController::class, 'destroy'])->middleware('auth')->name('fundraisers.destroy');
Route::post('/cagnottes/{fundraiser}/dons', [DonationController::class, 'store'])->middleware('auth')->name('fundraisers.donate');

Route::get('/comment-ca-marche', [PageController::class, 'howItWorks'])->name('pages.how');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('pages.contact.send');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.post');
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/devenir-organisateur', [AuthController::class, 'becomeOrganizer'])->middleware('auth')->name('become-organizer');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/utilisateurs', [UserController::class, 'index'])->name('users');
    Route::put('/utilisateurs/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
