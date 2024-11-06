<?php

use App\Http\Controllers\FavoriController;
use App\Http\Controllers\ScheduledTransferController;
use App\Http\Controllers\TransactionHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/transfer', [TransactionController::class, 'transfer']);
    Route::post('/transfer/cancel', [TransactionController::class, 'cancelTransfer']);
    Route::post('/transfer/schedule', [ScheduledTransferController::class, 'schedule']);
    Route::post('/transfer/multiple', [TransactionController::class, 'multipleTransfer']);
    // Routes pour l'historique
    Route::get('/transactions/history', [TransactionHistoryController::class, 'index']);
});
Route::middleware('auth:sanctum')->group(function () {
    // ... autres routes existantes
    Route::post('/favoris', [FavoriController::class, 'add']);
    Route::get('/favoris', [FavoriController::class, 'list']);
});







// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Profil utilisateur
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/users/update-profile', [UserController::class, 'updateProfile']);
    
    // Transactions
    Route::prefix('transactions')->group(function () {
        // Transfert d'argent
        Route::post('/transfer', [TransactionController::class, 'transfer']);
        // Historique des transactions
        Route::get('/history', [TransactionController::class, 'history']);
        // Vérifier le solde
        Route::get('/balance', [TransactionController::class, 'checkBalance']);
        // Vérifier un code QR
        Route::post('/verify-qr', [TransactionController::class, 'verifyQrCode']);
    });
    
    // Gestion du compte
    Route::prefix('account')->group(function () {
        // Activer/désactiver la carte
        Route::put('/toggle-card', [UserController::class, 'toggleCard']);
        // Régénérer le QR code
        Route::post('/regenerate-qr', [UserController::class, 'regenerateQrCode']);
        // Modifier le code PIN
        Route::put('/update-pin', [UserController::class, 'updatePin']);
    });
    
    // Routes Admin (avec middleware supplémentaire pour vérifier le rôle)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/transactions/all', [TransactionController::class, 'allTransactions']);
        Route::post('/users/block', [UserController::class, 'blockUser']);
        Route::post('/users/unblock', [UserController::class, 'unblockUser']);
    });
});

// Route de fallback pour les URL non trouvées
Route::fallback(function(){
    return response()->json([
        'message' => 'Route non trouvée. Veuillez vérifier l\'URL et la méthode HTTP.'
    ], 404);
});