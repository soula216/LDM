<?php

namespace App\Http\Controllers;

use App\Models\VitrineBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VitrineController extends Controller
{
    private const ACADEMY_PER_PAGE = 20;

    private const ACADEMY_CATEGORIES = [
        'catalogue' => ['label' => 'Catalogues', 'icon' => 'fas fa-book-open'],
        'guide' => ['label' => 'Guides techniques', 'icon' => 'fas fa-drafting-compass'],
        'protocole' => ['label' => 'Protocoles', 'icon' => 'fas fa-clipboard-list'],
        'notice' => ['label' => 'Notices', 'icon' => 'fas fa-file-alt'],
    ];

    public function show(): View
    {
        return view('accueil', [
            'blocks' => $this->loadBlocks(),
        ]);
    }

    public function services(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.services', [
            'blocks' => $blocks,
            'services' => $blocks['services'] ?? [
                'section_label' => 'Nos Services',
                'section_title' => 'Solutions Complètes',
                'section_subtitle' => '',
                'items' => [],
            ],
        ]);
    }

    public function serviceShow(string $slug): View
    {
        $blocks = $this->loadBlocks();
        $services = $blocks['services'] ?? ['items' => []];
        $service = $this->findServiceBySlug(VitrineBlock::activeServiceItems($services['items'] ?? []), $slug);

        if ($service === null) {
            throw new NotFoundHttpException();
        }

        return view('accueil.service-show', [
            'blocks' => $blocks,
            'services' => $services,
            'service' => $service,
        ]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     * @return array<string, mixed>|null
     */
    private function findServiceBySlug(Collection $items, string $slug): ?array
    {
        $slug = Str::slug($slug);

        if ($slug === '') {
            return null;
        }

        return $items->first(function (array $item) use ($slug) {
            $itemSlug = trim((string) ($item['slug'] ?? ''));

            if ($itemSlug !== '') {
                return Str::slug($itemSlug) === $slug;
            }

            return Str::slug((string) ($item['title'] ?? '')) === $slug;
        });
    }

    public function process(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.process', [
            'blocks' => $blocks,
            'process' => $blocks['process'] ?? [
                'section_label' => 'Notre Process',
                'section_title' => 'Comment Nous Travaillons',
                'section_subtitle' => '',
                'steps' => [],
            ],
        ]);
    }

    public function academy(): View
    {
        $blocks = $this->loadBlocks();
        $academy = $blocks['academy'] ?? [
            'section_label' => 'LDM Academy',
            'section_title' => 'Ressources pour les praticiens',
            'section_subtitle' => 'Catalogues, guides techniques, protocoles et notices à télécharger',
            'documents' => [],
        ];

        $allDocuments = $this->academyDocumentsCollection($academy);
        $pageData = $this->paginateAcademyDocuments($allDocuments, 1);

        return view('accueil.academy', [
            'blocks' => $blocks,
            'academy' => $academy,
            'academyCategories' => self::ACADEMY_CATEGORIES,
            'academyDocuments' => $pageData['documents'],
            'academyTotal' => $allDocuments->count(),
            'academyCategoryCounts' => $this->academyCategoryCounts($allDocuments),
            'academyPage' => 1,
            'academyHasMore' => $pageData['has_more'],
        ]);
    }

    public function academyDocuments(Request $request): JsonResponse
    {
        $blocks = $this->loadBlocks();
        $academy = $blocks['academy'] ?? ['documents' => []];
        $allDocuments = $this->academyDocumentsCollection($academy);

        $category = trim((string) $request->query('category', 'all'));
        if ($category !== 'all' && ! array_key_exists($category, self::ACADEMY_CATEGORIES)) {
            $category = 'all';
        }

        if ($category !== 'all') {
            $allDocuments = $allDocuments
                ->filter(fn (array $doc) => ($doc['category'] ?? '') === $category)
                ->values();
        }

        $page = max(1, (int) $request->query('page', 1));
        $pageData = $this->paginateAcademyDocuments($allDocuments, $page);

        return response()->json([
            'html' => view('accueil.partials.academy-cards', [
                'documents' => $pageData['documents'],
                'academyCategories' => self::ACADEMY_CATEGORIES,
            ])->render(),
            'has_more' => $pageData['has_more'],
            'page' => $page,
            'total' => $allDocuments->count(),
        ]);
    }

    private function academyDocumentsCollection(array $academy): Collection
    {
        return collect($academy['documents'] ?? [])
            ->filter(fn ($doc) => filled($doc['file_url'] ?? null))
            ->values();
    }

    private function academyCategoryCounts(Collection $documents): array
    {
        $counts = [];
        foreach (array_keys(self::ACADEMY_CATEGORIES) as $category) {
            $count = $documents->where('category', $category)->count();
            if ($count > 0) {
                $counts[$category] = $count;
            }
        }

        return $counts;
    }

    /**
     * @return array{documents: Collection, has_more: bool}
     */
    private function paginateAcademyDocuments(Collection $documents, int $page): array
    {
        $offset = ($page - 1) * self::ACADEMY_PER_PAGE;
        $slice = $documents->slice($offset, self::ACADEMY_PER_PAGE)->values();

        return [
            'documents' => $slice,
            'has_more' => $documents->count() > ($offset + $slice->count()),
        ];
    }

    private function loadBlocks(): array
    {
        try {
            $blocks = VitrineBlock::allKeyed();
        } catch (\Throwable) {
            $blocks = [];
        }

        if (empty($blocks)) {
            return [
                'header' => ['logo_alt' => 'LDM', 'nav_links' => [], 'client_space_label' => 'Espace client'],
                'hero' => ['slides' => [], 'title_line1' => 'Prothèses Dentaires', 'title_highlight' => 'de Précision', 'buttons' => [], 'card' => ['stats' => []]],
                'services' => ['section_title' => 'Services', 'items' => []],
                'process' => ['section_title' => 'Process', 'steps' => []],
                'gallery' => ['section_title' => 'Galerie', 'items' => []],
                'features' => ['title_before' => 'Pourquoi', 'title_highlight' => 'LDM', 'list' => [], 'card' => []],
                'academy' => ['section_title' => 'LDM Academy', 'documents' => []],
                'contact' => ['title' => 'Contact', 'items' => []],
                'footer' => ['brand_description' => '', 'social_links' => [], 'columns' => [], 'copyright' => 'LDM', 'legal_link' => ['label' => 'Mentions légales', 'href' => '#']],
            ];
        }

        return $blocks;
    }
}
