<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $about = VitrineBlock::query()->where('key', 'about')->first();

        if (! $about) {
            return;
        }

        $content = $about->content ?? [];
        $existing = is_array($content['menu_visibility'] ?? null)
            ? $content['menu_visibility']
            : [];
        $content['menu_visibility'] = array_replace(
            VitrineBlock::aboutMenuVisibilityDefaults(),
            $existing
        );

        $about->update(['content' => $content]);
    }

    public function down(): void
    {
        $about = VitrineBlock::query()->where('key', 'about')->first();

        if (! $about) {
            return;
        }

        $content = $about->content ?? [];
        unset($content['menu_visibility']);
        $about->update(['content' => $content]);
    }
};
