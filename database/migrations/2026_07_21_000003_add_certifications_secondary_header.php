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
        $content['certifications_kicker'] = $content['certifications_kicker'] ?? 'Qualité certifiée';
        $content['certifications_heading'] = $content['certifications_heading']
            ?? 'Des standards reconnus, une qualité maîtrisée';
        $content['certifications_lead'] = $content['certifications_lead']
            ?? 'Nos certifications témoignent de notre engagement pour des processus fiables, des matériaux conformes et une traçabilité rigoureuse.';

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
            $content['certifications_kicker'],
            $content['certifications_heading'],
            $content['certifications_lead']
        );

        $about->update(['content' => $content]);
    }
};
