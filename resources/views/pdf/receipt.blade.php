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

            // 1. Calcul des soldes si règlement d'hébergement
            if ($billing->service_type === 'room_booking' && $billing->room) {
                $activeCard = \Illuminate\Support\Facades\DB::table('magnetic_cards')->where('room_id', $billing->room_id)->latest()->first();
                $days = $activeCard ? ($activeCard->duration_days ?? 1) : 1;
                $roomBaseCost = $billing->room->price_per_night * $days;
                $restaurantOrdersTotal = \App\Models\Order::where('room_id', $billing->room_id)->sum('total_amount');
                $extraServicesTotal = \App\Models\ExtraService::where('room_id', $billing->room_id)->sum('total_amount');
                $activitiesTotal = \Illuminate\Support\Facades\DB::table('activity_bookings')->where('room_id', $billing->room_id)->sum('total_amount');

                $totalCost = $roomBaseCost + $restaurantOrdersTotal + $extraServicesTotal + $activitiesTotal;

                $checkedInReservation = \App\Models\Reservation::where('room_id', $billing->room_id)->where('status', 'checked_in')->latest('updated_at')->first();
                $depositPaid = $checkedInReservation ? (float) $checkedInReservation->deposit_paid : 0;

                $totalPaidForRoom = \App\Models\Billing::where('room_id', $billing->room_id)
                    ->where('service_type', 'room_booking')
                    ->where('status', 'completed')
                    ->sum('amount_paid') + $depositPaid;

                $remaining = max(0, $totalCost - $totalPaidForRoom);
            }

            // 2. Détermination précise du libellé de la prestation
            $prestationLabel = match($billing->service_type) {
                'room_booking'        => 'Hébergement Chambre ' . ($billing->room->number ?? ''),
                'reservation_deposit' => 'Arrhes Réservation Ch. ' . ($billing->room->number ?? ''),
                'restaurant'          => 'Restauration / Bar' . ($billing->order && $billing->order->restaurantTable ? ' (' . $billing->order->restaurantTable->number . ')' : ''),
                'pressing'            => 'Pressing & Blanchisserie',
                'car_wash'            => 'Lavage Auto',
                'show_ticket'         => 'Spectacle' . ($billing->eventTicket && $billing->eventTicket->event ? ' : ' . $billing->eventTicket->event->title : ''),
                'piscine'             => 'Pass Piscine & Transats',
                'spa'                 => 'Spa & Bien-être',
                'coworking'           => 'Coworking & Day-Use',
                'seminaire'           => 'Location Salle de Séminaire',
                'navette'             => 'Navette & Location Véhicule',
                'boutique'            => 'Boutique & Services Divers',
                default               => ucfirst(str_replace('_', ' ', $billing->service_type)),
            };

            // Si c'est une activité annexe, on tente de retrouver le nom exact dans activity_bookings
            if (in_array($billing->service_type, ['piscine', 'spa', 'coworking', 'seminaire', 'navette', 'boutique'])) {
                $activityMatch = \Illuminate\Support\Facades\DB::table('activity_bookings')
                    ->join('hotel_activities', 'activity_bookings.hotel_activity_id', '=', 'hotel_activities.id')
                    ->where('hotel_activities.category', $billing->service_type)
                    ->where('activity_bookings.total_amount', $billing->amount_paid)
                    ->whereDate('activity_bookings.created_at', $billing->created_at ? $billing->created_at->toDateString() : today())
                    ->orderByDesc('activity_bookings.id')
                    ->select('hotel_activities.name', 'activity_bookings.quantity')
                    ->first();

                if ($activityMatch) {
                    $prestationLabel = $activityMatch->name . ' (x' . $activityMatch->quantity . ')';
                }
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
