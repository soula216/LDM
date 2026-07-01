<?php

namespace Database\Seeders;

use App\Models\VitrineBlock;
use Illuminate\Database\Seeder;

class VitrineBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'key' => 'header',
                'label' => 'En-tête',
                'sort_order' => 1,
                'content' => [
                    'logo_url' => 'logo_ldm.png',
                    'logo_source_type' => 'url',
                    'logo_alt' => 'LDM - Dentaire Moderne',
                    'nav_links' => [
                        ['label' => 'Accueil', 'href' => '#accueil'],
                        ['label' => 'Services', 'href' => '#services'],
                        ['label' => 'Process', 'href' => '#process'],
                        ['label' => 'Contact', 'href' => '#contact'],
                    ],
                    'client_space_label' => 'Espace client',
                ],
            ],
            [
                'key' => 'hero',
                'label' => 'Hero',
                'sort_order' => 2,
                'content' => [
                    'slides' => [
                        ['image_url' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=1920&q=80'],
                        ['image_url' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=1920&q=80'],
                        ['image_url' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=1920&q=80'],
                    ],
                    'badge_icon' => 'fas fa-certificate',
                    'badge_text' => 'Laboratoire Certifié ISO 13485',
                    'title_line1' => 'Prothèses Dentaires',
                    'title_highlight' => 'de Précision',
                    'description' => "Découvrez l'excellence de la prothèse dentaire. Nous allions technologie de pointe et savoir-faire artisanal pour des sourires parfaits.",
                    'buttons' => [
                        ['label' => 'Demander un devis', 'href' => '#contact', 'style' => 'primary', 'icon' => 'fas fa-calendar-alt'],
                        ['label' => 'Nos services', 'href' => '#services', 'style' => 'secondary', 'icon' => 'fas fa-arrow-right'],
                    ],
                    'card' => [
                        'icon' => 'fas fa-tooth',
                        'title' => 'Prothèse Sur Mesure',
                        'description' => 'Fabrication avec matériaux de haute qualité',
                        'stats' => [
                            ['value' => '15+', 'label' => "Années d'expérience"],
                            ['value' => '5000+', 'label' => 'Patients satisfaits'],
                            ['value' => '99%', 'label' => 'Satisfaction'],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'services',
                'label' => 'Services',
                'sort_order' => 3,
                'content' => [
                    'section_label' => 'Nos Services',
                    'section_title' => 'Solutions Complètes',
                    'section_subtitle' => 'Une gamme complète de prothèses dentaires pour tous vos besoins',
                    'items' => [
                        ['icon' => 'fas fa-crown', 'title' => 'Couronnes Dentaires', 'description' => 'Couronnes en zircone, céramique ou métal-céramique pour une restauration esthétique et durable.'],
                        ['icon' => 'fas fa-teeth', 'title' => 'Bridges & Ponts', 'description' => 'Solutions fixes pour remplacer une ou plusieurs dents manquantes avec un rendu naturel.'],
                        ['icon' => 'fas fa-hand-sparkles', 'title' => 'Prothèses Amovibles', 'description' => 'Partielles ou complètes, nos prothèses offrent confort et esthétique au quotidien.'],
                        ['icon' => 'fas fa-magic', 'title' => 'Facettes & Éclaircissement', 'description' => 'Transformez votre sourire avec des facettes personnalisées et un blanchiment professionnel.'],
                    ],
                ],
            ],
            [
                'key' => 'process',
                'label' => 'Processus',
                'sort_order' => 4,
                'content' => [
                    'section_label' => 'Notre Process',
                    'section_title' => 'Comment Nous Travaillons',
                    'section_subtitle' => 'Un processus rigoureux pour des résultats exceptionnels',
                    'steps' => [
                        ['title' => 'Consultation', 'description' => "Analyse de vos besoins et prise d'empreintes numériques"],
                        ['title' => 'Conception', 'description' => 'Design 3D et planification précise de votre prothèse'],
                        ['title' => 'Fabrication', 'description' => 'Production avec nos équipements de haute précision'],
                        ['title' => 'Livraison', 'description' => 'Pose et ajustements pour un confort optimal'],
                    ],
                ],
            ],
            [
                'key' => 'gallery',
                'label' => 'Galerie',
                'sort_order' => 5,
                'content' => [
                    'section_label' => 'Nos Travaux',
                    'section_title' => 'Découvrez Nos Réalisations',
                    'section_subtitle' => 'Des résultats qui parlent d\'eux-mêmes',
                    'items' => [
                        ['image_url' => 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=600&q=80', 'title' => 'Prothèse Complète', 'description' => 'Restaurations complètes sur implants'],
                        ['image_url' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=600&q=80', 'title' => 'Couronnes Céramiques', 'description' => 'Esthétique naturelle et durable'],
                        ['image_url' => 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?w=600&q=80', 'title' => 'Facettes Dentaires', 'description' => 'Transformations de sourires'],
                        ['image_url' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?w=600&q=80', 'title' => 'Prothèse Partielle', 'description' => 'Solution esthétique et fonctionnelle'],
                        ['image_url' => 'https://images.unsplash.com/photo-1606265752439-1f18756aa5fc?w=600&q=80', 'title' => 'Bridge Sur Implants', 'description' => 'Réhabilitation complète'],
                        ['image_url' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=600&q=80', 'title' => 'Blanchiment', 'description' => 'Éclat et blancheur naturelle'],
                    ],
                ],
            ],
            [
                'key' => 'features',
                'label' => 'À propos',
                'sort_order' => 6,
                'content' => [
                    'title_before' => 'Pourquoi Choisir',
                    'title_highlight' => 'LDM',
                    'title_after' => '?',
                    'description' => 'Nous combinons expertise traditionnelle et technologies numériques de pointe pour vous offrir le meilleur de la prothèse dentaire moderne.',
                    'list' => [
                        'Matériaux premium certifiés biocompatibles',
                        'Technologie CAD/CAM de dernière génération',
                        'Garantie sur toutes nos prothèses',
                        'Délais rapides et respectés',
                    ],
                    'card' => [
                        'icon' => 'fas fa-award',
                        'title' => 'Certification ISO',
                        'description' => 'Qualité supérieure reconnue',
                    ],
                ],
            ],
            [
                'key' => 'contact',
                'label' => 'Contact',
                'sort_order' => 7,
                'content' => [
                    'tag_icon' => 'fas fa-comments',
                    'tag_text' => 'Contactez-nous',
                    'title' => 'Prêt à démarrer votre projet ?',
                    'description' => 'Discutez avec notre équipe pour obtenir un devis personnalisé ou simplement poser vos questions. Nous revenons vers vous sous 24h ouvrées.',
                    'items' => [
                        ['icon' => 'fas fa-location-dot', 'title' => 'Adresse du laboratoire', 'value_1' => '123 Avenue de la Dentisterie', 'value_2' => '75001 Paris'],
                        ['icon' => 'fas fa-phone', 'title' => 'Téléphone', 'value_1' => '+33 1 23 45 67 89', 'value_2' => ''],
                        ['icon' => 'fas fa-envelope', 'title' => 'Email professionnel', 'value_1' => 'contact@ldm-dental.fr', 'value_2' => ''],
                        ['icon' => 'fas fa-clock', 'title' => 'Horaires', 'value_1' => 'Lun – Ven', 'value_2' => '8h00 – 18h00'],
                    ],
                    'form_title' => 'Demander un devis personnalisé',
                    'form_submit_label' => 'Envoyer ma demande',
                    'form_options' => [
                        ['value' => 'couronne', 'label' => 'Couronne / Bridge'],
                        ['value' => 'facettes', 'label' => 'Facettes'],
                        ['value' => 'prothese-fixe', 'label' => 'Prothèse fixe sur implants'],
                        ['value' => 'prothese-amovible', 'label' => 'Prothèse amovible'],
                        ['value' => 'autre', 'label' => 'Autre demande'],
                    ],
                ],
            ],
            [
                'key' => 'footer',
                'label' => 'Pied de page',
                'sort_order' => 8,
                'content' => [
                    'logo_url' => 'logo_ldm.png',
                    'logo_source_type' => 'url',
                    'logo_alt' => 'LDM - Dentaire Moderne',
                    'brand_description' => 'Laboratoire de prothèse dentaire de référence en France. Excellence et innovation au service de votre sourire.',
                    'social_links' => [
                        ['icon' => 'fab fa-facebook-f', 'url' => '#'],
                        ['icon' => 'fab fa-instagram', 'url' => '#'],
                        ['icon' => 'fab fa-linkedin-in', 'url' => '#'],
                    ],
                    'columns' => [
                        [
                            'title' => 'Services',
                            'links' => [
                                ['label' => 'Couronnes', 'href' => '#'],
                                ['label' => 'Bridges', 'href' => '#'],
                                ['label' => 'Prothèses amovibles', 'href' => '#'],
                                ['label' => 'Facettes', 'href' => '#'],
                            ],
                        ],
                        [
                            'title' => 'Entreprise',
                            'links' => [
                                ['label' => 'À propos', 'href' => '#'],
                                ['label' => 'Équipe', 'href' => '#'],
                                ['label' => 'Carrières', 'href' => '#'],
                                ['label' => 'Actualités', 'href' => '#'],
                            ],
                        ],
                        [
                            'title' => 'Contact',
                            'links' => [
                                ['label' => 'Paris, France', 'href' => '#', 'icon' => 'fas fa-map-marker-alt'],
                                ['label' => '+33 1 23 45 67 89', 'href' => '#', 'icon' => 'fas fa-phone'],
                                ['label' => 'contact@dentaltech.fr', 'href' => '#', 'icon' => 'fas fa-envelope'],
                            ],
                        ],
                    ],
                    'copyright' => 'LDM. Tous droits réservés.',
                    'legal_link' => ['label' => 'Mentions légales', 'href' => '#'],
                ],
            ],
        ];

        foreach ($blocks as $block) {
            VitrineBlock::updateOrCreate(
                ['key' => $block['key']],
                [
                    'label' => $block['label'],
                    'content' => $block['content'],
                    'sort_order' => $block['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
