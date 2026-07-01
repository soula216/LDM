@props(['c', 'color' => 'primary'])

<div class="p-4 sm:p-5 rounded-xl bg-neutral-50/80 border border-border/60">
    <p class="text-xs font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-{{ $color === 'primary' ? 'primary' : $color }}"></span>
        En-tête de section
    </p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @include('admin.vitrine.partials.field', ['label' => 'Label', 'name' => 'content[section_label]', 'value' => $c['section_label'] ?? '', 'placeholder' => 'Nos Services'])
        @include('admin.vitrine.partials.field', ['label' => 'Titre', 'name' => 'content[section_title]', 'value' => $c['section_title'] ?? '', 'placeholder' => 'Solutions Complètes'])
        @include('admin.vitrine.partials.field', ['label' => 'Sous-titre', 'name' => 'content[section_subtitle]', 'value' => $c['section_subtitle'] ?? '', 'placeholder' => 'Courte description…'])
    </div>
</div>
