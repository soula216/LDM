<?php

namespace Database\Seeders;

use App\Models\VitrineBlock;
use Illuminate\Database\Seeder;

class AboutInfoPagesSeeder extends Seeder
{
    public function run(): void
    {
        $about = VitrineBlock::query()->where('key', 'about')->first();

        if (! $about) {
            $this->command?->warn('Bloc « about » introuvable. Lancez d\'abord VitrineBlockSeeder.');

            return;
        }

        $content = $about->content ?? [];
        $content['info_pages'] = static::infoPages();

        $about->update(['content' => $content]);

        $this->command?->info('Pages détaillées du Laboratoire renseignées (conditions, garantie, délais, qualité).');
    }

    /**
     * @return array<string, array{title: string, content_html: string}>
     */
    public static function infoPages(): array
    {
        return [
            'conditions-de-service' => [
                'title' => 'Conditions de service',
                'content_html' => <<<'HTML'
<h2>Objet</h2>
<p>Les présentes conditions de service définissent les modalités de collaboration entre <strong>LDM – Digital Max</strong> et les cabinets dentaires partenaires pour la conception, la fabrication et la livraison de dispositifs prothétiques.</p>

<h3>Périmètre des prestations</h3>
<ul>
<li>Prothèses fixes (couronnes, bridges, inlays/onlays, facettes)</li>
<li>Prothèses implantaires et guides chirurgicaux</li>
<li>Prothèses amovibles partielles et complètes</li>
<li>Travaux numériques (conception CAD, usinage CAM, impression 3D)</li>
<li>Réparations, modifications et retouches sous conditions</li>
</ul>

<h3>Réception et validation des dossiers</h3>
<p>Chaque commande doit être accompagnée des éléments nécessaires à sa réalisation :</p>
<ol>
<li>Fiche de prescription dûment complétée (matériau, teinte, date souhaitée, observations cliniques)</li>
<li>Fichiers numériques exploitables (STL/PLY) ou empreintes conventionnelles correctement conditionnées</li>
<li>Références implantaires, scans opposants et informations occlusales lorsque cela est requis</li>
</ol>
<p>LDM se réserve le droit de solliciter des informations complémentaires avant de lancer la fabrication. Le délai annoncé commence à compter de la validation complète du dossier.</p>

<h3>Obligations du praticien</h3>
<ul>
<li>Fournir des données cliniques exactes et exploitables</li>
<li>Valider les étapes critiques (essayages, designs virtuels) dans les délais convenus</li>
<li>Signaler toute particularité anatomique ou exigence esthétique spécifique</li>
</ul>

<h3>Obligations du laboratoire</h3>
<ul>
<li>Réaliser les travaux selon les règles de l’art et les données transmises</li>
<li>Utiliser des matériaux adaptés et traçables</li>
<li>Informer le cabinet en cas d’impossibilité technique ou de risque d’écart de délai</li>
<li>Assurer un contrôle qualité avant expédition</li>
</ul>

<h3>Modifications et annulations</h3>
<p>Toute modification après lancement de fabrication peut entraîner un ajustement de délai et/ou de tarif. Une commande annulée après démarrage effectif des étapes techniques pourra être facturée au prorata des travaux déjà réalisés.</p>

<h3>Livraison et réception</h3>
<p>Les travaux sont livrés selon le mode convenu (coursier, transporteur ou retrait). À réception, le praticien vérifie l’intégrité du colis et la conformité apparente du dispositif. Toute réserve doit être signalée rapidement afin d’organiser une reprise si nécessaire.</p>

<p><em>Pour toute question relative à l’application de ces conditions, contactez notre équipe via le formulaire de contact ou votre interlocuteur commercial LDM.</em></p>
HTML,
            ],
            'conditions-de-paiement' => [
                'title' => 'Conditions de paiement',
                'content_html' => <<<'HTML'
<h2>Modalités de facturation</h2>
<p>LDM établit une facture pour chaque prestation réalisée, conformément au tarif convenu avec le cabinet (grille générale ou conditions particulières négociées).</p>

<h3>Échéances</h3>
<ul>
<li><strong>Règlement standard :</strong> paiement à 30 jours date de facture, sauf accord spécifique.</li>
<li><strong>Nouveaux cabinets :</strong> un acompte ou un paiement à la commande peut être demandé lors des premières collaborations.</li>
<li><strong>Travaux urgents :</strong> des modalités particulières peuvent s’appliquer et sont confirmées avant exécution.</li>
</ul>

<h3>Moyens de paiement acceptés</h3>
<ul>
<li>Virement bancaire</li>
<li>Chèque (sous réserve d’encaissement)</li>
<li>Paiement via les canaux précisés sur la facture</li>
</ul>

<h3>Acomptes et soldes</h3>
<p>Pour certains dossiers complexes (prothèses implantaires étendues, séries importantes, fabrications sur mesure longues), un acompte peut être requis. Le solde est alors exigible à la livraison ou selon l’échéancier contractuel.</p>

<table>
<thead>
<tr>
<th>Situation</th>
<th>Condition habituelle</th>
</tr>
</thead>
<tbody>
<tr>
<td>Travaux courants</td>
<td>Facturation à livraisons / 30 jours</td>
</tr>
<tr>
<td>Nouveau compte</td>
<td>Acompte ou paiement anticipé possible</td>
</tr>
<tr>
<td>Dossier complexe / volume élevé</td>
<td>Acompte à la validation du devis</td>
</tr>
<tr>
<td>Retard de paiement</td>
<td>Relance puis suspension possible des livraisons</td>
</tr>
</tbody>
</table>

<h3>Retards et litiges</h3>
<p>En cas de retard de paiement, LDM pourra :</p>
<ol>
<li>Adresser des rappels amiables</li>
<li>Suspendre temporairement la prise en charge de nouveaux dossiers</li>
<li>Reporter les livraisons non urgentes jusqu’à régularisation</li>
</ol>
<p>Tout litige relatif à une facture doit être notifié par écrit dans les meilleurs délais, avec les références du dossier concerné.</p>

<h3>Devis et tarifs</h3>
<p>Les tarifs peuvent évoluer selon les matériaux, la complexité clinique et les options demandées (urgent, teintes personnalisées, guides, etc.). Un devis est communiqué sur demande avant engagement pour les prestations hors grille.</p>
HTML,
            ],
            'garantie' => [
                'title' => 'Garantie',
                'content_html' => <<<'HTML'
<h2>Engagement qualité LDM</h2>
<p>LDM s’engage sur la qualité de fabrication de ses dispositifs, dans le respect des prescriptions transmises et des matériaux sélectionnés. La garantie couvre les défauts de conception ou de fabrication imputables au laboratoire.</p>

<h3>Ce qui est couvert</h3>
<ul>
<li>Défauts de structure liés à la fabrication (fissuration précoce, décollement anormaux liés au process labo)</li>
<li>Non-conformité manifeste au design validé / à la prescription technique</li>
<li>Erreur de matériau ou de teinte par rapport aux indications claires fournies et confirmées</li>
</ul>

<h3>Ce qui n’est pas couvert</h3>
<ul>
<li>Usure normale liée à l’usage clinique</li>
<li>Fractures ou échecs liés à des contraintes occlusales excessives, bruxisme non pris en compte, ou conditions cliniques défavorables</li>
<li>Modifications réalisées hors du laboratoire</li>
<li>Problèmes issus d’empreintes / fichiers inexploitables ou d’informations incomplètes</li>
<li>Essayages non réalisés alors qu’ils étaient recommandés</li>
</ul>

<h3>Durée indicative</h3>
<p>Sauf mention particulière sur le devis ou la fiche produit :</p>
<ul>
<li><strong>Prothèses zircone / céramique :</strong> garantie atelier standard soumise à analyse du cas</li>
<li><strong>Structures implantaires :</strong> couverture selon le type de restauration et la traçabilité transmise</li>
<li><strong>Réparations :</strong> conditions de reprise évaluées au cas par cas</li>
</ul>

<h3>Procédure de reprise</h3>
<ol>
<li>Contacter LDM avec la référence dossier et une description du problème</li>
<li>Transmettre photos et informations cliniques utiles</li>
<li>Retourner le dispositif si nécessaire, correctement emballé</li>
<li>Recevoir le diagnostic atelier puis la proposition de reprise / remplacement / avoir</li>
</ol>

<p>La décision finale d’application de la garantie est prise après analyse technique du dispositif et des éléments fournis. Notre objectif reste de trouver une solution rapide et équitable pour le cabinet et le patient.</p>
HTML,
            ],
            'delais-de-fabrication' => [
                'title' => 'Délais de fabrication',
                'content_html' => <<<'HTML'
<h2>Délais standards</h2>
<p>Les délais ci-dessous sont donnés à titre indicatif pour un dossier complet et validé. Ils peuvent varier selon la charge de production, la complexité du cas et la disponibilité des matériaux.</p>

<table>
<thead>
<tr>
<th>Type de travail</th>
<th>Délai indicatif</th>
</tr>
</thead>
<tbody>
<tr>
<td>Couronne / inlay-onlay (workflow numérique)</td>
<td>5 à 7 jours ouvrés</td>
</tr>
<tr>
<td>Bridge de 3 éléments</td>
<td>7 à 10 jours ouvrés</td>
</tr>
<tr>
<td>Prothèse implantaire unitaire</td>
<td>7 à 12 jours ouvrés</td>
</tr>
<tr>
<td>Prothèse amovible</td>
<td>8 à 14 jours ouvrés</td>
</tr>
<tr>
<td>Guide chirurgical</td>
<td>4 à 7 jours ouvrés</td>
</tr>
<tr>
<td>Réparation simple</td>
<td>24 à 72 heures ouvrées</td>
</tr>
</tbody>
</table>

<h3>Point de départ du délai</h3>
<p>Le délai démarre lorsque le laboratoire dispose de :</p>
<ul>
<li>La prescription complète</li>
<li>Des fichiers / empreintes exploitables</li>
<li>Des validations nécessaires (design, teinte, essayage) si elles sont requises</li>
</ul>

<h3>Options express</h3>
<p>Des fabrications accélérées peuvent être proposées selon les créneaux disponibles. L’option express doit être confirmée explicitement par LDM ; elle peut entraîner un supplément tarifaire.</p>

<h3>Facteurs pouvant allonger le délai</h3>
<ul>
<li>Demandes de retouches après essayage</li>
<li>Attente de validation du design virtuel</li>
<li>Fichiers à corriger ou empreintes à refaire</li>
<li>Personnalisation esthétique poussée</li>
<li>Pic d’activité ou disponibilité matériaux spécifiques</li>
</ul>

<h3>Communication</h3>
<p>Un suivi peut être demandé à tout moment auprès de notre équipe. En cas de risque de dépassement, nous vous informons dès que possible afin d’anticiper l’organisation clinique.</p>
HTML,
            ],
            'processus-de-qualite' => [
                'title' => 'Processus de qualité',
                'content_html' => <<<'HTML'
<h2>Notre exigence qualité</h2>
<p>Chez LDM, chaque restauration suit un parcours contrôlé, de la réception du dossier jusqu’à l’expédition. L’objectif : garantir précision, reproductibilité et fiabilité clinique.</p>

<h3>1. Réception &amp; contrôle d’entrée</h3>
<ul>
<li>Vérification de la fiche de prescription</li>
<li>Contrôle de l’intégrité des fichiers STL / empreintes</li>
<li>Identification des manques et demande de complément si nécessaire</li>
</ul>

<h3>2. Conception numérique</h3>
<ul>
<li>Modélisation selon les consignes cliniques</li>
<li>Contrôle des contacts, des émergences et de l’occlusion virtuelle</li>
<li>Validation interne avant usinage / impression</li>
</ul>

<h3>3. Fabrication</h3>
<ul>
<li>Usinage CAM ou fabrication additive selon le cas</li>
<li>Utilisation de matériaux certifiés et traçables</li>
<li>Suivi des paramètres machine et des lots matières</li>
</ul>

<h3>4. Finition &amp; contrôle final</h3>
<ol>
<li>Ajustements d’adaptation et de contacts</li>
<li>Contrôle esthétique (morphologie, teinte, texture)</li>
<li>Vérification de la conformité à la prescription</li>
<li>Nettoyage, conditionnement et étiquetage du dossier</li>
</ol>

<h3>5. Traçabilité</h3>
<p>Chaque dossier conserve une traçabilité des étapes clés : réception, conception, fabrication, contrôles et expédition. Cette organisation facilite les reprises, le suivi qualité et la communication avec le cabinet.</p>

<h3>Amélioration continue</h3>
<p>Les retours cabinets sont analysés pour améliorer nos process (réduction des reprises, standardisation des contrôles, formation des équipes). La qualité n’est pas une étape isolée : c’est le fil conducteur de notre production quotidienne.</p>

<p><strong>Résultat attendu :</strong> des prothèses fiables, bien documentées, livrées dans un cadre maîtrisé et transparent.</p>
HTML,
            ],
        ];
    }
}
