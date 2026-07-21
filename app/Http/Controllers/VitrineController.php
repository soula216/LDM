<?php

namespace App\Http\Controllers;

use App\Models\VitrineBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VitrineController extends Controller
{
    private const ACADEMY_PER_PAGE = 20;

    public function show(): View
    {
        return view('accueil', [
            'blocks' => $this->loadBlocks(),
        ]);
    }

    public function services(): RedirectResponse
    {
        return redirect()->route('vitrine.about.show', [
            'page' => VitrineBlock::aboutCollaborationPageSlug(),
            'tab' => 'services',
        ], 301);
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

    private function processPage(): View
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

    public function gallery(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.gallery', [
            'blocks' => $blocks,
            'gallery' => $blocks['gallery'] ?? [
                'section_label' => 'Nos Travaux',
                'section_title' => 'Découvrez Nos Réalisations',
                'section_subtitle' => '',
                'items' => [],
            ],
        ]);
    }

    public function about(): RedirectResponse
    {
        return redirect()->route('vitrine.about.show', [
            'page' => VitrineBlock::aboutOverviewPageSlug(),
        ]);
    }

    public function aboutShow(Request $request, string $page): View|RedirectResponse
    {
        $overviewTabs = VitrineBlock::aboutOverviewTabs();
        $workTabs = VitrineBlock::aboutWorkTabs();
        $collaborationTabs = VitrineBlock::aboutCollaborationTabs();

        if (array_key_exists($page, $overviewTabs)) {
            return redirect()->route('vitrine.about.show', [
                'page' => VitrineBlock::aboutOverviewPageSlug(),
                'tab' => $page,
            ], 301);
        }

        if (array_key_exists($page, $workTabs)) {
            return redirect()->route('vitrine.about.show', [
                'page' => VitrineBlock::aboutWorkPageSlug(),
                'tab' => $page,
            ], 301);
        }

        if (array_key_exists($page, $collaborationTabs)) {
            return redirect()->route('vitrine.about.show', [
                'page' => VitrineBlock::aboutCollaborationPageSlug(),
                'tab' => $page,
            ], 301);
        }

        $allowed = array_merge(
            [
                VitrineBlock::aboutOverviewPageSlug(),
                VitrineBlock::aboutWorkPageSlug(),
                VitrineBlock::aboutCollaborationPageSlug(),
            ],
            array_keys(VitrineBlock::aboutSubPages())
        );

        if (! in_array($page, $allowed, true)) {
            throw new NotFoundHttpException();
        }

        if (VitrineBlock::isAboutOverviewPage($page)) {
            $activeTab = trim((string) $request->query('tab', 'qui-sommes-nous'));

            if (! array_key_exists($activeTab, $overviewTabs)) {
                $activeTab = 'qui-sommes-nous';
            }

            return $this->aboutPage($page, $activeTab);
        }

        if (VitrineBlock::isAboutWorkPage($page)) {
            $activeTab = trim((string) $request->query('tab', 'processus-de-qualite'));

            if (! array_key_exists($activeTab, $workTabs)) {
                $activeTab = 'processus-de-qualite';
            }

            return $this->aboutPage($page, 'qui-sommes-nous', $activeTab);
        }

        if (VitrineBlock::isAboutCollaborationPage($page)) {
            $activeTab = trim((string) $request->query('tab', 'services'));

            // Compatibilité avec les liens créés avant que ce slug ne devienne
            // le groupe « Travailler avec LDM ».
            if (array_key_exists($activeTab, $workTabs)) {
                return redirect()->route('vitrine.about.show', [
                    'page' => VitrineBlock::aboutWorkPageSlug(),
                    'tab' => $activeTab,
                ], 301);
            }

            if (! array_key_exists($activeTab, $collaborationTabs)) {
                $activeTab = 'services';
            }

            return $this->aboutPage($page, 'qui-sommes-nous', 'processus-de-qualite', $activeTab);
        }

        if (VitrineBlock::isAboutLaboratoryPage($page)) {
            return $this->laboratoryPage();
        }

        if (VitrineBlock::isAboutProcessPage($page)) {
            return $this->processPage();
        }

        return $this->aboutPage($page);
    }

    private function aboutPage(
        string $page,
        string $activeAboutTab = 'qui-sommes-nous',
        string $activeWorkTab = 'processus-de-qualite',
        string $activeCollaborationTab = 'services'
    ): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.about', [
            'blocks' => $blocks,
            'aboutPage' => $page,
            'activeAboutTab' => $activeAboutTab,
            'activeWorkTab' => $activeWorkTab,
            'activeCollaborationTab' => $activeCollaborationTab,
            'services' => $blocks['services'] ?? [
                'section_label' => 'Nos Services',
                'section_title' => 'Solutions Complètes',
                'section_subtitle' => '',
                'items' => [],
            ],
            'process' => $blocks['process'] ?? [
                'section_label' => 'Notre Process',
                'section_title' => 'Comment Nous Travaillons',
                'section_subtitle' => '',
                'steps' => [],
            ],
            'about' => $blocks['about'] ?? [
                'section_label' => 'Le Laboratoire',
                'title' => 'Notre laboratoire',
                'description' => '',
                'sections_kicker' => 'En détail',
                'sections_heading' => 'Nos engagements & expertises',
                'sections_lead' => 'Découvrez les piliers qui structurent notre laboratoire et notre relation avec les praticiens.',
                'sections' => [],
                'info_pages' => [],
                'media_page' => [
                    'section_label' => 'Certifications',
                    'title' => 'Certifications',
                    'description' => '',
                    'photos' => [],
                ],
                'photos' => [],
                'videos' => [],
            ],
        ]);
    }

    private function laboratoryPage(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.laboratory', [
            'blocks' => $blocks,
            'laboratory' => $blocks['laboratory'] ?? [
                'section_label' => 'Galerie équipe / équipement',
                'title' => 'Notre équipe & nos installations',
                'description' => '',
                'photos' => [],
                'videos' => [],
            ],
        ]);
    }

    public function faq(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.faq', [
            'blocks' => $blocks,
            'faq' => $blocks['faq'] ?? [
                'section_label' => 'FAQ',
                'section_title' => 'Foire Aux Questions',
                'section_subtitle' => '',
                'items' => [],
            ],
        ]);
    }

    public function vosPatients(): View
    {
        $blocks = $this->loadBlocks();
        $page = $blocks['vos-patients'] ?? [
            'section_label' => 'Vos patients',
            'section_title' => 'Des sourires qui parlent',
            'section_subtitle' => '',
            'categories' => [],
            'videos' => [],
        ];

        $videos = VitrineBlock::activeVosPatientsVideos($page['videos'] ?? []);
        $categories = VitrineBlock::resolveVosPatientsCategories($page);
        $categoryCounts = [];

        foreach (array_keys($categories) as $categoryKey) {
            $count = $videos->where('category', $categoryKey)->count();
            if ($count > 0) {
                $categoryCounts[$categoryKey] = $count;
            }
        }

        return view('accueil.vos-patients', [
            'blocks' => $blocks,
            'vosPatients' => $page,
            'videos' => $videos,
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
        ]);
    }

    public function recrutement(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.recrutement', [
            'blocks' => $blocks,
            'recrutement' => $blocks['recrutement'] ?? [
                'section_label' => 'Recrutement',
                'section_title' => 'Rejoindre LDM',
                'section_subtitle' => '',
                'items' => [],
            ],
        ]);
    }

    public function mentionsLegales(): View
    {
        $blocks = $this->loadBlocks();

        return view('accueil.mentions-legales', [
            'blocks' => $blocks,
            'mentionsLegales' => $blocks['mentions-legales'] ?? [
                'section_label' => 'Mentions légales',
                'section_title' => 'Mentions légales',
                'content_html' => '',
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
        $academyCategories = VitrineBlock::resolveAcademyCategories($academy);

        return view('accueil.academy', [
            'blocks' => $blocks,
            'academy' => $academy,
            'academyCategories' => $academyCategories,
            'academyDocuments' => $pageData['documents'],
            'academyTotal' => $allDocuments->count(),
            'academyCategoryCounts' => $this->academyCategoryCounts($allDocuments, $academyCategories),
            'academyPage' => 1,
            'academyHasMore' => $pageData['has_more'],
        ]);
    }

    public function academyDocuments(Request $request): JsonResponse
    {
        $blocks = $this->loadBlocks();
        $academy = $blocks['academy'] ?? ['documents' => []];
        $allDocuments = $this->academyDocumentsCollection($academy);
        $academyCategories = VitrineBlock::resolveAcademyCategories($academy);

        $category = trim((string) $request->query('category', 'all'));
        if ($category !== 'all' && ! array_key_exists($category, $academyCategories)) {
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
                'academyCategories' => $academyCategories,
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

    private function academyCategoryCounts(Collection $documents, array $academyCategories): array
    {
        $counts = [];
        foreach (array_keys($academyCategories) as $category) {
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
                'about' => ['title' => 'Le Laboratoire', 'description' => '', 'photos' => [], 'videos' => []],
                'laboratory' => ['title' => 'Galerie équipe / équipement', 'section_label' => 'Galerie équipe / équipement', 'description' => '', 'photos' => [], 'videos' => []],
                'features' => ['title_before' => 'Pourquoi', 'title_highlight' => 'LDM', 'list' => [], 'card' => []],
                'partners' => ['section_title' => 'Nos Partenaires', 'items' => []],
                'academy' => ['section_title' => 'LDM Academy', 'documents' => []],
                'faq' => ['section_title' => 'Foire Aux Questions', 'items' => []],
                'vos-patients' => ['section_title' => 'Des sourires qui parlent', 'categories' => [], 'videos' => []],
                'recrutement' => ['section_title' => 'Rejoindre LDM', 'items' => []],
                'contact' => ['title' => 'Contact', 'items' => []],
                'footer' => ['brand_description' => '', 'social_links' => [], 'columns' => [], 'copyright' => 'LDM', 'legal_link' => ['label' => 'Mentions légales', 'href' => '/mentions-legales']],
                'mentions-legales' => ['section_label' => 'Mentions légales', 'section_title' => 'Mentions légales', 'content_html' => ''],
            ];
        }

        return $blocks;
    }
}
