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
                if (! empty($item['icon_url'])) {
                    $item['icon_url'] = static::resolveImageAbsoluteUrl($item['icon_url']);
                }

                return $item;
            }, $content['items']);
        }

        return $content;
    }
}
