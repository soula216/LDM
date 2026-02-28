<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facture->titre_document_label }} {{ $facture->num_facture }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 70px;
        }
        .top-header .logo-container {
            flex-shrink: 0;
            margin-top: -10px;
        }
        .top-header .logo-container img {
            width: auto;
        }
        .labo-title {
            font-weight: bold;
            color: #00008B;
            margin-bottom: 0;
            font-size: 18px;
            line-height: 1.8;
        }
        .labo-title .labo-email {
            font-weight: normal;
        }
        .document-title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .document-title-box span {
            display: inline-block;
            font-size: 24px;
            font-weight: bold;
            padding: 10px 30px;
            background-color: #87CEFA;
            border: 2px solid #87CEFA;
            color: #000;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .content {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th:last-child, td:last-child {
            border-right: 1px solid #ddd !important;
        }
        th {
            background-color: #f5f5dc;
            font-weight: bold;
        }
        td {
            background-color: #ffffff;
        }
        table tbody td:nth-child(1), table tbody td:nth-child(2), table tbody td:nth-child(3) {
            font-size: 0.85em;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
        }
        .total-row td {
            background-color: #ffffff;
        }
        tfoot td {
            border-bottom: none !important;
            border-left: none !important;
        }
        tfoot td:last-child {
            border-left: 1px solid #ddd !important;
            border-bottom: 1px solid #ddd !important;
            border-right: 1px solid #ddd !important;
        }
        tfoot tr.total-row-no-top-border td {
            border-top: none !important;
        }
        tfoot tr.total-row-no-top-border td:last-child {
            border-top: 1px solid #ddd !important;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .no-print button {
            padding: 10px 20px;
            background: #3B82F6;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .no-print button:hover {
            background: #2563EB;
        }
        @media print {
            body { 
                padding: 0; 
            }
            .no-print { 
                display: none; 
            }
            @page {
                margin: 1cm;
            }
            /* Forcer la bordure droite colonne Montant en PDF (même couleur #ddd que les autres) */
            table th:last-child,
            table td:last-child,
            tfoot td:last-child {
                border-right: 1px solid #ddd !important;
                box-shadow: inset -1px 0 0 0 #ddd;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="top-header">
        <div class="labo-title">
            Laboratoire Jdidi Souhaieb de prothése dentaire<br>
            Rue de la liberté Sahloul Sousse<br>
            Gsm : 21 000 041 - 54 767 109<br>
            <span class="labo-email">E-mail : ldm.communicate@gmail.com</span><br>
            <span class="labo-email">Diplomé de l'école de la santé - Monastir</span>
        </div>
        <div class="logo-container">
            <img src="{{ asset('logo_ldm.png') }}" alt="LDM">
        </div>
    </div>
    <div class="document-title-box">
        <span>{{ strtoupper($facture->titre_document_label) }}</span>
    </div>
    <div class="header">
        <div>
            <p><strong>Numéro :</strong> {{ $facture->num_facture }}</p>
            <p><strong>Date :</strong> {{ $facture->date->format('d/m/Y') }}</p>
        </div>
        <div>
            @if(!empty(trim($facture->dentist->num_dentist ?? '')))
                <p><strong>Code Client :</strong> {{ $facture->dentist->num_dentist }}</p>
            @endif
            <p><strong>Nom & Prénom :</strong> {{ $facture->dentist->full_name ?? $facture->dentist->name }}</p>
            <p><strong>Adresse :</strong> {{ $facture->dentist->ville ?? '-' }}</p>
        </div>
    </div>

    <div class="content">
        <div class="section">
            @if($facture->bonsLivraison->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>N° BL</th>
                            <th>Nom Patient</th>
                            <th>Travaux</th>
                            <th class="text-right">Prix Uni</th>
                            <th class="text-center">Qt</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facture->bonsLivraison as $bl)
                            @foreach($bl->lignes as $index => $ligne)
                                @php
                                    $tache = $bl->commande->taches[$index] ?? null;
                                    $dents = $tache && trim($tache->dents ?? '') !== '' ? ' ' . trim($tache->dents) : '';
                                @endphp
                                <tr>
                                    <td>{{ $bl->numero_bl }}</td>
                                    <td>{{ $bl->commande->nom_patient ?? '-' }}</td>
                                    <td>{{ $ligne->service_name_snapshot }}{{ $dents }}</td>
                                    <td class="text-right">{{ number_format($ligne->prix_unitaire_ttc_snapshot, 2, ',', ' ') }}</td>
                                    <td class="text-center">{{ $ligne->quantite }}</td>
                                    <td class="text-right">{{ number_format($ligne->total_ligne_ttc, 2, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td style="font-size: 1.5em; font-weight: bold; padding-left: 3em; border-right: none !important;">Signature</td>
                            <td colspan="4" class="total" style="font-size: 1.05em; text-align: right;">Net à Payer</td>
                            <td class="total" style="font-size: 0.9em;">{{ number_format($facture->montant, 2, ',', ' ') }}</td>
                        </tr>
                        <tr class="total-row total-row-no-top-border">
                            <td colspan="5" class="total" style="font-size: 1.05em;">Ancien Solde</td>
                            <td class="total text-right" style="font-size: 0.9em;">{{ number_format($facture->ancien_solde ?? 0, 2, ',', ' ') }}</td>
                        </tr>
                        <tr class="total-row total-row-no-top-border">
                            <td colspan="5" class="total" style="font-size: 1.05em;">Avance</td>
                            <td class="total text-right" style="font-size: 0.9em;">{{ number_format($facture->avance ?? 0, 2, ',', ' ') }}</td>
                        </tr>
                        <tr class="total-row total-row-no-top-border">
                            <td colspan="5" class="total" style="font-size: 1.05em;">Reste à Payer</td>
                            <td class="total" style="font-size: 0.9em;">{{ number_format($facture->montant_restant ?? 0, 2, ',', ' ') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p class="text-center">Aucun bon de livraison</p>
            @endif
        </div>

    </div>

    <div class="no-print">
        <button onclick="window.print()">Imprimer</button>
    </div>
</body>
</html>
