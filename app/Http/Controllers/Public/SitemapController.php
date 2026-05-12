<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\Category;
use App\Models\Decision;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Index du Sitemap (Sitemap Index)
     */
    public function index()
    {
        // On récupère les cercles qui ont au moins une décision publique
        $circles = Circle::whereHas('decisions', function ($q) {
            $q->whereIn('status', ['draft', 'clarification', 'reaction', 'objection', 'adopted', 'abandoned']);
        })->get();

        return response()->view('sitemaps.index', [
            'circles' => $circles
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Sitemap principal (Accueil + Catégories)
     */
    public function main()
    {
        $categories = Category::whereHas('decisions')->get();
        
        return response()->view('sitemaps.main', [
            'categories' => $categories
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Sitemap par cercle (Décisions)
     */
    public function circle($id)
    {
        $circle = Circle::findOrFail($id);
        $decisions = Decision::where('circle_id', $id)
            ->whereIn('status', ['draft', 'clarification', 'reaction', 'objection', 'adopted', 'abandoned'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->view('sitemaps.circle', [
            'circle' => $circle,
            'decisions' => $decisions
        ])->header('Content-Type', 'text/xml');
    }
}
