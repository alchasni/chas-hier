<?php

use App\Http\Controllers\{DashboardController,
    CategoryController,
    LaporanController,
    ProductController,
    GuestController,
    TransactionDetailController,
    TransactionController,
    UserController};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'csrf'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::group(['middleware' => 'auth'], function () {
            Route::get('/api/transaction', [TransactionController::class, 'getTransactionByDate']);
        });

        Route::group(['middleware' => 'level:1'], function () {
            Route::get('/category/data', [CategoryController::class, 'data'])->name('category.data');
            Route::resource('/category', CategoryController::class);

            Route::get('/product/data', [ProductController::class, 'data'])->name('product.data');
            Route::post('/product/barcode', [ProductController::class, 'printBarcode'])->name('product.print_barcode');
            Route::resource('/product', ProductController::class);

            Route::get('/guest/data', [GuestController::class, 'data'])->name('guest.data');
            Route::resource('/guest', GuestController::class);

            Route::get('/transaction/data', [TransactionController::class, 'data'])->name('transaction.data');
            Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
            Route::get('/transaction/{id}', [TransactionController::class, 'show'])->name('transaction.show');
            Route::delete('/transaction/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
        });

        Route::group(['middleware' => 'level:1,2'], function () {
            Route::get('/transaction/new', [TransactionController::class, 'create'])->name('transaction.new');
            Route::post('/transaction/save', [TransactionController::class, 'store'])->name('transaction.save');
            Route::get('/transaction/created', [TransactionController::class, 'created'])->name('transaction.created');
            Route::get('/transaction/print-orders', [TransactionController::class, 'printOrders'])->name('transaction.print_orders');
            Route::get('/transaction/print-orders-pdf', [TransactionController::class, 'printOrdersPDF'])->name('transaction.print_orders_pdf');

            Route::get('/transaction_detail/{id}/data', [TransactionDetailController::class, 'data'])->name('transaction_detail.data');
            Route::get('/transaction_detail/loadform/{total}/{diterima}', [TransactionDetailController::class, 'loadForm'])->name('transaction_detail.load_form');
            Route::resource('/transaction_detail', TransactionDetailController::class)
                ->except('create', 'show', 'edit');
        });

        Route::group(['middleware' => 'level:1'], function () {
            Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
            Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
            Route::get('/laporan/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

            Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
            Route::resource('/user', UserController::class);
        });

        Route::group(['middleware' => 'level:1,2'], function () {
            Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
            Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.show');
        });
    });
});
