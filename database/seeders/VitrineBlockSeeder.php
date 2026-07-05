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
                    'logo_alt' => 'LDM',
                    'nav_links' => [
                        ['label' => 'Accueil', 'href' => '#accueil'],
                        ['label' => 'Services', 'href' => '/services'],
                        ['label' => 'Process', 'href' => '/process'],
                        ['label' => 'LDM Academy', 'href' => '/academy'],
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
                        ['label' => 'Nos services', 'href' => '/services', 'style' => 'secondary', 'icon' => 'fas fa-arrow-right'],
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
                    'section_label' => 'Les Services',
                    'section_title' => 'Solutions Complètes',
                    'section_subtitle' => 'Une gamme complète de prothèses dentaires pour tous vos besoins',
                    'items' => [
                        [
                            'title' => 'Numérique / CFAO',
                            'slug' => 'numerique-cfao',
                            'description' => 'Conception et fabrication assistées par ordinateur pour une précision optimale.',
                            'image_source_type' => 'url',
                            'image_url' => 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=800&q=80',
                            'content_html' => '<p>Notre laboratoire intègre les technologies numériques les plus avancées : scan intra-oral, conception CAD et usinage CAM pour des prothèses d\'une précision micrométrique.</p><ul><li>Workflow entièrement numérique</li><li>Réduction des délais de fabrication</li><li>Ajustement optimal et reproductibilité</li></ul>',
                        ],
                        [
                            'title' => 'Implantologie',
                            'slug' => 'implantologie',
                            'description' => 'Solutions prothétiques sur implants adaptées à chaque cas clinique.',
                            'image_source_type' => 'url',
                            'image_url' => 'https://images.unsplash.com/photo-1598256989800-fe5f95da9787?w=800&q=80',
                            'content_html' => '<p>Nous réalisons des prothèses implantaires fixes et amovibles avec une attention particulière portée à l\'esthétique gingivale et à la stabilité occlusale.</p><ul><li>Couronnes et bridges sur implants</li><li>Prothèses amovibles sur attachments</li><li>Guides chirurgicaux et planification 3D</li></ul>',
                        ],
                        [
                            'title' => 'Conjointe',
                            'slug' => 'conjointe',
                            'description' => 'Couronnes et bridges céramiques pour des restaurations esthétiques durables.',
                            'image_source_type' => 'url',
                            'image_url' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&q=80',
                            'content_html' => '<p>Prothèses conjointes en zircone, céramique ou métal-céramique, conçues pour s\'intégrer harmonieusement au sourire du patient.</p><ul><li>Couronnes unitaires et bridges</li><li>Stratification céramique personnalisée</li><li>Teintes et morphologies sur mesure</li></ul>',
                        ],
                        [
                            'title' => 'Prothèses Amovibles',
                            'slug' => 'protheses-amovibles',
                            'description' => 'Partielles ou complètes, pour un confort et une esthétique au quotidien.',
                            'image_source_type' => 'url',
                            'image_url' => 'https://images.unsplash.com/photo-1559757175-5700dde675bc?w=800&q=80',
                            'content_html' => '<p>Nos prothèses amovibles allient légèreté, rétention et rendu naturel grâce à des matériaux premium et une finition soignée.</p><ul><li>Stellite et partielles esthétiques</li><li>Prothèses complètes sur mesure</li><li>Réparations et relines rapides</li></ul>',
                        ],
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
                    'description' => 'Nous combinons expertise traditionnelle et technologies numériques de pointe pour vous offrir le meilleur de la prothèse dentaire.',
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
                'key' => 'academy',
                'label' => 'LDM Academy',
                'sort_order' => 7,
                'content' => [
                    'section_label' => 'LDM Academy',
                    'section_title' => 'Ressources pour les praticiens',
                    'section_subtitle' => 'Catalogues, guides techniques, protocoles et notices à télécharger',
                    'documents' => [],
                ],
            ],
            [
                'key' => 'contact',
                'label' => 'Contact',
                'sort_order' => 8,
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
                ],
            ],
            [
                'key' => 'footer',
                'label' => 'Pied de page',
                'sort_order' => 9,
                'content' => [
                    'logo_url' => 'logo_ldm.png',
                    'logo_source_type' => 'url',
                    'logo_alt' => 'LDM',
                    'brand_description' => 'Laboratoire de prothèse dentaire de référence en tunise. Excellence et innovation au service de votre sourire.',
                    'social_links' => [
                        ['label' => 'Facebook', 'url' => '#', 'icon' => 'fab fa-facebook-f'],
                        ['label' => 'Instagram', 'url' => '#', 'icon' => 'fab fa-instagram'],
                        ['label' => 'TikTok', 'url' => '#', 'icon' => 'fab fa-tiktok'],
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
