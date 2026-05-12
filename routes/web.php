<?php

use App\Http\Controllers\MagicLoginController;
use Illuminate\Support\Facades\Route;
use App\Models\Decision;
use App\Models\Circle;
use App\Models\Category;
use App\Enums\DecisionVisibility;
use App\Services\ConfigService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Public\SitemapController;

Route::get('/magic-login/{user}', [MagicLoginController::class, 'login'])
    ->name('magic.login')
    ->middleware('signed');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

// --- SEO Public Routes ---

// 1. Détail d'une décision
Route::get('/front/decision/{id}', function ($id) {
    $decision = Decision::with(['currentVersion', 'circle', 'categories'])->find($id);
    $configService = app(ConfigService::class);
    $appName = $configService->get('app_name', 'DAZO');

    if ($decision && $decision->visibility === DecisionVisibility::PUBLIC) {
        $abstract = $decision->currentVersion?->abstract ?: strip_tags($decision->currentVersion?->description ?? $decision->currentVersion?->content ?? '');
        $meta = [
            'title' => $decision->title . ' - ' . $appName,
            'description' => Str::limit($abstract, 160),
            'image' => asset('images/dazo-logo.png'),
            'type' => 'article'
        ];
        return view('welcome', compact('meta'));
    }
    return view('welcome');
})->name('public.decision');

// 2. Pages statiques
Route::get('/front/p/{slug}', function ($slug) {
    $configService = app(ConfigService::class);
    $configs = $configService->all();
    $appName = $configs['app_name'] ?? 'DAZO';
    
    foreach (['legal', 'privacy', 'terms'] as $key) {
        if (($configs['page_' . $key . '_slug'] ?? '') === $slug) {
            $meta = [
                'title' => ($configs['page_' . $key . '_title'] ?? 'Page') . ' - ' . $appName,
                'description' => Str::limit(strip_tags($configs['page_' . $key . '_content'] ?? ''), 160),
                'image' => asset('images/dazo-logo.png'),
                'type' => 'website'
            ];
            return view('welcome', compact('meta'));
        }
    }
    return view('welcome');
})->name('public.page');

// 3. Listing avec filtres dynamiques (SEO)
$frontListingHandler = function (Request $request) {
    $configService = app(ConfigService::class);
    // On ne génère les metas que si le front public est activé
    if ($configService->get('enable_public_front') !== 'true' && !auth()->check()) {
        return view('welcome');
    }

    $appName = $configService->get('app_name', 'DAZO');
    
    $filters = [];
    if ($request->filled('circle')) {
        $circle = Circle::find($request->circle);
        if ($circle) $filters[] = "Cercle : " . $circle->name;
    }
    if ($request->filled('category')) {
        $category = Category::find($request->category);
        if ($category) $filters[] = "Thématique : " . $category->name;
    }
    if ($request->filled('status')) {
        $filters[] = "Phase : " . $request->status;
    }
    if ($request->filled('search')) {
        $filters[] = "Recherche : \"" . $request->search . "\"";
    }

    $suffix = count($filters) > 0 ? " (" . implode(', ', $filters) . ")" : "";
    $descPrefix = count($filters) > 0 ? "Résultats pour " . implode(', ', $filters) . ". " : "";

    $meta = [
        'title' => "Décisions Publiques" . $suffix . " - " . $appName,
        'description' => $descPrefix . "Consultez et suivez les décisions ouvertes de notre organisation sur la plateforme DAZO.",
        'image' => asset('images/dazo-logo.png'),
        'type' => 'website'
    ];

    return view('welcome', compact('meta'));
};

Route::get('/front', $frontListingHandler)->name('public.front');
Route::get('/', $frontListingHandler)->name('home');

// --- Sitemaps ---
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/main.xml', [SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap/circle-{id}.xml', [SitemapController::class, 'circle'])->name('sitemap.circle');

Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Allow: /front\n";
    $content .= "Allow: /sitemap.xml\n";
    $content .= "Allow: /sitemap/*.xml\n\n";
    $content .= "Disallow: /admin\n";
    $content .= "Disallow: /admin/*\n";
    $content .= "Disallow: /api/*\n\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
    
    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Catch-all route to serve the Vue SPA.
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
