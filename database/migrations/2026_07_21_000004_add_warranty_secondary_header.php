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
        $infoPages = is_array($content['info_pages'] ?? null) ? $content['info_pages'] : [];
        $warranty = is_array($infoPages['garantie'] ?? null) ? $infoPages['garantie'] : [];

        $warranty['hero_kicker'] = $warranty['hero_kicker'] ?? 'Votre sérénité';
        $warranty['hero_heading'] = $warranty['hero_heading']
            ?? 'Une garantie fondée sur la qualité et la confiance';
        $warranty['hero_lead'] = $warranty['hero_lead']
            ?? 'Nous nous engageons durablement sur la qualité de nos réalisations afin de sécuriser chaque collaboration avec votre cabinet.';

        $infoPages['garantie'] = $warranty;
        $content['info_pages'] = $infoPages;

        $about->update(['content' => $content]);
    }

    public function down(): void
    {
        $about = VitrineBlock::query()->where('key', 'about')->first();

        if (! $about) {
            return;
        }

        $content = $about->content ?? [];
        $infoPages = is_array($content['info_pages'] ?? null) ? $content['info_pages'] : [];
        $warranty = is_array($infoPages['garantie'] ?? null) ? $infoPages['garantie'] : [];

        unset($warranty['hero_kicker'], $warranty['hero_heading'], $warranty['hero_lead']);

        $infoPages['garantie'] = $warranty;
        $content['info_pages'] = $infoPages;

        $about->update(['content' => $content]);
    }
};
