<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentification (Public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::post('/auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

    // Authentification (Protégé)
    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        Route::get('/auth/me', [ProfileController::class, 'me']);
        Route::put('/auth/me', [ProfileController::class, 'update']);

        Route::post('/auth/email/resend', [AuthController::class, 'resendEmailVerification']);

        // Cercles CRUD
        Route::apiResource('circles', \App\Http\Controllers\Api\V1\CircleController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
            
        // Catégories (lecture publique)
        Route::get('/categories', [\App\Http\Controllers\Api\V1\CategoryController::class, 'index']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);
        
        // Utilisateurs
        Route::get('/users/me', [\App\Http\Controllers\Api\V1\UserController::class, 'me']);
        Route::get('/users/search', [\App\Http\Controllers\Api\V1\UserController::class, 'search']);

        // Données de navigation (compteurs en attente)
        Route::get('/pending-counts', [\App\Http\Controllers\Api\V1\PendingCountsController::class, 'index']);
        Route::get('/pending-items', [\App\Http\Controllers\Api\V1\PendingItemsController::class, 'index']);

        // Cercles Adhésion & Membres
        Route::get('/circles/{circle}/members', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'index']);
        Route::post('/circles/{circle}/join', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'join']);
        Route::post('/circles/{circle}/leave', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'leave']);
        Route::put('/circles/{circle}/members/{user}', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'update']);
        Route::delete('/circles/{circle}/members/{user}', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'destroy']);

        // Invitations
        Route::post('/invitations', [\App\Http\Controllers\Api\V1\InvitationController::class, 'store']);
        Route::post('/invitations/{token}/accept', [\App\Http\Controllers\Api\V1\InvitationController::class, 'accept']);

        // Moteur de Décision (Core)
        // Templates (Models)
        Route::apiResource('models', \App\Http\Controllers\Api\V1\DecisionModelController::class);

        // Initialisation de cycle & Récupération depuis le cercle
        Route::get('/decisions', [\App\Http\Controllers\Api\V1\DecisionController::class, 'mine']);
        Route::get('/circles/{circle}/decisions', [\App\Http\Controllers\Api\V1\DecisionController::class, 'index']);
        Route::post('/circles/{circle}/decisions', [\App\Http\Controllers\Api\V1\DecisionController::class, 'store']);

        // Lecture et modification d'une décision spécifique
        Route::get('/decisions/{id}', [\App\Http\Controllers\Api\V1\DecisionController::class, 'show']);
        Route::put('/decisions/{id}', [\App\Http\Controllers\Api\V1\DecisionController::class, 'update']);
        Route::delete('/decisions/{id}', [\App\Http\Controllers\Api\V1\DecisionController::class, 'destroy']);
        Route::put('/decisions/{id}/animator', [\App\Http\Controllers\Api\V1\DecisionController::class, 'updateAnimator']);

        // Lecture et création des versions d'une décision
        Route::get('/decisions/{decision_id}/versions', [\App\Http\Controllers\Api\V1\DecisionVersionController::class, 'index']);
        Route::post('/decisions/{decisionId}/versions', [\App\Http\Controllers\Api\V1\DecisionVersionController::class, 'store']);
        Route::get('/decisions/{decision_id}/versions/{version_id}', [\App\Http\Controllers\Api\V1\DecisionVersionController::class, 'show']);

        // Transitions (Machine à États)
        Route::post('/decisions/{decision_id}/transition', [\App\Http\Controllers\Api\V1\DecisionTransitionController::class, 'transition']);
        Route::post('/decisions/{decision_id}/abandon', [\App\Http\Controllers\Api\V1\DecisionTransitionController::class, 'abandon']);

        // Feedback & Joins
        Route::get('/decisions/{id}/feedback', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'index']);
        Route::post('/decisions/{id}/feedback', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'store']);
        Route::get('/decisions/{id}/feedback/{feedbackId}', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'show']);
        Route::put('/decisions/{id}/feedback/{feedbackId}/status', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'updateStatus']);
        Route::post('/feedback/{feedbackId}/join', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'join']);

        // Feedback Messages
        Route::get('/feedback/{feedbackId}/messages', [\App\Http\Controllers\Api\V1\FeedbackMessageController::class, 'index']);
        Route::post('/feedback/{feedbackId}/messages', [\App\Http\Controllers\Api\V1\FeedbackMessageController::class, 'store']);

        // Consentements
        Route::get('/decisions/{id}/versions/{versionId}/consent', [\App\Http\Controllers\Api\V1\ConsentController::class, 'index']);
        Route::post('/decisions/{id}/versions/{versionId}/consent', [\App\Http\Controllers\Api\V1\ConsentController::class, 'store']);

        // Pièces Jointes
        Route::post('/attachments', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'store']);
        Route::post('/decisions/versions/{versionId}/attachments/link', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'link']);
        Route::delete('/attachments/{attachment}', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'destroy']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);

        // Administration
        Route::prefix('admin')->middleware(['admin'])->group(function () {
            Route::get('/config', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'index']);
            Route::put('/config', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'update']);
            
            // Impersonation
            Route::post('/impersonate/{user}', [\App\Http\Controllers\Api\V1\Admin\ImpersonationController::class, 'impersonate']);
            
            // Users CRUD
            Route::apiResource('users', \App\Http\Controllers\Api\V1\Admin\UserController::class)
                ->only(['index', 'update', 'destroy']);
                
            // Categories CRUD
            Route::apiResource('categories', \App\Http\Controllers\Api\V1\Admin\CategoryController::class)
                ->except(['show']);
        });
    });
});
