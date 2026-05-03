<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Configuration publique (logo, front, etc.)
    Route::get('/init', [\App\Http\Controllers\Api\V1\InitController::class, 'index']);

    // SPA Publique (sans API Key)
    Route::get('/front/decisions', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'indexFront']);
    Route::get('/front/decisions/suggestions', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'suggestions']);
    Route::get('/front/decisions/{id}', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'showFront']);
    Route::post('/front/decisions/{id}/share', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'incrementShare']);
    Route::get('/front/meta', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'meta']);

    // Authentification (Public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('/auth/reset-password', [PasswordResetController::class, 'reset']);

    Route::post('/auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

    // Public Invitation check
    Route::get('/invitations/{token}', [\App\Http\Controllers\Api\V1\InvitationController::class, 'show']);

    // OAuth / SSO (Public — redirects & callbacks)
    Route::get('/auth/social/{provider}/redirect', [\App\Http\Controllers\Api\V1\SocialAuthController::class, 'redirect']);
    Route::get('/auth/social/{provider}/callback', [\App\Http\Controllers\Api\V1\SocialAuthController::class, 'callback']);

    // Téléchargement de pièces jointes (accès public possible si la décision est publique)
    Route::get('/attachments/{attachment}/download', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'download'])
        ->name('attachments.download');

    // API Publique XML (CMS tiers)
    Route::prefix('public')->middleware(['throttle:60,1', \App\Http\Middleware\EnsureValidApiKey::class])->group(function () {
        Route::get('/decisions', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'index']);
        Route::get('/decisions/{id}', [\App\Http\Controllers\Api\V1\PublicDecisionController::class, 'show']);
    });

    // Authentification (Protégé)
    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        Route::get('/auth/me', [ProfileController::class, 'me']);
        Route::put('/auth/me', [ProfileController::class, 'update']);
        Route::put('/auth/password', [ProfileController::class, 'updatePassword']);
        Route::get('/auth/notifications', [ProfileController::class, 'getNotificationPreferences']);
        Route::put('/auth/notifications', [ProfileController::class, 'updateNotificationPreferences']);

        Route::post('/auth/email/resend', [AuthController::class, 'resendEmailVerification']);

        // Cercles CRUD
        Route::apiResource('circles', \App\Http\Controllers\Api\V1\CircleController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
            
        // Catégories (lecture publique)
        Route::get('/categories', [\App\Http\Controllers\Api\V1\CategoryController::class, 'index']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);
        
        // Wiki (lecture publique)
        Route::get('/wiki', [\App\Http\Controllers\Api\V1\WikiController::class, 'index']);
        Route::get('/wiki/{slug}', [\App\Http\Controllers\Api\V1\WikiController::class, 'show']);
        
        // Utilisateurs
        Route::get('/users/me', [\App\Http\Controllers\Api\V1\UserController::class, 'me']);
        Route::get('/users/admins', [\App\Http\Controllers\Api\V1\UserController::class, 'admins']);
        Route::get('/users/search', [\App\Http\Controllers\Api\V1\UserController::class, 'search']);

        // Contact
        Route::post('/contact/admin', [\App\Http\Controllers\Api\V1\ContactController::class, 'sendToAdmin']);

        // OAuth — Gestion des comptes liés (protégé)
        Route::get('/auth/social/accounts', [\App\Http\Controllers\Api\V1\SocialAuthController::class, 'accounts']);
        Route::delete('/auth/social/{provider}/unlink', [\App\Http\Controllers\Api\V1\SocialAuthController::class, 'unlink']);

        // Données de navigation (compteurs en attente)
        Route::get('/pending-counts', [\App\Http\Controllers\Api\V1\PendingCountsController::class, 'index']);
        Route::get('/pending-items', [\App\Http\Controllers\Api\V1\PendingItemsController::class, 'index']);

        // Cercles Adhésion & Membres
        Route::get('/circles/{circle}/members', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'index']);
        Route::post('/circles/{circle}/members', [\App\Http\Controllers\Api\V1\CircleMemberController::class, 'store']);
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
        Route::post('/decisions/{id}/favorite', [\App\Http\Controllers\Api\V1\DecisionUserSettingController::class, 'toggleFavorite']);
        Route::put('/decisions/{id}/notifications', [\App\Http\Controllers\Api\V1\DecisionUserSettingController::class, 'setNotificationLevel']);
        Route::get('/decisions/{id}/pending-participants', [\App\Http\Controllers\Api\V1\DecisionController::class, 'getPendingParticipants']);
        Route::post('/decisions/{id}/remind', [\App\Http\Controllers\Api\V1\DecisionController::class, 'remind'])
            ->middleware('throttle:5,1'); // 5 relances par minute max

        // Lecture et création des versions d'une décision
        Route::get('/decisions/{decision_id}/versions', [\App\Http\Controllers\Api\V1\DecisionVersionController::class, 'index']);
        Route::post('/decisions/{decisionId}/versions', [\App\Http\Controllers\Api\V1\DecisionVersionController::class, 'store']);
        Route::get('/decisions/{decision_id}/versions/{version_id}', [\App\Http\Controllers\Api\V1\DecisionVersionController::class, 'show']);

        // Transitions (Machine à États)
        Route::post('/decisions/{decision_id}/transition', [\App\Http\Controllers\Api\V1\DecisionTransitionController::class, 'transition'])
            ->middleware('throttle:20,1');
        Route::post('/decisions/{decision_id}/abandon', [\App\Http\Controllers\Api\V1\DecisionTransitionController::class, 'abandon'])
            ->middleware('throttle:20,1');
        Route::post('/decisions/{decision_id}/extend', [\App\Http\Controllers\Api\V1\DecisionTransitionController::class, 'extend'])
            ->middleware('throttle:20,1');
        Route::post('/decisions/{decision_id}/rollback-phase', [\App\Http\Controllers\Api\V1\DecisionTransitionController::class, 'rollbackPhase'])
            ->middleware('throttle:20,1');

        // Feedback & Joins
        Route::get('/decisions/{id}/feedback', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'index']);
        Route::post('/decisions/{id}/feedback', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'store'])
            ->middleware('throttle:10,1');
        Route::get('/decisions/{id}/feedback/{feedbackId}', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'show']);
        Route::put('/decisions/{id}/feedback/{feedbackId}/status', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'updateStatus'])
            ->middleware('throttle:20,1');
        Route::post('/feedback/{feedbackId}/join', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'join'])
            ->middleware('throttle:10,1');
        Route::delete('/feedback/{feedbackId}', [\App\Http\Controllers\Api\V1\FeedbackController::class, 'destroy'])
            ->middleware('throttle:20,1');

        // Feedback Messages
        Route::get('/feedback/{feedbackId}/messages', [\App\Http\Controllers\Api\V1\FeedbackMessageController::class, 'index']);
        Route::post('/feedback/{feedbackId}/messages', [\App\Http\Controllers\Api\V1\FeedbackMessageController::class, 'store'])
            ->middleware('throttle:30,1');
        Route::delete('/feedback/messages/{messageId}', [\App\Http\Controllers\Api\V1\FeedbackMessageController::class, 'destroy'])
            ->middleware('throttle:20,1');

        // Consentements
        Route::get('/decisions/{id}/versions/{versionId}/consent', [\App\Http\Controllers\Api\V1\ConsentController::class, 'index']);
        Route::post('/decisions/{id}/versions/{versionId}/consent', [\App\Http\Controllers\Api\V1\ConsentController::class, 'store'])
            ->middleware('throttle:10,1');
        Route::delete('/consents/{consentId}', [\App\Http\Controllers\Api\V1\ConsentController::class, 'destroy'])
            ->middleware('throttle:20,1');

        // Pièces Jointes
        Route::post('/attachments', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'store'])
            ->middleware('throttle:20,1');
        Route::post('/decisions/versions/{versionId}/attachments/link', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'link']);
        Route::delete('/attachments/{attachment}', [\App\Http\Controllers\Api\V1\AttachmentController::class, 'destroy']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);

        // Administration
        Route::prefix('admin')->as('admin.')->middleware(['admin'])->group(function () {
            Route::get('/config', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'index']);
            Route::put('/config', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'update']);
            Route::post('/config/logo', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'uploadLogo']);
            Route::post('/config/test-email', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'testEmail']);
            Route::post('/config/api-key', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'generateApiKey']);
            Route::delete('/config/api-key', [\App\Http\Controllers\Api\V1\Admin\ConfigController::class, 'revokeApiKey']);
            Route::get('/stats', [\App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'stats']);
            
            // Impersonation
            Route::post('/impersonate/{user}', [\App\Http\Controllers\Api\V1\Admin\ImpersonationController::class, 'impersonate']);
            
            // Users CRUD
            Route::apiResource('users', \App\Http\Controllers\Api\V1\Admin\UserController::class)
                ->only(['index', 'store', 'update', 'destroy'])
                ->names('admin.users');
            
            Route::get('/users/{user}/circles', [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'userCircles']);
                
            // Categories CRUD
            Route::apiResource('categories', \App\Http\Controllers\Api\V1\Admin\CategoryController::class)
                ->except(['show'])
                ->names('admin.categories');

            // Circles CRUD
            Route::apiResource('circles', \App\Http\Controllers\Api\V1\Admin\CircleController::class)
                ->names('admin.circles');
            Route::post('/circles/{circle}/members', [\App\Http\Controllers\Api\V1\Admin\CircleController::class, 'addMember']);
            Route::post('/circles/{circle}/invitations/{invitation}/resend', [\App\Http\Controllers\Api\V1\Admin\CircleController::class, 'resendInvitation']);
            Route::delete('/circles/{circle}/invitations/{invitation}', [\App\Http\Controllers\Api\V1\Admin\CircleController::class, 'removeInvitation']);

            Route::delete('/circles/{circle}/members/{user}', [\App\Http\Controllers\Api\V1\Admin\CircleController::class, 'removeMember']);
            Route::put('/circles/{circle}/members/{user}', [\App\Http\Controllers\Api\V1\Admin\CircleController::class, 'updateMemberRole']);

            // Wiki Admin CRUD
            Route::get('wiki/categories/search', [\App\Http\Controllers\Api\V1\Admin\WikiController::class, 'searchCategories']);
            Route::post('wiki/reorder', [\App\Http\Controllers\Api\V1\Admin\WikiController::class, 'reorder']);
            Route::put('wiki/categories/{category}', [\App\Http\Controllers\Api\V1\Admin\WikiController::class, 'updateCategory']);
            Route::delete('wiki/categories/{category}', [\App\Http\Controllers\Api\V1\Admin\WikiController::class, 'destroyCategory']);
            Route::apiResource('wiki', \App\Http\Controllers\Api\V1\Admin\WikiController::class);

            // Monitoring & Outils
            Route::prefix('tools')->group(function () {
                Route::get('/database', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'databaseStats']);
                Route::post('/database/backup', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'backup']);
                Route::post('/database/restore', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'restore']);
                Route::get('/database/backups/{filename}/url', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'getDownloadUrl']);
                Route::delete('/database/backups/{filename}', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'deleteBackup']);
                Route::get('/server', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'serverStats']);
                Route::get('/logs', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'logs']);
            });
        });
    });
});

// Route de téléchargement publique mais signée (pour contourner les limitations des SPAs lors des téléchargements directs)
Route::get('/v1/admin/backups/{filename}/download', [\App\Http\Controllers\Api\V1\Admin\AdminToolController::class, 'downloadBackup'])
    ->name('admin.backup.download')
    ->middleware('signed');
