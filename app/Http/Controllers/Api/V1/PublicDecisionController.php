<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Decision;
use App\Models\Circle;
use App\Models\Category;
use App\Models\User;
use App\Services\ConfigService;
use App\Services\XmlResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class PublicDecisionController extends Controller
{
    public function __construct(
        private ConfigService $configService,
        private XmlResponseService $xmlService
    ) {}

    public function indexFront(Request $request): JsonResponse
    {
        if (filter_var($this->configService->get('enable_public_front'), FILTER_VALIDATE_BOOLEAN) !== true) {
            abort(404);
        }

        $query = $this->buildBaseQuery();
        $this->applyFilters($query, $request);
        
        $decisions = $query->paginate(20);

        // We can just return the paginator for JSON
        return response()->json($decisions);
    }

    public function showFront($id): JsonResponse
    {
        if (filter_var($this->configService->get('enable_public_front'), FILTER_VALIDATE_BOOLEAN) !== true) {
            abort(404);
        }

        $decision = $this->buildBaseQuery()
                         ->with([
                             'currentVersion.attachments',
                             'currentVersion.feedbacks.author:id,name',
                             'participants:id,decision_id,user_id,role',
                             'categories',
                             'circle',
                         ])
                         ->findOrFail($id);

        // Annotate each feedback with the author's participant role in this decision
        $roleMap = $decision->participants->pluck('role', 'user_id');
        $decision->currentVersion?->feedbacks?->each(function ($fb) use ($roleMap) {
            $role = $roleMap[$fb->author_id] ?? 'participant';
            $fb->author_role = (is_object($role) && isset($role->value)) ? $role->value : $role;
        });

        return response()->json(['decision' => $decision]);
    }

    /**
     * Return metadata needed by the public front: allowed filters, circles, categories, statuses.
     */
    public function meta(): JsonResponse
    {
        if (filter_var($this->configService->get('enable_public_front'), FILTER_VALIDATE_BOOLEAN) !== true) {
            abort(404);
        }

        $allowedFilters   = $this->configService->get('public_filters', []);
        $publicCircleIds  = $this->configService->get('public_circles', []);
        $publicCatIds     = $this->configService->get('public_categories', []);
        $publicStatuses   = $this->configService->get('public_statuses', []);

        if (!is_array($allowedFilters))  $allowedFilters  = [];
        if (!is_array($publicCircleIds)) $publicCircleIds = [];
        if (!is_array($publicCatIds))    $publicCatIds    = [];
        if (!is_array($publicStatuses))  $publicStatuses  = [];

        $circles = !empty($publicCircleIds)
            ? Circle::whereIn('id', $publicCircleIds)->select('id','name')->get()
            : Circle::select('id','name')->get();  // fallback: all circles

        $categories = !empty($publicCatIds)
            ? Category::whereIn('id', $publicCatIds)->select('id','name','color_hex')->get()
            : Category::select('id','name','color_hex')->get(); // fallback: all categories

        $allStatusMap = [
            'draft'         => 'Brouillon',
            'clarification' => 'Clarification',
            'reaction'      => 'Réaction',
            'objection'     => 'Objection',
            'adopted'       => 'Adoptée',
            'abandoned'     => 'Abandonnée',
        ];

        // If public_statuses is configured, use those; otherwise expose all
        $statusKeys = !empty($publicStatuses) ? $publicStatuses : array_keys($allStatusMap);
        $statuses = collect($statusKeys)->map(fn($s) => [
            'value' => $s,
            'label' => $allStatusMap[$s] ?? $s,
        ])->values();

        $authors = User::whereHas('participations', function($q) {
            $q->where('role', 'author');
        })->select('id', 'name')->get();

        return response()->json([
            'allowed_filters' => $allowedFilters,
            'circles'         => $circles,
            'categories'      => $categories,
            'statuses'        => $statuses,
            'authors'         => $authors,
        ]);
    }

    /**
     * Apply query filters. All standard filters (search, status, circle, category) are always
     * available on the public front — no gate on public_filters config needed here.
     */
    private function applyFilters($query, Request $request): void
    {
        // Texte libre — titre ou auteur
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->query('search') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhereHas('participants', function($q2) use ($searchTerm) {
                      $q2->where('role', 'author')
                         ->whereHas('user', function($q3) use ($searchTerm) {
                             $q3->where('name', 'like', $searchTerm);
                         });
                  });
            });
        }

        // Phase / statut
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Cercle
        if ($request->filled('circle')) {
            $query->where('circle_id', $request->query('circle'));
        }

        // Catégorie
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->query('category'));
            });
        }

        // Auteur
        if ($request->filled('author')) {
            $query->whereHas('participants', function ($q) use ($request) {
                $q->where('role', 'author')
                  ->where('user_id', $request->query('author'));
            });
        }

        // Tri
        $sort = $request->query('sort', 'created_desc');
        match ($sort) {
            'created_asc'  => $query->orderBy('created_at', 'asc'),
            'updated_desc' => $query->orderBy('updated_at', 'desc'),
            'updated_asc'  => $query->orderBy('updated_at', 'asc'),
            'alpha_asc'    => $query->orderBy('title', 'asc'),
            'alpha_desc'   => $query->orderBy('title', 'desc'),
            default        => $query->orderBy('created_at', 'desc'),
        };
    }

    public function index(Request $request): Response
    {
        $query = $this->buildBaseQuery();
        $this->applyFilters($query, $request);

        $decisions = $query->paginate(20);

        $xml = $this->xmlService->formatDecisionsList($decisions);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function show($id): Response
    {
        $decision = $this->buildBaseQuery()
                         ->with(['currentVersion', 'categories', 'circle'])
                         ->findOrFail($id);

        $xml = $this->xmlService->formatDecisionDetail($decision);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Build the base query applying the strict publication rules.
     */
    private function buildBaseQuery()
    {
        $query = Decision::query()->with(['circle', 'categories', 'currentVersion']);

        // Rule 1: Decision itself must be public
        $query->where('visibility', 'public');

        // Extract settings
        $publicCircles = $this->configService->get('public_circles', []);
        $publicCategories = $this->configService->get('public_categories', []);
        $publicStatuses = $this->configService->get('public_statuses', []);

        // Ensure they are arrays
        if (!is_array($publicCircles)) $publicCircles = [];
        if (!is_array($publicCategories)) $publicCategories = [];
        if (!is_array($publicStatuses)) $publicStatuses = [];

        // Rule 2: Must belong to an allowed circle
        if (!empty($publicCircles)) {
            $query->whereIn('circle_id', $publicCircles);
        } else {
            // If no circle is configured as public, nothing is public
            $query->whereRaw('1 = 0');
        }

        // Rule 3: Must have at least one allowed category
        if (!empty($publicCategories)) {
            $query->whereHas('categories', function($q) use ($publicCategories) {
                $q->whereIn('categories.id', $publicCategories);
            });
        } else {
             $query->whereRaw('1 = 0');
        }

        // Rule 4: Must have an allowed status
        if (!empty($publicStatuses)) {
            $query->whereIn('status', $publicStatuses);
        } else {
             $query->whereRaw('1 = 0');
        }

        return $query;
    }
}
