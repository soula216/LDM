<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

        $pageRoutes = [
            'academy' => 'vitrine.academy',
            'services' => 'vitrine.services',
            'process' => 'vitrine.process',
            'gallery' => 'vitrine.gallery',
            'faq' => 'vitrine.faq',
        ];

        foreach ($pageRoutes as $slug => $routeName) {
            if (
                in_array($normalized, ["#{$slug}", "/{$slug}", $slug], true)
                || str_ends_with($normalized, "/{$slug}")
            ) {
                return route($routeName);
            }
        }

        if ($normalized === '#accueil') {
            return $homeUrl;
        }

        if (str_starts_with($href, '#')) {
            return $homeUrl . $href;
        }

        return $href;
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
     * Image de fond d'une carte Academy : couverture personnalisée ou 1re page du PDF.
     *
     * @param  array<string, mixed>  $doc
     */
    public static function academyDocumentBackgroundUrl(array $doc): string
    {
        $coverUrl = trim((string) ($doc['cover_image_url'] ?? ''));
        if ($coverUrl !== '') {
            return static::resolveImageAbsoluteUrl($coverUrl);
        }

        $previewUrl = trim((string) ($doc['pdf_preview_url'] ?? ''));
        if ($previewUrl !== '') {
            return static::resolveImageAbsoluteUrl($previewUrl);
        }

        return '';
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
