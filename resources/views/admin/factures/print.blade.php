<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->num_facture }}</title>
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
        th {
            background-color: #f2f2f2;
            font-weight: bold;
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
        .total-row {
            background-color: #f9f9f9;
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
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>FACTURE</h1>
            <p><strong>Numéro :</strong> {{ $facture->num_facture }}</p>
            <p><strong>Date :</strong> {{ $facture->date->format('d/m/Y') }}</p>
        </div>
        <div>
            <h3>Informations</h3>
            <p><strong>Dentiste :</strong> {{ $facture->dentist->full_name ?? $facture->dentist->name }}</p>
        </div>
    </div>

    <div class="content">
        <!-- Bons de Livraison -->
        <div class="section">
            <div class="section-title">Bons de Livraison</div>
            
            @if($facture->bonsLivraison->count() > 0)
                <!-- Tableau unique avec toutes les lignes de tous les BL -->
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th class="text-right">Prix Unitaire TTC</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-right">Total Ligne TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facture->bonsLivraison as $bl)
                            @foreach($bl->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->service_name_snapshot }}</td>
                                    <td class="text-right">{{ number_format($ligne->prix_unitaire_ttc_snapshot, 2, ',', ' ') }} TND</td>
                                    <td class="text-center">{{ $ligne->quantite }}</td>
                                    <td class="text-right">{{ number_format($ligne->total_ligne_ttc, 2, ',', ' ') }} TND</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="total" style="font-size: 1.2em;">Total Facture TTC :</td>
                            <td class="total" style="font-size: 1.2em;">{{ number_format($facture->montant, 2, ',', ' ') }} TND</td>
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
