<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BL {{ $bl->numero_bl }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
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
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2em;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>BON DE LIVRAISON</h1>
            <p><strong>Numéro :</strong> {{ $bl->numero_bl }}</p>
            <p><strong>Date :</strong> {{ $bl->created_at->format('d/m/Y') }}</p>
        </div>
        <div>
            <h3>Commande</h3>
            <p><strong>Numéro :</strong> {{ $bl->commande->num_cmd }}</p>
            @if($bl->commande->nom_patient)
            <p><strong>Patient :</strong> {{ $bl->commande->nom_patient }}</p>
            @endif
            <p><strong>Dentiste :</strong> {{ $bl->commande->dentiste->full_name ?? $bl->commande->dentiste->name }}</p>
        </div>
    </div>

    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Prix Unitaire TTC</th>
                    <th>Quantité</th>
                    <th>Total Ligne TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bl->lignes as $ligne)
                    <tr>
                        <td>{{ $ligne->service_name_snapshot }}</td>
                        <td>{{ number_format($ligne->prix_unitaire_ttc_snapshot, 2) }} TND</td>
                        <td>{{ $ligne->quantite }}</td>
                        <td>{{ number_format($ligne->total_ligne_ttc, 2) }} TND</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total">Total TTC :</td>
                    <td class="total">{{ number_format($bl->total_ttc, 2) }} TND</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 4px;">
            Imprimer
        </button>
    </div>
</body>
</html>
