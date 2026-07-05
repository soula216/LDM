<?php

use App\Models\VitrineBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private function defaultFaqContent(): array
    {
        return [
            'section_label' => 'FAQ',
            'section_title' => 'Foire Aux Questions',
            'section_subtitle' => 'Retrouvez les réponses aux questions les plus fréquentes sur nos services, délais et modalités de collaboration.',
            'items' => [
                [
                    'question' => 'Comment envoyer un fichier STL ?',
                    'answer' => 'Vous pouvez nous transmettre vos fichiers STL via votre espace client sécurisé, par e-mail à notre adresse dédiée aux fichiers numériques, ou sur support USB lors de la remise des empreintes. Vérifiez que le fichier est correctement exporté (maillage fermé, sans erreurs) et indiquez la référence du dossier patient dans votre message.',
                ],
                [
                    'question' => 'Quels sont vos délais de fabrication ?',
                    'answer' => 'Nos délais standards varient de 5 à 10 jours ouvrés selon la complexité du cas et le type de prothèse. Pour les cas urgents, nous proposons des options express sous réserve de faisabilité technique. Un délai précis vous est confirmé à la validation de chaque commande.',
                ],
                [
                    'question' => 'Quels matériaux utilisez-vous ?',
                    'answer' => 'Nous travaillons avec des matériaux certifiés biocompatibles : zircone multilayer, céramique stratifiée, titane, chrome-cobalt, résines haut de gamme et polymères pour prothèses amovibles. Chaque matériau est sélectionné en fonction du cas clinique, de l\'esthétique attendue et des contraintes fonctionnelles.',
                ],
                [
                    'question' => 'Réalisez-vous des restaurations sur implants ?',
                    'answer' => 'Oui, nous réalisons l\'ensemble des solutions implantaires : couronnes unitaires, bridges, prothèses amovibles sur barre ou attachments, ainsi que les structures en titane ou zircone. Nous travaillons à partir de vos scans, fichiers STL ou empreintes conventionnelles.',
                ],
                [
                    'question' => 'Traitez-vous les cas urgents ?',
                    'answer' => 'Oui, nous traitons les cas urgents en priorité lorsque la situation clinique l\'exige. Contactez-nous par téléphone dès que possible pour évaluer la faisabilité et organiser une fabrication accélérée, sous réserve de disponibilité des équipes et des matériaux.',
                ],
                [
                    'question' => 'Livrez-vous sur tout le territoire tunisien ?',
                    'answer' => 'Oui, nous assurons la livraison sur l\'ensemble du territoire tunisien via notre réseau de transporteurs partenaires ou par coursier dédié selon votre localisation. Les modalités et délais de livraison vous sont communiqués à la confirmation de commande.',
                ],
                [
                    'question' => 'Comment demander le catalogue et la liste des prix ?',
                    'answer' => 'Pour recevoir notre catalogue et la liste des tarifs, remplissez le formulaire de contact en précisant votre demande, ou écrivez-nous directement par e-mail. Vous pouvez également consulter la rubrique LDM Academy pour télécharger nos documents techniques. Notre équipe commerciale vous répond sous 24 h ouvrées.',
                ],
            ],
        ];
    }

    public function up(): void
    {
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 9]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 10]);

        VitrineBlock::updateOrCreate(
            ['key' => 'faq'],
            [
                'label' => 'FAQ',
                'sort_order' => 7,
                'is_active' => true,
                'content' => $this->defaultFaqContent(),
            ]
        );

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $navLinks = $content['nav_links'] ?? [];
        $hasFaqLink = collect($navLinks)->contains(fn ($link) => ($link['href'] ?? '') === '#faq');

        if (! $hasFaqLink) {
            $contactIndex = collect($navLinks)->search(fn ($link) => ($link['href'] ?? '') === '#contact');
            $faqLink = ['label' => 'FAQ', 'href' => '/faq'];

            if ($contactIndex !== false) {
                array_splice($navLinks, (int) $contactIndex, 0, [$faqLink]);
            } else {
                $navLinks[] = $faqLink;
            }

            $content['nav_links'] = array_values($navLinks);
            $header->update(['content' => $content]);
        }
    }

    public function down(): void
    {
        VitrineBlock::query()->where('key', 'faq')->delete();
        VitrineBlock::query()->where('key', 'academy')->update(['sort_order' => 7]);
        VitrineBlock::query()->where('key', 'contact')->update(['sort_order' => 8]);
        VitrineBlock::query()->where('key', 'footer')->update(['sort_order' => 9]);

        $header = VitrineBlock::query()->where('key', 'header')->first();
        if (! $header) {
            return;
        }

        $content = $header->content ?? [];
        $content['nav_links'] = collect($content['nav_links'] ?? [])
            ->reject(fn ($link) => ($link['href'] ?? '') === '#faq')
            ->values()
            ->all();
        $header->update(['content' => $content]);
    }
};
