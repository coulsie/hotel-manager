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
   /**
 * Enregistrer une nouvelle salle de spectacle (Infrastructure dans la table 'shows').
 */
public function store(Request $request)
{
    // 1. Validation des champs propres à la salle physique
    $validated = $request->validate([
        'title'          => 'required|string|max:191',
        'total_capacity' => 'required|integer|min:1',
        'description'    => 'nullable|string',
    ]);

    // 2. Insertion de la salle dans la table 'shows'
    Show::create([
        'title'          => $validated['title'],
        'total_capacity' => $validated['total_capacity'],
        'description'    => $validated['description'],
    ]);

    // 3. Redirection vers la liste des salles (admin.shows.index)
    return redirect()->route('admin.shows.index')
        ->with('success', 'La nouvelle salle de spectacle a été ajoutée avec succès !');
}

    /**
     * Mettre à jour les informations d'une salle de spectacle existante.
     */
    public function update(Request $request, $id)
    {
        // 1. Récupération de la salle concernée (table: shows)
        $venue = Show::findOrFail($id);

        // 2. Validation des données reçues depuis la fenêtre modale
        $validated = $request->validate([
            'title'          => 'required|string|max:191',
            'total_capacity' => 'required|integer|min:1',
            'description'    => 'nullable|string',
        ]);

        // 3. Mise à jour en base de données
        $venue->update([
            'title'          => $validated['title'],
            'total_capacity' => $validated['total_capacity'],
            'description'    => $validated['description'],
        ]);

        // 4. Redirection vers la liste avec un message de confirmation
        return redirect()->route('admin.shows.index')
            ->with('success', "La salle « {$venue->title} » a été mise à jour avec succès.");
    }


    /**
     * Afficher le formulaire de réservation de billet pour un spectacle
     */

    public function showBookingForm()
    {
        // 1. Récupération des spectacles à venir avec leurs tickets ET leur salle (venue)
        $shows = Event::with(['tickets', 'venue'])
            ->where('performance_date', '>', now())
            ->get();

        // 2. Récupération des chambres occupées pour l'imputation sur note
        $rooms = Room::where('status', 'occupied')->get();

        // 3. Récupération de tous les utilisateurs pour l'association client
        $users = User::all();

        // 4. Envoi unique et propre des variables à la vue Blade
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
    // 1. Validation conditionnelle rigoureuse
    $request->validate([
        'event_id'         => 'required|exists:events,id',
        'beneficiary_type' => 'required|string|in:existing,manual',

        // user_id est obligatoire UNIQUEMENT si le bénéficiaire est un client enregistré ("existing")
        'user_id'          => 'required_if:beneficiary_type,existing|exclude_if:beneficiary_type,manual|nullable|exists:users,id',

        // customer_name est obligatoire UNIQUEMENT si le bénéficiaire est saisi manuellement ("manual")
        'customer_name'    => 'required_if:beneficiary_type,manual|exclude_if:beneficiary_type,existing|nullable|string|max:191',

        'ticket_type'      => 'required|string',
        'calculated_price' => 'required|numeric|min:0',
        'charge_type'      => 'required|string|in:direct,room',
        'room_id'          => 'required_if:charge_type,room|nullable|exists:rooms,id',
    ]);

    // 2. Récupération du spectacle (Event) et de sa salle physique (Venue)
    $event = Event::with('venue')->findOrFail($request->event_id);
    $venue = $event->venue;

    // 🛑 3. RÉCUPÉRATION ET VÉRIFICATION DU STOCK D'UN TYPE DE TICKET SPÉCIFIQUE
    // Liaison directe avec votre table event_tickets
    $ticketStock = \App\Models\EventTicket::where('event_id', $event->id)
        ->where('ticket_type', $request->ticket_type)
        ->first();

    if (!$ticketStock) {
        return back()->withErrors(['ticket_type' => "Cette catégorie de tarification n'existe pas pour ce spectacle."])->withInput();
    }

    // Vérification s'il reste des places dans cette catégorie précise
    if ($ticketStock->available_seats <= 0) {
        return back()->withErrors(['ticket_type' => "Désolé, la catégorie [ " . strtoupper($request->ticket_type) . " ] est complète !"])->withInput();
    }

    // 4. Libellés propres pour le type de ticket
    $ticketLabels = [
        'populaire' => 'Populaire',
        'honneurs'  => "Invité d'Honneur",
        'vip'       => 'VIP / Privilège',
        'gratuit'   => 'Invitation / Gratuit'
    ];
    $typeLibelle = $ticketLabels[$request->ticket_type] ?? strtoupper($request->ticket_type);

    // 5. Détermination du nom à afficher dans le message de succès
    $clientName = "Client Externe";
    if ($request->beneficiary_type === 'existing') {
        $user = User::find($request->user_id);
        if ($user) {
            $clientName = $user->name;
        }
    } else {
        $clientName = $request->customer_name;
    }

    // 🛑 6. TRANSACTION SQL SÉCURISÉE : ÉVITE LES SURRÉSERVATIONS EN SIMULTANÉ
    $seatNumber = '';
    \DB::transaction(function () use ($event, $venue, $request, $ticketStock, &$seatNumber) {

        // Décrémentation de -1 sur la ligne de ticket concernée
        $ticketStock->decrement('available_seats');

        // Attribution du numéro de siège dynamique basé sur les ventes de la catégorie
        $seatNumber = 'S-' . ($ticketStock->total_capacity - $ticketStock->available_seats);

        // Création de la réservation avec gestion des clés conditionnelles
        ShowBooking::create([
            'event_id'      => $event->id,
            'show_id'       => $venue->id,
            'user_id'       => $request->beneficiary_type === 'existing' ? $request->user_id : null,
            'customer_name' => $request->beneficiary_type === 'manual' ? $request->customer_name : null,
            'room_id'       => $request->charge_type === 'room' ? $request->room_id : null,
            'status'        => $request->charge_type === 'room' ? 'charged_to_room' : 'confirmed',
            'seat_number'   => $seatNumber,
            'ticket_type'   => $request->ticket_type,
            'amount_paid'   => $request->calculated_price,
        ]);
    });

    // 7. Préparation du message de confirmation
    $formattedPrice = number_format($request->calculated_price, 0, ',', ' ');
    $msg = "Billet [{$typeLibelle}] émis avec succès pour {$clientName} (\"{$event->title}\"). Place : {$seatNumber} (Montant : {$formattedPrice} F CFA).";

    if ($request->charge_type === 'room') {
        $room = Room::find($request->room_id);
        if ($room) {
            $msg .= " Les frais ont été imputés sur la note de la chambre {$room->number}.";
        }
    }

    return redirect()->route('bookings.shows.form')->with('success', $msg);
}


public function index()
{
    // Récupère toutes les salles de spectacle (table show)
    $venues = \App\Models\Show::all();

    return view('admin.shows.index', compact('venues'));
}



}
