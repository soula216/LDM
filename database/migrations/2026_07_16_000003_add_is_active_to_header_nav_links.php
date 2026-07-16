<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];

        if (! array_key_exists('client_space_is_active', $content)) {
            $content['client_space_is_active'] = true;
        }

        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->map(function ($link) {
                if (! is_array($link)) {
                    return $link;
                }

                if (! array_key_exists('is_active', $link)) {
                    $link['is_active'] = true;
                } else {
                    $link['is_active'] = filter_var($link['is_active'], FILTER_VALIDATE_BOOLEAN);
                }

                return $link;
            })
            ->values()
            ->all();

        $header->update(['content' => $content]);
    }

    public function down(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        unset($content['client_space_is_active']);

        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->map(function ($link) {
                if (! is_array($link)) {
                    return $link;
                }

                unset($link['is_active']);

                return $link;
            })
            ->values()
            ->all();

        $header->update(['content' => $content]);
    }
};
