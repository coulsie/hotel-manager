<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu N° {{ $order->id }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 80mm; margin: 0 auto; padding: 10px; font-size: 12px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="text-center">
        <h3>HOTEL MANAGER</h3>
        <p>Abidjan, Côte d'Ivoire</p>
        <strong>REÇU RESTAURATION</strong>
        <p>Commande N° : {{ $order->id }}<br>Date : {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="line"></div>
    <p>Emplacement : {{ $order->restaurantTable->number ?? 'N/A' }}</p>
    @if($room)
        <p style="color:red;">Mode : Facturé sur Chambre ({{ $room->number }})</p>
    @else
        <p>Mode : Règlement Comptoir Direct</p>
    @endif
    <div class="line"></div>
    <table>
        <thead>
            <tr>
                <th>Qté</th>
                <th>Désignation</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderItems as $item)
                <tr>
                    <td>{{ $item->quantity }} x</td>
                    <td>{{ $item->menuItem->name ?? 'Article' }}</td>
                    <td class="text-end">{{ number_format($item->price_at_order * $item->quantity, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="line"></div>
    <h3 class="text-end">TOTAL : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</h3>
    <div class="line"></div>
    <p class="text-center">Merci de votre visite !<br>Logiciel Hotel-Manager</p>

    <script>
        // Lance automatiquement l'impression matérielle 300ms après le chargement complet des polices
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>
</html>
