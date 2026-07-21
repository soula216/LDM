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
        $content['principles_kicker'] = $content['principles_kicker'] ?? 'Nos valeurs';
        $content['principles_heading'] = $content['principles_heading'] ?? 'Les principes qui guident notre exigence';
        $content['principles_lead'] = $content['principles_lead']
            ?? 'Précision, qualité, réactivité, innovation et confiance structurent chacune de nos collaborations.';

        $about->update(['content' => $content]);
    }

    public function down(): void
    {
        $about = VitrineBlock::query()->where('key', 'about')->first();

        if (! $about) {
            return;
        }

        $content = $about->content ?? [];
        unset(
            $content['principles_kicker'],
            $content['principles_heading'],
            $content['principles_lead']
        );

        $about->update(['content' => $content]);
    }
};
