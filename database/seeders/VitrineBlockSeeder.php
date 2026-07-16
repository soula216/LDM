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
                        ['label' => 'Accueil', 'href' => '/', 'is_active' => true],
                        ['label' => 'Le Laboratoire', 'href' => '/le-laboratoire', 'is_active' => true],
                        ['label' => 'Services', 'href' => '/services', 'is_active' => true],
                        ['label' => 'Process', 'href' => '/process', 'is_active' => true],
                        ['label' => 'Galerie', 'href' => '/gallery', 'is_active' => true],
                        ['label' => 'LDM Academy', 'href' => '/academy', 'is_active' => true],
                        ['label' => 'FAQ', 'href' => '/faq', 'is_active' => true],
                        ['label' => 'Recrutement', 'href' => '/recrutement', 'is_active' => true],
                        ['label' => 'Contact', 'href' => '#contact', 'is_active' => true],
                    ],
                    'client_space_label' => 'Espace client',
                    'client_space_is_active' => true,
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
                'key' => 'about',
                'label' => 'Le Laboratoire',
                'sort_order' => 7,
                'content' => [
                    'section_label' => 'Le Laboratoire',
                    'title' => 'Notre laboratoire, notre engagement',
                    'description' => 'Depuis plus de 15 ans, LDM accompagne les chirurgiens-dentistes avec des prothèses de précision, une expertise technique reconnue et un service client réactif. Découvrez notre univers, notre équipe et notre savoir-faire à travers images et vidéos.',
                    'photos' => [],
                    'videos' => [],
                    'info_pages' => AboutInfoPagesSeeder::infoPages(),
                    'media_page' => AboutInfoPagesSeeder::mediaPage(),
                ],
            ],
            [
                'key' => 'laboratory',
                'label' => 'Galerie',
                'sort_order' => 8,
                'content' => [
                    'section_label' => 'Galerie',
                    'title' => 'Notre équipe & nos installations',
                    'description' => 'Découvrez les professionnels qui composent LDM, nos espaces de travail et les équipements de pointe qui garantissent la précision de nos prothèses.',
                    'photos' => [],
                    'videos' => [],
                    'media' => [],
                ],
            ],
            [
                'key' => 'partners',
                'label' => 'Partenaires',
                'sort_order' => 9,
                'content' => [
                    'section_label' => 'Nos Partenaires',
                    'section_title' => 'Ils Nous Font Confiance',
                    'section_subtitle' => 'Des collaborations solides avec les leaders du secteur dentaire pour vous offrir excellence et innovation.',
                    'items' => [
                        ['name' => 'Straumann', 'url' => '', 'image_url' => 'https://logo.clearbit.com/straumann.com', 'source_type' => 'url', 'is_active' => true],
                        ['name' => 'Ivoclar', 'url' => '', 'image_url' => 'https://logo.clearbit.com/ivoclar.com', 'source_type' => 'url', 'is_active' => true],
                        ['name' => '3Shape', 'url' => '', 'image_url' => 'https://logo.clearbit.com/3shape.com', 'source_type' => 'url', 'is_active' => true],
                        ['name' => 'Dentsply Sirona', 'url' => '', 'image_url' => 'https://logo.clearbit.com/dentsplysirona.com', 'source_type' => 'url', 'is_active' => true],
                        ['name' => 'Nobel Biocare', 'url' => '', 'image_url' => 'https://logo.clearbit.com/nobelbiocare.com', 'source_type' => 'url', 'is_active' => true],
                        ['name' => 'VITA Zahnfabrik', 'url' => '', 'image_url' => 'https://logo.clearbit.com/vita-zahnfabrik.com', 'source_type' => 'url', 'is_active' => true],
                    ],
                ],
            ],
            [
                'key' => 'faq',
                'label' => 'FAQ',
                'sort_order' => 10,
                'content' => [
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
                ],
            ],
            [
                'key' => 'recrutement',
                'label' => 'Recrutement',
                'sort_order' => 11,
                'content' => [
                    'section_label' => 'Recrutement',
                    'section_title' => 'Rejoindre LDM',
                    'section_subtitle' => 'Découvrez nos offres d’emploi et rejoignez une équipe passionnée par l’excellence prothétique.',
                    'items' => RecrutementOffersSeeder::offers(),
                ],
            ],
            [
                'key' => 'academy',
                'label' => 'LDM Academy',
                'sort_order' => 12,
                'content' => [
                    'section_label' => 'LDM Academy',
                    'section_title' => 'Ressources pour les praticiens',
                    'section_subtitle' => 'Catalogues, guides techniques, protocoles et notices à télécharger',
                    'categories' => [
                        ['key' => 'catalogue', 'label' => 'Catalogues', 'icon' => 'fas fa-book-open'],
                        ['key' => 'guide', 'label' => 'Guides techniques', 'icon' => 'fas fa-drafting-compass'],
                        ['key' => 'protocole', 'label' => 'Protocoles', 'icon' => 'fas fa-clipboard-list'],
                        ['key' => 'notice', 'label' => 'Notices', 'icon' => 'fas fa-file-alt'],
                    ],
                    'documents' => [],
                ],
            ],
            [
                'key' => 'contact',
                'label' => 'Contact',
                'sort_order' => 13,
                'content' => [
                    'info_is_active' => true,
                    'form_is_active' => true,
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
                    'form_subtitle' => 'Réponse sous 24 h ouvrées',
                    'form_submit_label' => 'Envoyer ma demande',
                    'map_is_active' => true,
                    'map_title' => 'Notre localisation',
                    'map_address' => '123 Avenue de la Dentisterie, 75001 Paris, France',
                    'map_embed_url' => '',
                ],
            ],
            [
                'key' => 'footer',
                'label' => 'Pied de page',
                'sort_order' => 14,
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
                                ['label' => 'Le Laboratoire', 'href' => '/le-laboratoire'],
                                ['label' => 'Équipe', 'href' => '/le-laboratoire/gallery'],
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
