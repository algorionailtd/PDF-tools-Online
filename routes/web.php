<?php 
 
use App\Http\Controllers\AdminPaymentController; 
use App\Http\Controllers\PaymentController; 
use App\Http\Controllers\PdfUsageController; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\ProtectPdfController; 
use App\Http\Controllers\SocialAuthController; 
use Illuminate\Support\Facades\Route; 
 
Route::get('/', function () {  if (! auth()->check()) {  return redirect()->route('login'); 
    } return view('welcome'); })->name('home'); 
 
Route::get('/auth/google', function () { return redirect()->route('login'); }); 
Route::post('/auth/google', [SocialAuthController::class, 'googleLogin'])->name('firebase.google.login'); 
Route::middleware(['auth'])->group(function () { 
 
    Route::get('/compress', function () {  return view('tools.compress');  })->name('compress'); 
    Route::get('/merge', function () {  return view('tools.merge');   })->name('merge'); 
    Route::get('/split', function () {  return view('tools.split');  })->name('split'); 
    Route::get('/rotate', function () {  return view('tools.rotate');  })->name('rotate'); 
    Route::get('/extract', function () {  return view('tools.extract');  })->name('extract'); 
    Route::get('/watermark', function () {  return view('tools.watermark');  })->name('watermark'); 
    Route::get('/jpg-to-pdf', function () {  return view('tools.jpg-to-pdf'); })->name('jpg.to.pdf'); 
    Route::get('/pdf-to-jpg', function () {  return view('tools.pdf-to-jpg');   })->name('pdf.to.jpg'); 
    Route::get('/pdf-to-word', function () {  return view('tools.pdf-to-word');  })->name('pdf.to.word'); 
    Route::get('/word-to-pdf', function () {  return view('tools.word-to-pdf');  })->name('word.to.pdf'); 
    Route::get('/add-signature', function () {   return view('tools.addsignature');  })->name('addsignature'); 
    Route::get('/protect-pdf', function () {   return view('tools.protect-pdf');  })->name('protect.pdf'); 
    Route::post('/protect-pdf/process', [ProtectPdfController::class, 'protect'])->name('protect.pdf.process'); 


    /* 
    |-------------------------------------------------------------------------- 
    | PDF USAGE 
    |-------------------------------------------------------------------------- 
    */ 
 
    Route::post('/pdf-usage/reserve', [PdfUsageController::class, 'reserve'])->name('pdf.usage.reserve'); 
    Route::get('/pdf-usage/status', [PdfUsageController::class, 'status'])->name('pdf.usage.status'); 
 
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment'); 
    Route::get('/payment/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('payment.receipt'); 
    Route::post('/payment/submit', [PaymentController::class, 'submit'])->name('payment.submit'); 
 
    /* 
    |-------------------------------------------------------------------------- 
    | ADMIN PAYMENT MANAGEMENT 
    |-------------------------------------------------------------------------- 
    | 
    | AdminPaymentController checks the ADMIN_EMAIL setting. 
    | 
    */ 
 
    Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments'); 
    Route::get('/admin/payments/{payment}/proof', [AdminPaymentController::class, 'downloadProof'])->name('admin.payments.proof'); 
    Route::post('/admin/payments/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('admin.payments.approve'); 
    Route::post('/admin/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('admin.payments.reject'); 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); 
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); 
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
 
}); 
 
require __DIR__.'/auth.php'; 
 