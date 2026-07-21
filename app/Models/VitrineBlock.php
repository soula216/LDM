<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VitrineBlock extends Model
{
    protected $fillable = [
        'key',
        'label',
        'content',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Normalise une URL d'image vitrine (upload ou externe) pour l'affichage.
     * Les fichiers uploadés utilisent un chemin absolu depuis la racine web : /storage/...
     */
    public static function resolveImageUrl(?string $imageUrl): string
    {
        if ($imageUrl === null || $imageUrl === '') {
            return '';
        }

        $imageUrl = trim($imageUrl);

        // URL externe (Unsplash, etc.)
        if (preg_match('#^https?://#i', $imageUrl)) {
            $path = parse_url($imageUrl, PHP_URL_PATH) ?? '';
            if ($path && str_starts_with($path, '/storage/')) {
                return $path;
            }

            return $imageUrl;
        }

        if (str_starts_with($imageUrl, '/storage/')) {
            return $imageUrl;
        }

        if (str_starts_with($imageUrl, 'storage/')) {
            return '/' . $imageUrl;
        }

        if (str_starts_with($imageUrl, 'vitrine/')) {
            return '/storage/' . $imageUrl;
        }

        return $imageUrl;
    }

    /**
     * URL absolue complète (schéma + hôte) pour prévisualisation.
     */
    public static function resolveImageAbsoluteUrl(?string $imageUrl): string
    {
        $resolved = static::resolveImageUrl($imageUrl);

        if ($resolved === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $resolved)) {
            return $resolved;
        }

        if (! str_starts_with($resolved, '/')) {
            $resolved = '/' . $resolved;
        }

        if (! app()->runningInConsole() && request()->hasHeader('Host')) {
            return request()->getSchemeAndHttpHost() . $resolved;
        }

        return url($resolved);
    }

    public static function allKeyed(): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key')
            ->map(fn (self $block) => static::resolveContentImages($block->content, $block->key))
            ->all();
    }

    public static function resolveLogoDisplayUrl(?string $logoUrl): string
    {
        if ($logoUrl === null || trim($logoUrl) === '') {
            return asset('logo_ldm.png');
        }

        $absolute = static::resolveImageAbsoluteUrl($logoUrl);

        return $absolute !== '' ? $absolute : asset('logo_ldm.png');
    }

    /**
     * Résout les liens du site public (ancres homepage → pages dédiées).
     */
    public static function resolvePublicHref(?string $href): string
    {
        $href = trim((string) $href);
        $homeUrl = route('vitrine');

        if ($href === '' || $href === '#') {
            return $homeUrl;
        }

        $normalized = rtrim(strtolower($href), '/');

        if (preg_match('#^/(?:le-laboratoire|about)(?:/([^/?]+))?$#', $normalized, $matches) === 1) {
            $subPage = $matches[1] ?? null;

            // Ancien slug Galerie sous Le Laboratoire
            if ($subPage === 'gallery') {
                $subPage = static::aboutLaboratoryPageSlug();
            }

            if (is_string($subPage) && static::isAboutOverviewPage($subPage)) {
                return route('vitrine.about.show', ['page' => static::aboutOverviewPageSlug()]);
            }

            if (is_string($subPage) && array_key_exists($subPage, static::aboutOverviewTabs())) {
                return route('vitrine.about.show', [
                    'page' => static::aboutOverviewPageSlug(),
                    'tab' => $subPage,
                ]);
            }

            if (is_string($subPage) && static::isAboutWorkPage($subPage)) {
                return route('vitrine.about.show', ['page' => static::aboutWorkPageSlug()]);
            }

            if (is_string($subPage) && array_key_exists($subPage, static::aboutWorkTabs())) {
                return route('vitrine.about.show', [
                    'page' => static::aboutWorkPageSlug(),
                    'tab' => $subPage,
                ]);
            }

            if (is_string($subPage) && static::isAboutCollaborationPage($subPage)) {
                return route('vitrine.about.show', ['page' => static::aboutCollaborationPageSlug()]);
            }

            if (is_string($subPage) && array_key_exists($subPage, static::aboutCollaborationTabs())) {
                return route('vitrine.about.show', [
                    'page' => static::aboutCollaborationPageSlug(),
                    'tab' => $subPage,
                ]);
            }

            if (is_string($subPage) && array_key_exists($subPage, static::aboutSubPages())) {
                return route('vitrine.about.show', ['page' => $subPage]);
            }

            return route('vitrine.about');
        }

        if (in_array($normalized, ['#laboratoire', '/laboratoire', 'laboratoire'], true)) {
            return route('vitrine.about.show', ['page' => static::aboutLaboratoryPageSlug()]);
        }

        if (in_array($normalized, ['#process', '/process', 'process'], true)) {
            return route('vitrine.about.show', ['page' => static::aboutProcessPageSlug()]);
        }

        $pageRoutes = [
            'academy' => 'vitrine.academy',
            'gallery' => 'vitrine.gallery',
            'faq' => 'vitrine.faq',
            'vos-patients' => 'vitrine.vos-patients',
            'recrutement' => 'vitrine.recrutement',
            'mentions-legales' => 'vitrine.mentions-legales',
        ];

        foreach ($pageRoutes as $slug => $routeName) {
            if (in_array($normalized, ["#{$slug}", "/{$slug}", $slug], true)) {
                return route($routeName);
            }
        }

        if (in_array($normalized, ['#services', '/services', 'services'], true)) {
            return route('vitrine.about.show', [
                'page' => static::aboutCollaborationPageSlug(),
                'tab' => 'services',
            ]);
        }

        if ($normalized === '#accueil') {
            return $homeUrl;
        }

        if (str_starts_with($href, '#')) {
            return $homeUrl . $href;
        }

        return $href;
    }

    /**
     * Détecte un lien WhatsApp (wa.me, api.whatsapp.com, etc.).
     */
    public static function isWhatsAppHref(?string $href): bool
    {
        $href = trim((string) $href);

        if ($href === '' || $href === '#') {
            return false;
        }

        if (str_starts_with(strtolower($href), 'whatsapp://')) {
            return true;
        }

        $host = parse_url($href, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        $host = strtolower($host);

        return in_array($host, ['wa.me', 'api.whatsapp.com', 'web.whatsapp.com', 'chat.whatsapp.com'], true);
    }
    public static function resolveFooterLinkHref(?string $href, ?string $label = null, ?string $icon = null): string
    {
        $href = trim((string) $href);
        $label = trim((string) $label);
        $icon = strtolower(trim((string) $icon));

        if (str_starts_with(strtolower($href), 'mailto:')) {
            return $href;
        }

        if (str_starts_with(strtolower($href), 'tel:')) {
            return $href;
        }

        foreach ([$href, $label] as $candidate) {
            if ($candidate === '' || $candidate === '#') {
                continue;
            }

            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                return 'mailto:' . $candidate;
            }

            $phone = static::extractPhoneNumber($candidate);
            if ($phone !== null) {
                return 'tel:' . $phone;
            }
        }

        if (str_contains($href, '@') && ! str_starts_with($href, 'http') && ! str_starts_with($href, '/')) {
            $email = preg_replace('/^mailto:/i', '', $href);
            if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return 'mailto:' . $email;
            }
        }

        if ($icon !== '' && str_contains($icon, 'fa-phone')) {
            $phone = static::extractPhoneNumber($label) ?? static::extractPhoneNumber($href);
            if ($phone !== null) {
                return 'tel:' . $phone;
            }
        }

        return static::resolvePublicHref($href);
    }

    private static function extractPhoneNumber(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '#') {
            return null;
        }

        if (str_starts_with(strtolower($value), 'tel:')) {
            $value = substr($value, 4);
        }

        if (
            str_contains($value, '@')
            || preg_match('/^https?:\/\//i', $value)
            || str_starts_with($value, '/')
            || str_starts_with($value, '#')
        ) {
            return null;
        }

        if (! preg_match('/^[\d+\s().\-\/]+$/', $value)) {
            return null;
        }

        $digitsOnly = preg_replace('/\D/', '', $value);
        if (strlen($digitsOnly) < 8) {
            return null;
        }

        $normalized = preg_replace('/[^\d+]/', '', $value);
        if (str_contains($normalized, '+')) {
            $normalized = '+' . str_replace('+', '', $normalized);
        }

        return $normalized !== '' ? $normalized : null;
    }

    public static function isPublicPageActive(string $page): bool
    {
        return request()->routeIs('vitrine.' . $page)
            || request()->routeIs('vitrine.' . $page . '.*');
    }

    public static function getContent(string $key, ?array $default = null): ?array
    {
        $block = static::query()->where('key', $key)->where('is_active', true)->first();

        if (! $block) {
            return $default;
        }

        return static::resolveContentImages($block->content, $key);
    }

    private static function resolveContentImages(array $content, string $blockKey): array
    {
        if ($blockKey === 'footer') {
            if (! empty($content['logo_url'])) {
                $content['logo_url'] = static::resolveImageAbsoluteUrl($content['logo_url']);
            }

            if (isset($content['social_links']) && is_array($content['social_links'])) {
                $content['social_links'] = array_map(function (array $social) {
                    if (! empty($social['icon_url'])) {
                        $social['icon_url'] = static::resolveImageAbsoluteUrl($social['icon_url']);
                    }

                    return $social;
                }, $content['social_links']);
            }
        }

        if ($blockKey === 'header' && ! empty($content['logo_url'])) {
            $content['logo_url'] = static::resolveImageAbsoluteUrl($content['logo_url']);
        }

        if ($blockKey === 'hero' && isset($content['slides']) && is_array($content['slides'])) {
            $content['slides'] = array_map(function (array $slide) {
                if (isset($slide['image_url'])) {
                    $slide['image_url'] = static::resolveImageAbsoluteUrl($slide['image_url']);
                }

                return $slide;
            }, $content['slides']);
        }

        if ($blockKey === 'gallery' && isset($content['items']) && is_array($content['items'])) {
            $content['items'] = array_map(function (array $item) {
                if (isset($item['image_url'])) {
                    $item['image_url'] = static::resolveImageAbsoluteUrl($item['image_url']);
                }

                return $item;
            }, $content['items']);
        }

        if ($blockKey === 'partners' && isset($content['items']) && is_array($content['items'])) {
            $content['items'] = array_map(function (array $item) {
                if (isset($item['image_url'])) {
                    $item['image_url'] = static::resolveImageAbsoluteUrl($item['image_url']);
                }

                return $item;
            }, $content['items']);
        }

        if ($blockKey === 'temoignages' && isset($content['items']) && is_array($content['items'])) {
            $content['items'] = array_map(function (array $item) {
                if (isset($item['image_url'])) {
                    $item['image_url'] = static::resolveImageAbsoluteUrl($item['image_url']);
                }

                return $item;
            }, $content['items']);
        }

        if ($blockKey === 'vos-patients' && isset($content['videos']) && is_array($content['videos'])) {
            $content['videos'] = array_map(function (array $item) {
                if (! empty($item['video_url'])) {
                    $item['video_url'] = static::resolveImageAbsoluteUrl($item['video_url']);
                }

                return $item;
            }, $content['videos']);
        }

        if ($blockKey === 'services' && isset($content['items']) && is_array($content['items'])) {
            $content['items'] = array_map(function (array $item) {
                if (! empty($item['image_url'])) {
                    $item['image_url'] = static::resolveImageAbsoluteUrl($item['image_url']);
                }

                if (! empty($item['icon_url'])) {
                    $item['icon_url'] = static::resolveImageAbsoluteUrl($item['icon_url']);
                }

                return $item;
            }, $content['items']);
        }

        if ($blockKey === 'academy' && isset($content['documents']) && is_array($content['documents'])) {
            $content['documents'] = array_map(function (array $doc) {
                if (! empty($doc['file_url'])) {
                    $doc['file_url'] = static::resolveImageAbsoluteUrl($doc['file_url']);
                }

                if (! empty($doc['cover_image_url'])) {
                    $doc['cover_image_url'] = static::resolveImageAbsoluteUrl($doc['cover_image_url']);
                }

                if (! empty($doc['pdf_preview_url'])) {
                    $doc['pdf_preview_url'] = static::resolveImageAbsoluteUrl($doc['pdf_preview_url']);
                }

                return $doc;
            }, $content['documents']);
        }

        if ($blockKey === 'about') {
            if (isset($content['photos']) && is_array($content['photos'])) {
                $content['photos'] = array_map(function (array $photo) {
                    if (! empty($photo['image_url'])) {
                        $photo['image_url'] = static::resolveImageAbsoluteUrl($photo['image_url']);
                    }

                    return $photo;
                }, $content['photos']);
            }

            if (isset($content['videos']) && is_array($content['videos'])) {
                $content['videos'] = array_map(function (array $video) {
                    if (! empty($video['video_url'])) {
                        $video['video_url'] = static::resolveImageAbsoluteUrl($video['video_url']);
                    }

                    if (! empty($video['poster_url'])) {
                        $video['poster_url'] = static::resolveImageAbsoluteUrl($video['poster_url']);
                    }

                    return $video;
                }, $content['videos']);
            }
        }

        if ($blockKey === 'laboratory') {
            if (isset($content['media']) && is_array($content['media'])) {
                $content['media'] = array_map(function (array $item) {
                    $type = ($item['type'] ?? '') === 'video' ? 'video' : 'image';
                    $item['type'] = $type;
                    $item['category'] = static::normalizeLaboratoryCategory($item['category'] ?? null);

                    if (! empty($item['image_url'])) {
                        $item['image_url'] = static::resolveImageAbsoluteUrl($item['image_url']);
                    }

                    if (! empty($item['video_url'])) {
                        $item['video_url'] = static::resolveImageAbsoluteUrl($item['video_url']);
                    }

                    if (! empty($item['poster_url'])) {
                        $item['poster_url'] = static::resolveImageAbsoluteUrl($item['poster_url']);
                    }

                    return $item;
                }, $content['media']);
            }

            if (isset($content['photos']) && is_array($content['photos'])) {
                $content['photos'] = array_map(function (array $photo) {
                    if (! empty($photo['image_url'])) {
                        $photo['image_url'] = static::resolveImageAbsoluteUrl($photo['image_url']);
                    }

                    $photo['category'] = static::normalizeLaboratoryCategory($photo['category'] ?? null);

                    return $photo;
                }, $content['photos']);
            }

            if (isset($content['videos']) && is_array($content['videos'])) {
                $content['videos'] = array_map(function (array $video) {
                    if (! empty($video['video_url'])) {
                        $video['video_url'] = static::resolveImageAbsoluteUrl($video['video_url']);
                    }

                    if (! empty($video['poster_url'])) {
                        $video['poster_url'] = static::resolveImageAbsoluteUrl($video['poster_url']);
                    }

                    $video['category'] = static::normalizeLaboratoryCategory($video['category'] ?? null);

                    return $video;
                }, $content['videos']);
            }
        }

        return $content;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activeServiceItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function (array $item): bool {
            return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        })->values();
    }

    public static function isServiceItemActive(array $item): bool
    {
        return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Placement honeycomb : 1er service en haut au centre, dernier en bas au centre,
     * les intermédiaires en alternance gauche / droite.
     *
     * @return array{row: int, col: int, role: 'only'|'first'|'middle'|'last'}
     */
    public static function serviceHoneycombPlacement(int $index, int $total): array
    {
        if ($total <= 1) {
            return ['row' => 1, 'col' => 2, 'role' => 'only'];
        }

        if ($index === 0) {
            return ['row' => 1, 'col' => 2, 'role' => 'first'];
        }

        if ($index === $total - 1) {
            return [
                'row' => 2 + intdiv($total - 1, 2),
                'col' => 2,
                'role' => 'last',
            ];
        }

        $middleIndex = $index - 1;

        return [
            'row' => 2 + intdiv($middleIndex, 2),
            'col' => ($middleIndex % 2 === 0) ? 1 : 3,
            'role' => 'middle',
        ];
    }

    public static function serviceHoneycombRowCount(int $total): int
    {
        if ($total <= 1) {
            return 1;
        }

        return 2 + intdiv($total - 1, 2);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function serviceSections(array $item): \Illuminate\Support\Collection
    {
        return collect($item['sections'] ?? [])
            ->filter(function (array $section): bool {
                $hasPhotos = collect($section['photos'] ?? [])
                    ->contains(fn (array $photo) => filled($photo['image_url'] ?? null));

                return filled($section['title'] ?? null)
                    || filled($section['description'] ?? null)
                    || $hasPhotos;
            })
            ->values();
    }

    /**
     * @param  array<string, mixed>  $section
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function serviceSectionPhotos(array $section): \Illuminate\Support\Collection
    {
        return collect($section['photos'] ?? [])
            ->filter(fn (array $photo) => filled($photo['image_url'] ?? null))
            ->map(function (array $photo): array {
                $imageUrl = static::resolveImageAbsoluteUrl((string) ($photo['image_url'] ?? ''));

                return [
                    'image_url' => $imageUrl,
                    'title' => trim((string) ($photo['title'] ?? '')),
                ];
            })
            ->values();
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activeProcessSteps(array $steps): \Illuminate\Support\Collection
    {
        return collect($steps)->filter(function (array $step): bool {
            return filter_var($step['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        })->values();
    }

    public static function isProcessStepActive(array $step): bool
    {
        return filter_var($step['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activeFaqItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function (array $item): bool {
            return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        })->values();
    }

    public static function isFaqItemActive(array $item): bool
    {
        return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<int, string>
     */
    public static function recruitmentEmploymentTypes(): array
    {
        return ['CDI', 'CDD', 'SIVP', 'Karama'];
    }

    /**
     * @return array<int, string>
     */
    public static function recruitmentGenderOptions(): array
    {
        return ['Indifférent', 'Homme', 'Femme'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activeRecruitmentItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)
            ->filter(function (array $item): bool {
                return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN)
                    && ! filter_var($item['is_expired'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    && filled($item['title'] ?? null);
            })
            ->map(function (array $item): array {
                $item['vacancies'] = max(0, (int) ($item['vacancies'] ?? 0));
                $item['employment_types'] = collect($item['employment_types'] ?? [])
                    ->map(fn ($type) => trim((string) $type))
                    ->filter(fn ($type) => in_array($type, static::recruitmentEmploymentTypes(), true))
                    ->values()
                    ->all();
                $gender = trim((string) ($item['gender'] ?? 'Indifférent'));
                $item['gender'] = in_array($gender, static::recruitmentGenderOptions(), true)
                    ? $gender
                    : 'Indifférent';

                return $item;
            })
            ->values();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activePartnerItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function (array $item): bool {
            return static::isPartnerItemActive($item) && filled($item['image_url'] ?? null);
        })->values();
    }

    public static function isPartnerItemActive(array $item): bool
    {
        return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Témoignages actifs (avec au minimum un nom ou un commentaire).
     */
    public static function activeTemoignageItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function (array $item): bool {
            $isActive = filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

            return $isActive && (filled($item['name'] ?? null) || filled($item['comment'] ?? null));
        })->values();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activeGalleryItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function (array $item): bool {
            return static::isGalleryItemActive($item);
        })->values();
    }

    /**
     * Images actives marquées favorites (aperçu page d'accueil).
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function activeFavoriteGalleryItems(array $items): \Illuminate\Support\Collection
    {
        return collect($items)->filter(function (array $item): bool {
            return static::isGalleryItemActive($item) && static::isGalleryItemFavorite($item);
        })->values();
    }

    public static function isGalleryItemActive(array $item): bool
    {
        return filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    public static function isGalleryItemFavorite(array $item): bool
    {
        return filter_var($item['is_favorite'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Images affichées dans la galerie de la page d'accueil (actives + favorites uniquement).
     *
     * @param  array<string, mixed>  $gallery
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function homepageGalleryItems(array $gallery): \Illuminate\Support\Collection
    {
        return static::activeFavoriteGalleryItems($gallery['items'] ?? []);
    }

    /**
     * Images actives non favorites (affichées après « Voir la suite » sur l'accueil).
     *
     * @param  array<string, mixed>  $gallery
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function homepageGalleryMoreItems(array $gallery): \Illuminate\Support\Collection
    {
        return static::activeGalleryItems($gallery['items'] ?? [])
            ->reject(fn (array $item): bool => static::isGalleryItemFavorite($item))
            ->values();
    }

    /**
     * Images affichées sur la page Galerie dédiée (actives, favorite ou non).
     *
     * @param  array<string, mixed>  $gallery
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function pageGalleryItems(array $gallery): \Illuminate\Support\Collection
    {
        return static::activeGalleryItems($gallery['items'] ?? []);
    }

    /**
     * URL d'intégration Google Maps pour le bloc contact.
     *
     * @param  array<string, mixed>  $contact
     */
    public static function contactMapEmbedUrl(array $contact): string
    {
        $custom = trim((string) ($contact['map_embed_url'] ?? ''));
        if ($custom !== '' && str_contains($custom, 'google.com/maps')) {
            return $custom;
        }

        $address = trim((string) ($contact['map_address'] ?? ''));
        if ($address === '') {
            return '';
        }

        return 'https://www.google.com/maps?q=' . rawurlencode($address) . '&output=embed';
    }

    /**
     * Lien Google Maps (itinéraire / ouverture dans Maps).
     *
     * @param  array<string, mixed>  $contact
     */
    public static function contactMapLinkUrl(array $contact): string
    {
        $address = trim((string) ($contact['map_address'] ?? ''));
        if ($address === '') {
            return '';
        }

        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
    }

    public static function isContactMapActive(array $contact): bool
    {
        if (! filter_var($contact['map_is_active'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        return static::contactMapEmbedUrl($contact) !== '';
    }

    /**
     * URL d'affichage pour l'image d'un service vitrine (hexagone ou détail).
     *
     * @param  array<string, mixed>  $item
     */
    public static function serviceItemImageUrl(array $item): string
    {
        $imageUrl = trim((string) ($item['image_url'] ?? ''));
        if ($imageUrl === '') {
            $imageUrl = trim((string) ($item['icon_url'] ?? ''));
        }

        return static::resolveImageAbsoluteUrl($imageUrl);
    }

    /**
     * @return array<string, array{label: string, icon: string}>
     */
    public static function defaultAcademyCategories(): array
    {
        return [
            'catalogue' => ['label' => 'Catalogues', 'icon' => 'fas fa-book-open'],
            'guide' => ['label' => 'Guides techniques', 'icon' => 'fas fa-drafting-compass'],
            'protocole' => ['label' => 'Protocoles', 'icon' => 'fas fa-clipboard-list'],
            'notice' => ['label' => 'Notices', 'icon' => 'fas fa-file-alt'],
        ];
    }

    /**
     * @param  array<string, mixed>  $academyContent
     * @return array<string, array{label: string, icon: string}>
     */
    public static function resolveAcademyCategories(array $academyContent): array
    {
        $stored = $academyContent['categories'] ?? [];
        $resolved = [];

        if (! is_array($stored)) {
            return static::defaultAcademyCategories();
        }

        foreach ($stored as $category) {
            if (! is_array($category)) {
                continue;
            }

            $label = trim((string) ($category['label'] ?? ''));
            $icon = trim((string) ($category['icon'] ?? ''));
            $key = Str::slug(trim((string) ($category['key'] ?? '')));

            if ($key === '' && $label !== '') {
                $key = Str::slug($label);
            }

            if ($key === '' || $label === '') {
                continue;
            }

            $resolved[$key] = [
                'label' => $label,
                'icon' => static::normalizeAcademyCategoryIcon($icon),
            ];
        }

        return $resolved !== [] ? $resolved : static::defaultAcademyCategories();
    }

    /**
     * Retourne une icône de catégorie utilisable, ou une chaîne vide.
     */
    public static function normalizeAcademyCategoryIcon(?string $icon): string
    {
        $icon = trim((string) $icon);

        if ($icon === '') {
            return '';
        }

        // Placeholder injecté par l'ancienne version de l'admin.
        if (in_array(strtolower($icon), [
            'fas fa-file-pdf',
            'fa-solid fa-file-pdf',
            'fa fa-file-pdf',
        ], true)) {
            return '';
        }

        return $icon;
    }

    /**
     * @param  array<string, mixed>  $academyContent
     * @return list<array{key: string, label: string, icon: string}>
     */
    public static function academyCategoriesList(array $academyContent): array
    {
        return collect(static::resolveAcademyCategories($academyContent))
            ->map(fn (array $meta, string $key) => [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, array{label: string, icon: string}>
     */
    public static function defaultVosPatientsCategories(): array
    {
        return [
            'esthetique' => ['label' => 'Esthétique', 'icon' => 'fas fa-smile'],
            'implantologie' => ['label' => 'Implantologie', 'icon' => 'fas fa-tooth'],
            'prothese' => ['label' => 'Prothèse', 'icon' => 'fas fa-teeth'],
        ];
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, array{label: string, icon: string}>
     */
    public static function resolveVosPatientsCategories(array $content): array
    {
        $stored = $content['categories'] ?? [];
        $resolved = [];

        if (! is_array($stored)) {
            return static::defaultVosPatientsCategories();
        }

        foreach ($stored as $category) {
            if (! is_array($category)) {
                continue;
            }

            $label = trim((string) ($category['label'] ?? ''));
            $icon = trim((string) ($category['icon'] ?? ''));
            $key = Str::slug(trim((string) ($category['key'] ?? '')));

            if ($key === '' && $label !== '') {
                $key = Str::slug($label);
            }

            if ($key === '' || $label === '') {
                continue;
            }

            $resolved[$key] = [
                'label' => $label,
                'icon' => static::normalizeAcademyCategoryIcon($icon),
            ];
        }

        return $resolved !== [] ? $resolved : static::defaultVosPatientsCategories();
    }

    /**
     * @param  array<string, mixed>  $content
     * @return list<array{key: string, label: string, icon: string}>
     */
    public static function vosPatientsCategoriesList(array $content): array
    {
        return collect(static::resolveVosPatientsCategories($content))
            ->map(fn (array $meta, string $key) => [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
            ])
            ->values()
            ->all();
    }

    /**
     * @param  list<array{key: string, label: string, icon: string}>  $categories
     */
    public static function resolveVosPatientsVideoCategory(?string $category, array $categories): string
    {
        return static::resolveAcademyDocumentCategory($category, $categories);
    }

    /**
     * Vidéos actives avec une URL renseignée.
     */
    public static function activeVosPatientsVideos(array $videos): \Illuminate\Support\Collection
    {
        return collect($videos)->filter(function (array $item): bool {
            $isActive = filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

            return $isActive && filled($item['video_url'] ?? null);
        })->values();
    }

    /**
     * @param  list<array{key: string, label: string, icon: string}>  $categories
     */
    public static function resolveAcademyDocumentCategory(?string $category, array $categories): string
    {
        $category = trim((string) $category);
        $default = $categories[0]['key'] ?? 'catalogue';

        if ($category === '') {
            return $default;
        }

        foreach ($categories as $item) {
            $key = trim((string) ($item['key'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));

            if ($key === '' && $label !== '') {
                $key = Str::slug($label);
            }

            if ($key === '') {
                continue;
            }

            if ($category === $key || Str::slug($category) === $key) {
                return $key;
            }

            if ($label !== '' && ($category === $label || Str::slug($category) === Str::slug($label))) {
                return $key;
            }
        }

        return $default;
    }

    /**
     * @return array<string, array{label: string, icon: string, action: string}>
     */
    public static function academyFileTypes(): array
    {
        return [
            'pdf' => ['label' => 'PDF', 'icon' => 'fas fa-file-pdf', 'action' => 'Télécharger'],
            'image' => ['label' => 'Image', 'icon' => 'fas fa-file-image', 'action' => 'Voir'],
            'video' => ['label' => 'Vidéo', 'icon' => 'fas fa-file-video', 'action' => 'Regarder'],
            'word' => ['label' => 'Word', 'icon' => 'fas fa-file-word', 'action' => 'Télécharger'],
        ];
    }

    public static function normalizeAcademyFileType(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        return array_key_exists($type, static::academyFileTypes()) ? $type : 'pdf';
    }

    /**
     * @param  array<string, mixed>  $doc
     * @return array{label: string, icon: string, action: string}
     */
    public static function academyFileTypeMeta(array $doc): array
    {
        $type = static::normalizeAcademyFileType($doc['file_type'] ?? 'pdf');

        return static::academyFileTypes()[$type];
    }

    /**
     * @return array{mode: string, src: string}|null
     */
    public static function academyVideoPlayerConfig(?string $url): ?array
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $youtubeVideoId = static::extractYouTubeVideoId($url);
        if ($youtubeVideoId !== null) {
            return [
                'mode' => 'iframe',
                'src' => 'https://www.youtube.com/embed/' . $youtubeVideoId . '?autoplay=1&rel=0',
            ];
        }

        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/i', $url, $matches)) {
            return [
                'mode' => 'iframe',
                'src' => 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1',
            ];
        }

        return [
            'mode' => 'video',
            'src' => static::resolveImageAbsoluteUrl($url),
        ];
    }

    public static function extractYouTubeVideoId(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{11})/i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function youtubeThumbnailUrl(?string $url, string $quality = 'hqdefault'): string
    {
        $videoId = static::extractYouTubeVideoId($url);

        if ($videoId === null) {
            return '';
        }

        return 'https://img.youtube.com/vi/' . $videoId . '/' . $quality . '.jpg';
    }

    /**
     * @param  array<string, mixed>  $video
     */
    public static function aboutVideoPosterUrl(array $video): string
    {
        $posterUrl = trim((string) ($video['poster_url'] ?? ''));
        if ($posterUrl !== '') {
            return static::resolveImageAbsoluteUrl($posterUrl);
        }

        return static::youtubeThumbnailUrl((string) ($video['video_url'] ?? ''));
    }

    /**
     * Image de fond d'une carte Academy : couverture, aperçu PDF ou fichier image.
     *
     * @param  array<string, mixed>  $doc
     */
    public static function academyDocumentBackgroundUrl(array $doc): string
    {
        $coverUrl = trim((string) ($doc['cover_image_url'] ?? ''));
        if ($coverUrl !== '') {
            return static::resolveImageAbsoluteUrl($coverUrl);
        }

        $fileType = static::normalizeAcademyFileType($doc['file_type'] ?? 'pdf');

        if ($fileType === 'image') {
            $fileUrl = trim((string) ($doc['file_url'] ?? ''));
            if ($fileUrl !== '') {
                return static::resolveImageAbsoluteUrl($fileUrl);
            }
        }

        $previewUrl = trim((string) ($doc['pdf_preview_url'] ?? ''));
        if ($previewUrl !== '') {
            return static::resolveImageAbsoluteUrl($previewUrl);
        }

        return '';
    }

    public static function aboutSections(array $about): \Illuminate\Support\Collection
    {
        return collect($about['sections'] ?? [])
            ->map(fn (array $section): array => [
                'title' => trim((string) ($section['title'] ?? '')),
                'description' => trim((string) ($section['description'] ?? '')),
            ])
            ->filter(fn (array $section): bool => filled($section['title']) || filled($section['description']))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $about
     * @return array{title: string, description: string}|null
     */
    public static function aboutMissionSection(array $about): ?array
    {
        $sections = static::aboutSections($about);
        $mission = $sections->first(function (array $section): bool {
            return str_contains(mb_strtolower($section['title']), 'mission');
        }) ?? $sections->get(0);

        return is_array($mission) ? $mission : null;
    }

    /**
     * @param  array<string, mixed>  $about
     * @return array{title: string, description: string}|null
     */
    public static function aboutPrinciplesSection(array $about): ?array
    {
        $sections = static::aboutSections($about);
        $principles = $sections->first(function (array $section): bool {
            $title = mb_strtolower($section['title']);

            return str_contains($title, 'principe') || str_contains($title, 'valeur');
        }) ?? $sections->get(1);

        return is_array($principles) ? $principles : null;
    }

    /**
     * @return array<string, string>
     */
    public static function aboutInfoPageDefinitions(): array
    {
        return [
            'conditions-de-service' => 'Conditions de service',
            'conditions-de-paiement' => 'Conditions de paiement',
            'garantie' => 'Garantie',
            'delais-de-fabrication' => 'Délais de fabrication',
            'processus-de-qualite' => 'Processus de qualité',
        ];
    }

    /**
     * @param  array<string, mixed>  $about
     * @return array{title: string, content_html: string}|null
     */
    public static function aboutInfoPage(array $about, string $slug): ?array
    {
        $definitions = static::aboutInfoPageDefinitions();
        if (! array_key_exists($slug, $definitions)) {
            return null;
        }

        $page = $about['info_pages'][$slug] ?? [];
        $title = trim((string) ($page['title'] ?? $definitions[$slug]));
        $contentHtml = trim((string) ($page['content_html'] ?? ''));

        return [
            'title' => $title !== '' ? $title : $definitions[$slug],
            'content_html' => $contentHtml,
            'hero_kicker' => trim((string) ($page['hero_kicker'] ?? '')),
            'hero_heading' => trim((string) ($page['hero_heading'] ?? '')),
            'hero_lead' => trim((string) ($page['hero_lead'] ?? '')),
        ];
    }

    public static function isAboutInfoPage(string $slug): bool
    {
        return array_key_exists($slug, static::aboutInfoPageDefinitions());
    }

    public static function aboutMediaPageSlug(): string
    {
        return 'certifications';
    }

    public static function aboutMediaPageLabel(): string
    {
        return 'Certifications';
    }

    public static function isAboutMediaPage(string $slug): bool
    {
        return $slug === static::aboutMediaPageSlug();
    }

    public static function aboutLaboratoryPageSlug(): string
    {
        return 'galerie-equipe-equipement';
    }

    public static function aboutLaboratoryPageLabel(): string
    {
        return 'Galerie équipe / équipement';
    }

    public static function isAboutLaboratoryPage(string $slug): bool
    {
        return $slug === static::aboutLaboratoryPageSlug()
            || $slug === 'gallery'; // ancien slug (redirections)
    }

    public static function aboutProcessPageSlug(): string
    {
        return 'process-de-travail';
    }

    public static function aboutProcessPageLabel(): string
    {
        return 'Process de travail';
    }

    public static function isAboutProcessPage(string $slug): bool
    {
        return $slug === static::aboutProcessPageSlug();
    }

    /**
     * @param  array<string, mixed>  $about
     * @return array{section_label: string, title: string, description: string, photos: array<int, array<string, mixed>>}
     */
    public static function aboutMediaPage(array $about): array
    {
        $page = is_array($about['media_page'] ?? null) ? $about['media_page'] : [];

        return [
            'section_label' => trim((string) ($page['section_label'] ?? static::aboutMediaPageLabel())),
            'title' => trim((string) ($page['title'] ?? static::aboutMediaPageLabel())),
            'description' => trim((string) ($page['description'] ?? '')),
            'photos' => is_array($page['photos'] ?? null) ? $page['photos'] : [],
        ];
    }

    /**
     * @param  array<string, mixed>  $about
     * @return \Illuminate\Support\Collection<int, array{title: string, description: string, image_url: string}>
     */
    public static function aboutMediaPagePhotos(array $about): \Illuminate\Support\Collection
    {
        $page = static::aboutMediaPage($about);

        return collect($page['photos'])
            ->map(fn (array $photo): array => [
                'title' => trim((string) ($photo['title'] ?? '')),
                'description' => trim((string) ($photo['description'] ?? '')),
                'image_url' => trim((string) ($photo['image_url'] ?? '')),
            ])
            ->filter(fn (array $photo): bool => filled($photo['image_url']))
            ->values();
    }

    /**
     * @return array<string, array{label: string, route: string}>
     */
    public static function aboutOverviewTabs(): array
    {
        return [
            'qui-sommes-nous' => ['label' => 'Qui sommes-nous'],
            'notre-mission' => ['label' => 'Notre mission'],
            'nos-principe' => ['label' => 'Nos principe'],
        ];
    }

    public static function aboutOverviewPageSlug(): string
    {
        return 'a-propos';
    }

    public static function aboutOverviewPageLabel(): string
    {
        return 'À propos';
    }

    public static function isAboutOverviewPage(string $slug): bool
    {
        return $slug === static::aboutOverviewPageSlug();
    }

    /**
     * @return array<string, array{label: string}>
     */
    public static function aboutWorkTabs(): array
    {
        return [
            'processus-de-qualite' => ['label' => 'Processus de qualité'],
            static::aboutMediaPageSlug() => ['label' => static::aboutMediaPageLabel()],
            'garantie' => ['label' => 'Garantie'],
        ];
    }

    public static function aboutWorkPageSlug(): string
    {
        return 'qualite-certifications';
    }

    public static function aboutWorkPageLabel(): string
    {
        return 'Qualité & Certifications';
    }

    public static function isAboutWorkPage(string $slug): bool
    {
        return $slug === static::aboutWorkPageSlug();
    }

    /**
     * @return array<string, array{label: string}>
     */
    public static function aboutCollaborationTabs(): array
    {
        return [
            'services' => ['label' => 'Services'],
            'conditions-de-service' => ['label' => 'Conditions de service'],
            'conditions-de-paiement' => ['label' => 'Conditions de paiement'],
            'delais-de-fabrication' => ['label' => 'Délais de fabrication'],
            static::aboutProcessPageSlug() => ['label' => static::aboutProcessPageLabel()],
        ];
    }

    public static function aboutCollaborationPageSlug(): string
    {
        return 'travailler-avec-ldm';
    }

    public static function aboutCollaborationPageLabel(): string
    {
        return 'Travailler avec LDM';
    }

    public static function isAboutCollaborationPage(string $slug): bool
    {
        return $slug === static::aboutCollaborationPageSlug();
    }

    /**
     * Toutes les entrées configurables des sous-menus Le Laboratoire.
     *
     * @return array<string, bool>
     */
    public static function aboutMenuVisibilityDefaults(): array
    {
        $keys = [
            static::aboutOverviewPageSlug(),
            static::aboutCollaborationPageSlug(),
            static::aboutWorkPageSlug(),
            static::aboutLaboratoryPageSlug(),
            ...array_keys(static::aboutOverviewTabs()),
            ...array_keys(static::aboutCollaborationTabs()),
            ...array_keys(static::aboutWorkTabs()),
        ];

        return array_fill_keys(array_unique($keys), true);
    }

    public static function isAboutMenuItemVisible(array $about, string $slug): bool
    {
        $visibility = is_array($about['menu_visibility'] ?? null)
            ? $about['menu_visibility']
            : [];

        return filter_var($visibility[$slug] ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<string, array{label: string, route: string}>
     */
    public static function aboutSubPages(): array
    {
        $pages = [
            'qui-sommes-nous' => [
                'label' => 'Qui sommes-nous',
                'route' => 'vitrine.about.show',
            ],
            'notre-mission' => [
                'label' => 'Notre mission',
                'route' => 'vitrine.about.show',
            ],
            'nos-principe' => [
                'label' => 'Nos principe',
                'route' => 'vitrine.about.show',
            ],
            static::aboutLaboratoryPageSlug() => [
                'label' => static::aboutLaboratoryPageLabel(),
                'route' => 'vitrine.about.show',
            ],
        ];

        foreach (static::aboutInfoPageDefinitions() as $slug => $label) {
            $pages[$slug] = [
                'label' => $label,
                'route' => 'vitrine.about.show',
            ];
        }

        $pages[static::aboutProcessPageSlug()] = [
            'label' => static::aboutProcessPageLabel(),
            'route' => 'vitrine.about.show',
        ];

        $pages[static::aboutMediaPageSlug()] = [
            'label' => static::aboutMediaPageLabel(),
            'route' => 'vitrine.about.show',
        ];

        return $pages;
    }

    /**
     * Retourne les sous-pages du Laboratoire selon l'ordre configuré dans l'admin.
     *
     * @param  array<string, mixed>  $about
     * @return array<string, array{label: string, route: string}>
     */
    public static function orderedAboutSubPages(array $about = []): array
    {
        $pages = static::aboutSubPages();
        $configuredOrder = is_array($about['subpage_order'] ?? null)
            ? $about['subpage_order']
            : [];
        $ordered = [];

        foreach ($configuredOrder as $slug) {
            $slug = trim((string) $slug);

            if (array_key_exists($slug, $pages) && ! array_key_exists($slug, $ordered)) {
                $ordered[$slug] = $pages[$slug];
            }
        }

        foreach ($pages as $slug => $page) {
            if (! array_key_exists($slug, $ordered)) {
                $ordered[$slug] = $page;
            }
        }

        return $ordered;
    }

    /**
     * Ordre du menu public, avec Services injecté dans son groupe virtuel.
     *
     * @param  array<string, mixed>  $about
     * @return array<string, array{label: string, route: string}>
     */
    public static function orderedAboutMenuSubPages(array $about = []): array
    {
        $pages = static::orderedAboutSubPages($about);
        $ordered = [];
        $servicesAdded = false;
        $collaborationKeys = array_keys(static::aboutCollaborationTabs());

        foreach ($pages as $slug => $page) {
            if (! $servicesAdded && in_array($slug, $collaborationKeys, true)) {
                $ordered['services'] = [
                    'label' => 'Services',
                    'route' => 'vitrine.about.show',
                ];
                $servicesAdded = true;
            }

            $ordered[$slug] = $page;
        }

        if (! $servicesAdded) {
            $ordered['services'] = [
                'label' => 'Services',
                'route' => 'vitrine.about.show',
            ];
        }

        return $ordered;
    }

    /**
     * @param  array<string, mixed>  $about
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function aboutPhotos(array $about): \Illuminate\Support\Collection
    {
        return collect($about['photos'] ?? [])
            ->filter(fn (array $photo): bool => filled($photo['image_url'] ?? null))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $about
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function aboutVideos(array $about): \Illuminate\Support\Collection
    {
        return collect($about['videos'] ?? [])
            ->filter(fn (array $video): bool => filled($video['video_url'] ?? null))
            ->values();
    }

    /**
     * @return array<string, array{label: string, icon: string}>
     */
    public static function laboratoryCategories(): array
    {
        return [
            'equipe' => ['label' => 'Équipe', 'icon' => 'fas fa-users'],
            'laboratoire' => ['label' => 'Laboratoire', 'icon' => 'fas fa-building'],
            'machines' => ['label' => 'Machines et équipements', 'icon' => 'fas fa-cogs'],
        ];
    }

    public static function normalizeLaboratoryCategory(?string $category): string
    {
        $category = strtolower(trim((string) $category));

        return array_key_exists($category, static::laboratoryCategories()) ? $category : 'equipe';
    }

    /**
     * @param  array<string, mixed>  $laboratory
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function laboratoryMediaItems(array $laboratory): \Illuminate\Support\Collection
    {
        $media = $laboratory['media'] ?? null;

        if (! is_array($media) || $media === []) {
            $media = [];

            foreach ($laboratory['photos'] ?? [] as $photo) {
                if (! filled($photo['image_url'] ?? null)) {
                    continue;
                }

                $media[] = array_merge($photo, ['type' => 'image']);
            }

            foreach ($laboratory['videos'] ?? [] as $video) {
                if (! filled($video['video_url'] ?? null)) {
                    continue;
                }

                $media[] = array_merge($video, ['type' => 'video']);
            }
        }

        return collect($media)
            ->map(function (array $item): ?array {
                $type = ($item['type'] ?? '') === 'video' ? 'video' : 'image';
                $item['type'] = $type;
                $item['category'] = static::normalizeLaboratoryCategory($item['category'] ?? null);

                if ($type === 'image' && ! filled($item['image_url'] ?? null)) {
                    return null;
                }

                if ($type === 'video' && ! filled($item['video_url'] ?? null)) {
                    return null;
                }

                return $item;
            })
            ->filter()
            ->values();
    }

    /**
     * @param  array<string, mixed>  $laboratory
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function laboratoryPhotos(array $laboratory): \Illuminate\Support\Collection
    {
        return static::laboratoryMediaItems($laboratory)
            ->filter(fn (array $item): bool => ($item['type'] ?? '') === 'image')
            ->values();
    }

    /**
     * @param  array<string, mixed>  $laboratory
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function laboratoryVideos(array $laboratory): \Illuminate\Support\Collection
    {
        return static::laboratoryMediaItems($laboratory)
            ->filter(fn (array $item): bool => ($item['type'] ?? '') === 'video')
            ->values();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public static function serviceItemSlug(array $item): string
    {
        $slug = trim((string) ($item['slug'] ?? ''));
        if ($slug !== '') {
            return \Illuminate\Support\Str::slug($slug);
        }

        return \Illuminate\Support\Str::slug((string) ($item['title'] ?? 'service'));
    }
}
