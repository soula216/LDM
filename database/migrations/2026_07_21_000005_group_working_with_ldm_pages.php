<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $header = VitrineBlock::query()->where('key', 'header')->first();

        if ($header) {
            $content = $header->content ?? [];
            $links = is_array($content['nav_links'] ?? null) ? $content['nav_links'] : [];
            $content['nav_links'] = array_values(array_filter(
                $links,
                fn (mixed $link): bool => ! is_array($link)
                    || ! in_array(trim((string) ($link['href'] ?? '')), ['/services', 'services', '#services'], true)
            ));
            $header->update(['content' => $content]);
        }

    }

    public function down(): void
    {
        // Les liens et leur ordre peuvent être personnalisés dans l’admin :
        // ne pas réintroduire automatiquement un élément potentiellement supprimé.
    }
};
