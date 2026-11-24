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
            // 1. Libellé lisible de la prestation
            $prestationLabel = match ($billing->service_type) {
                'room_booking', 'reservation_deposit' => 'Hébergement Chambre',
                'restaurant'                          => 'Restauration & Bar',
                'pressing'                            => 'Pressing & Blanchisserie',
                'car_wash'                            => 'Lavage Auto',
                'show_ticket'                         => 'Billetterie Spectacle',
                default                               => ucfirst(str_replace('_', ' ', (string) $billing->service_type)),
            };

            if ($billing->room) {
                $prestationLabel .= " — Ch. {$billing->room->number}";
            } elseif ($billing->eventTicket && $billing->eventTicket->event) {
                $prestationLabel .= " — {$billing->eventTicket->event->title}";
            }

            $totalCost   = (float) $billing->amount_paid;
            $alreadyPaid = (float) $billing->amount_paid;
            $remaining   = 0;

            if ($billing->room_id && in_array($billing->service_type, ['room_booking', 'reservation_deposit'])) {
                // 2. Vérifier si ce reçu correspond à l'acompte d'une réservation créée au même moment (± 1 minute)
                $matchingReservation = \App\Models\Reservation::where('room_id', $billing->room_id)
                    ->whereBetween('created_at', [
                        \Carbon\Carbon::parse($billing->created_at)->subMinute(),
                        \Carbon\Carbon::parse($billing->created_at)->addMinute(),
                    ])
                    ->first();

                if ($matchingReservation) {
                    $prestationLabel = "Arrhes Réservation Ch. {$billing->room->number} ({$matchingReservation->nights} nuit" . ($matchingReservation->nights > 1 ? 's' : '') . ")";
                    $totalCost       = (float) $matchingReservation->total_amount;
                    $alreadyPaid     = (float) $billing->amount_paid;
                    $remaining       = max(0, $totalCost - $alreadyPaid);
                } else {
                    // 3. Sinon, règlement sur place d'un séjour en cours
                    $activeReservation = \App\Models\Reservation::where('room_id', $billing->room_id)
                        ->whereIn('status', ['checked_in', 'completed'])
                        ->whereDate('check_in_date', '<=', $billing->created_at)
                        ->latest('id')
                        ->first();

                    $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')
                        ->where('room_id', $billing->room_id)
                        ->where('created_at', '<=', \Carbon\Carbon::parse($billing->created_at)->addMinutes(5))
                        ->latest('id')
                        ->first();

                    $days = $activeReservation
                        ? (int) $activeReservation->nights
                        : ($activeCard ? (int) ($activeCard->duration_days ?? 1) : 1);

                    $stayStart = $activeReservation
                        ? \Carbon\Carbon::parse($activeReservation->created_at)->subSeconds(5)
                        : ($activeCard ? \Carbon\Carbon::parse($activeCard->created_at)->subSeconds(5) : \Carbon\Carbon::parse($billing->created_at)->startOfDay());

                    $roomBaseCost = ($billing->room ? (float) $billing->room->price_per_night : 0) * $days;

                    $ordersCost = \App\Models\Order::where('room_id', $billing->room_id)
                        ->whereBetween('created_at', [$stayStart, $billing->created_at])
                        ->sum('total_amount');

                    $extrasCost = \App\Models\ExtraService::where('room_id', $billing->room_id)
                        ->whereBetween('created_at', [$stayStart, $billing->created_at])
                        ->sum('total_amount');

                    $activitiesCost = \Illuminate\Support\Facades\DB::table('activity_bookings')
                        ->where('room_id', $billing->room_id)
                        ->whereBetween('created_at', [$stayStart, $billing->created_at])
                        ->sum('total_amount');

                    $totalCost = $roomBaseCost + $ordersCost + $extrasCost + $activitiesCost;

                    $alreadyPaid = \App\Models\Billing::where('room_id', $billing->room_id)
                        ->whereIn('service_type', ['room_booking', 'reservation_deposit'])
                        ->where('status', 'completed')
                        ->whereBetween('created_at', [$stayStart, $billing->created_at])
                        ->sum('amount_paid');

                    $remaining = max(0, $totalCost - $alreadyPaid);
                }
            } elseif ($billing->order_id && $billing->order) {
                $totalCost   = (float) $billing->order->total_amount;
                $alreadyPaid = (float) $billing->amount_paid;
                $remaining   = max(0, $totalCost - $alreadyPaid);
            }
        @endphp

        <p><strong>Prestation :</strong> {{ $prestationLabel }}</p>

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
            @if($alreadyPaid > $billing->amount_paid)
            <tr>
                <td><strong>Total déjà réglé :</strong></td>
                <td class="text-end">{{ number_format($alreadyPaid, 0, ',', ' ') }} F CFA</td>
            </tr>
            @endif
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
