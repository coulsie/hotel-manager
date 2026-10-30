<?php

namespace App\Http\Controllers;

use App\Models\Show; // Représente la Salle de Spectacle (Infrastructures)
use App\Models\Event; // Représente le Spectacle / Événement programmé
use App\Models\ShowBooking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * 1. Afficher la liste des salles et le formulaire de création de salle
     */
    public function venueIndex()
    {
        // Récupère toutes les salles avec le nombre de spectacles qui y sont programmés
        $venues = Show::withCount('events')->get();
        return view('admin.shows.venues', compact('venues'));
    }

    /**
     * 2. Enregistrer une nouvelle salle de spectacle (Espace physique)
     */
    public function venueStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191|unique:shows,title',
            'total_capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Show::create([
            'title' => $request->title, // Nom de la salle (ex: Salle Opéra)
            'description' => $request->description ?? 'Espace culturel configuré.', // Histoire / Emplacement
            'total_capacity' => $request->total_capacity,
        ]);

        return redirect()->route('admin.venues.index')->with('success', "La nouvelle salle de spectacle a été créée et configurée avec succès.");
    }

    /**
     * Afficher le formulaire de création d'un événement (Spectacle dans une salle)
     */
    public function create()
    {
        $venues = Show::all(); // Nécessaire pour lier le spectacle à une salle physique
        return view('admin.shows.create', compact('venues'));
    }

    /**
     * Enregistrer un spectacle (Lié à une salle, prix de ticket optionnel)
     */
    public function store(Request $request)
    {
        $request->validate([
            'show_id' => 'required|exists:shows,id', // ID de la salle physique sélectionnée
            'title' => 'required|string|max:191',
            'performance_date' => 'required|date',
            'ticket_price' => 'nullable|numeric|min:0', // RENDU OPTIONNEL ICI
            'description' => 'nullable|string',
        ]);

        Event::create([
            'show_id' => $request->show_id,
            'title' => $request->title, // Nom du spectacle (ex: Concert de Jazz)
            'description' => $request->description ?? 'Spectacle programmé',
            'performance_date' => $request->performance_date,
            // Si le prix n'est pas fourni, on lui affecte 0 par défaut
            'ticket_price' => $request->ticket_price ?? 0,
        ]);

        return redirect()->route('admin.venues.index')->with('success', 'Le spectacle a été programmé avec succès !');
    }

    /**
     * Afficher le formulaire de réservation de billet pour un spectacle
     */
    public function showBookingForm()
    {
        // On récupère les spectacles dont la date n'est pas passée, avec les infos de leur salle (venue)
        $shows = Event::with('venue')->where('performance_date', '>', now())->get();
        $rooms = Room::where('status', 'occupied')->get(); // Chambres occupées pour imputation
        $users = User::all(); // Pour associer à un client/utilisateur

        return view('bookings.shows', compact('shows', 'rooms', 'users'));
    }

    /**
     * Traiter la réservation du billet pour un spectacle donné
     */
   
        public function venueCreate()
    {
        return view('admin.shows.create_venue'); // Une vue dédiée à la création de salle
    }


        // Traiter la réservation du billet avec multi-tarifs
    public function processShowBooking(Request $request)
    {
        // 1. Validation des données du formulaire incluant le type et le prix
        $request->validate([
            'event_id'         => 'required|exists:events,id',
            'user_id'          => 'required|exists:users,id',
            'ticket_type'      => 'required|string|in:populaire,honneurs,vip,gratuit',
            'calculated_price' => 'required|numeric|min:0',
            'charge_type'      => 'required|string|in:direct,room',
            'room_id'          => 'required_if:charge_type,room|nullable|exists:rooms,id',
        ]);

        // 2. Récupération du spectacle (Event) et de sa salle physique (Venue)
        $event = Event::with('venue')->findOrFail($request->event_id);
        $venue = $event->venue;

        // 3. Vérification stricte de la jauge / capacité de la salle
        $currentBookings = ShowBooking::where('event_id', $event->id)
            ->whereIn('status', ['confirmed', 'charged_to_room'])
            ->count();

        if ($currentBookings >= $venue->total_capacity) {
            return back()->withErrors(['event_id' => "Désolé, la salle \"{$venue->title}\" est complète pour ce spectacle !"])->withInput();
        }

        // 4. Libellés propres pour le type de ticket
        $ticketLabels = [
            'populaire' => 'Populaire',
            'honneurs'  => "Invité d'Honneur",
            'vip'       => 'VIP / Privilège',
            'gratuit'   => 'Invitation / Gratuit'
        ];
        $typeLibelle = $ticketLabels[$request->ticket_type] ?? 'Standard';

        // 5. Création de la réservation avec prix et type de ticket
        ShowBooking::create([
            'event_id'     => $event->id,
            'show_id'      => $venue->id, // Référence de la salle historique
            'user_id'      => $request->user_id,
            'room_id'      => $request->charge_type === 'room' ? $request->room_id : null,
            'status'       => $request->charge_type === 'room' ? 'charged_to_room' : 'confirmed',
            'seat_number'  => 'S-' . ($currentBookings + 1),
            'ticket_type'  => $request->ticket_type,      // Enregistre: populaire, vip, etc.
            'amount_paid'  => $request->calculated_price, // Enregistre le montant exact calculé
        ]);

        // 6. Préparation du message de confirmation
        $formattedPrice = number_format($request->calculated_price, 0, ',', ' ');
        $msg = "Billet [{$typeLibelle}] émis avec succès pour \"{$event->title}\". Place : S-" . ($currentBookings + 1) . " (Montant : {$formattedPrice} F CFA).";

        if ($request->charge_type === 'room') {
            $room = Room::find($request->room_id);
            $msg .= " Les frais ont été imputés sur la note de la chambre {$room->number}.";
        }

        return redirect()->route('bookings.shows.form')->with('success', $msg);
    }

}
