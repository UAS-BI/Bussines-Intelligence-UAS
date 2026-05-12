<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

$checkLogin = function () {
    if (!session('is_logged_in')) {
        return redirect()->route('login');
    }

    return null;
};

Route::get('/login', function () {
    if (session('is_logged_in')) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $username = request('username');
    $password = request('password');

    if (empty($username) || empty($password)) {
        return back()->with('error', 'Username dan password wajib diisi.');
    }

    if ($username === 'lewis' && $password === 'lw12') {
        session([
            'is_logged_in' => true,
            'username' => $username,
        ]);

        return redirect()->route('dashboard');
    }

    return back()->with('error', 'Username atau password salah.');
})->name('login.process');

Route::get('/logout', function () {
    session()->flush();

    return redirect()->route('login');
})->name('logout');

Route::get('/', function () use ($checkLogin) {
    if ($response = $checkLogin()) {
        return $response;
    }

    return app(DashboardController::class)->index();
})->name('dashboard');

Route::get('/location-insights', function () use ($checkLogin) {
    if ($response = $checkLogin()) {
        return $response;
    }

    return app(DashboardController::class)->locationInsights();
})->name('location.insights');

Route::get('/property-types', function () use ($checkLogin) {
    if ($response = $checkLogin()) {
        return $response;
    }

    return app(DashboardController::class)->propertyTypes();
})->name('property.types');

Route::get('/price-analysis', function () use ($checkLogin) {
    if ($response = $checkLogin()) {
        return $response;
    }

    return app(DashboardController::class)->priceAnalysis();
})->name('price.analysis');

Route::get('/property-condition', function () use ($checkLogin) {
    if ($response = $checkLogin()) {
        return $response;
    }

    return app(DashboardController::class)->propertyCondition();
})->name('property.condition');

Route::get('/property-data', [DashboardController::class, 'propertyData'])
    ->name('property.data');

Route::post('/property-data', [DashboardController::class, 'storeProperty'])
    ->name('property.data.store');

Route::put('/property-data/{id}', [DashboardController::class, 'updateProperty'])
    ->name('property.data.update');

Route::delete('/property-data/{id}', [DashboardController::class, 'deleteProperty'])
    ->name('property.data.delete');