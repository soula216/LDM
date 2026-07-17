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

        if (! is_array($content['subpage_order'] ?? null)) {
            $content['subpage_order'] = array_keys(VitrineBlock::aboutSubPages());
            $about->update(['content' => $content]);
        }
    }

    public function down(): void
    {
        $about = VitrineBlock::query()->where('key', 'about')->first();

        if (! $about) {
            return;
        }

        $content = $about->content ?? [];
        unset($content['subpage_order']);
        $about->update(['content' => $content]);
    }
};
