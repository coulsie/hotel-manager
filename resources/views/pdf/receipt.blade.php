<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu N° {{ $billing->receipt_number }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 14px; padding: 20px; }
        .ticket { width: 80mm; margin: 0 auto; } /* Format ticket de caisse standard */
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        hr { border-top: 1px dashed #000; }
    </style>
</head>
<body>
    <div class="ticket">
        <h2 class="text-center">HOTEL MANAGER</h2>
        <p class="text-center">Abidjan, Côte d'Ivoire</p>
        <hr>
        <p><strong>RECU N° :</strong> {{ $billing->receipt_number }}</p>
        <p><strong>Date :</strong> {{ $billing->created_at ? $billing->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
        <p><strong>Règlement :</strong> {{ strtoupper(str_replace('_', ' ', $billing->payment_method)) }}</p>
        <hr>

        @php
            $totalCost = $billing->amount_paid;
            $remaining = 0;

            if($billing->service_type === 'room_booking' && $billing->room) {
                $activeCard = \DB::table('magnetic_cards')->where('room_id', $billing->room_id)->latest()->first();
                $days = $activeCard ? ($activeCard->duration_days ?? 2) : 2;
                $roomBaseCost = $billing->room->price_per_night * $days;
                $restaurantOrdersTotal = \App\Models\Order::where('room_id', $billing->room_id)->sum('total_amount');

                $totalCost = $roomBaseCost + $restaurantOrdersTotal;
                $totalPaidForRoom = \App\Models\Billing::where('room_id', $billing->room_id)->where('service_type', 'room_booking')->where('status', 'completed')->sum('amount_paid');
                $remaining = $totalCost - $totalPaidForRoom;
                if($remaining < 0) $remaining = 0;
            }
        @endphp

        <p><strong>Prestation :</strong>
            @if($billing->service_type === 'room_booking') Hébergement Chambre {{ $billing->room->number }}
            @elseif($billing->service_type === 'restaurant') Restauration / Bar
            @else Spectacle
            @endif
        </p>

        <hr>
        <table style="width: 100%;">
            <tr>
                <td><strong>Coût Total :</strong></td>
                <td class="text-end">{{ number_format($totalCost, 0, ',', ' ') }} F CFA</td>
            </tr>
            <tr>
                <td style="color: green;"><strong>Montant Perçu :</strong></td>
                <td class="text-end" style="color: green;"><strong>{{ number_format($billing->amount_paid, 0, ',', ' ') }} F CFA</strong></td>
            </tr>
            <tr>
                <td style="color: red;"><strong>Reste à payer :</strong></td>
                <td class="text-end" style="color: red;">{{ number_format($remaining, 0, ',', ' ') }} F CFA</td>
            </tr>
        </table>
        <hr>
        <p class="text-center fw-bold">Merci de votre visite !</p>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
