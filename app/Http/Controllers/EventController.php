<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\Show; // Représente vos Salles de spectacle
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class EventController extends Controller
{
    /**
 * Afficher la liste de tous les spectacles/concerts programmés.
 */
public function index()
{
    // Récupère les spectacles avec les grilles de tickets et les infos de la salle (table show)
    $events = Event::with(['tickets', 'venue'])->orderBy('performance_date', 'asc')->get();

    // Renvoie vers une vue index (à créer ou à adapter selon votre dossier)
    return view('admin.events.index', compact('events'));
}

public function edit($id)
{
    $event = Event::with('tickets')->findOrFail($id);
    $shows = Show::all(); // Pour pouvoir re-sélectionner la salle si besoin
    return view('admin.events.edit', compact('event', 'shows'));
}

public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $validated = $request->validate([
        'show_id'                  => 'required|exists:shows,id',
        'title'                    => 'required|string|max:191',
        'description'              => 'nullable|string',
        'performance_date'         => 'required|date',
        'tickets'                  => 'required|array|min:1',
        'tickets.*.ticket_type'    => 'required|string',
        'tickets.*.total_capacity' => 'required|integer|min:1',
        'tickets.*.price'          => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($event, $validated) {
        // 1. Mise à jour de l'événement principal
        $event->update([
            'show_id'          => $validated['show_id'],
            'title'            => $validated['title'],
            'description'      => $validated['description'],
            'performance_date' => $validated['performance_date'],
        ]);

        // 2. Nettoyage des anciennes grilles tarifaires de cet événement
        $event->tickets()->delete();

        // 3. Ré-insertion de la nouvelle grille modifiée
        foreach ($validated['tickets'] as $ticketData) {
            $event->tickets()->create([
                'ticket_type'     => $ticketData['ticket_type'],
                'total_capacity'  => $ticketData['total_capacity'],
                'available_seats' => $ticketData['total_capacity'], // Réinitialisé à la capacité mise à jour
                'price'           => $ticketData['price'],
            ]);
        }
    });

    return redirect()->route('admin.events.index')->with('success', 'Le spectacle a été mis à jour avec succès.');
}



    /**
     * Afficher le formulaire de création d'un spectacle.
     */
    public function create()
    {
        // Récupération de toutes les salles (nommé $shows pour s'aligner avec le foreach de votre Blade)
        $shows = Show::all();

        return view('admin.events.create', compact('shows'));
    }

    /**
     * Enregistrer le nouveau spectacle dans la base de données.
     */
    public function store(Request $request)
    {
        // 1. Validation rigoureuse des données de l'événement et de son tableau de tickets
        $validated = $request->validate([
            'show_id'                  => 'required|exists:shows,id',
            'title'                    => 'required|string|max:191',
            'description'              => 'nullable|string',
            'performance_date'         => 'required|date',

            // Validation de la grille de tickets dynamiques envoyée par le formulaire
            'tickets'                  => 'required|array|min:1',
            'tickets.*.ticket_type'    => 'required|string|max:191',
            'tickets.*.total_capacity' => 'required|integer|min:1',
            'tickets.*.price'          => 'required|numeric|min:0',
        ]);

        try {
            // 2. Utilisation d'une transaction pour sécuriser l'écriture simultanée dans les deux tables
            DB::beginTransaction();

            // 3. Insertion de l'événement principal
            $event = Event::create([
                'show_id'          => $validated['show_id'],
                'title'            => $validated['title'],
                'description'      => $validated['description'],
                'performance_date' => $validated['performance_date'],
            ]);

            // 4. Boucle d'enregistrement de chaque catégorie de ticket associée
            foreach ($validated['tickets'] as $ticketData) {
                EventTicket::create([
                    'event_id'        => $event->id,
                    'ticket_type'     => $ticketData['ticket_type'],
                    'total_capacity'  => $ticketData['total_capacity'],
                    // Initialisation des places disponibles au même niveau que la capacité totale
                    'available_seats' => $ticketData['total_capacity'],
                    'price'           => $ticketData['price'],
                ]);
            }

            // Validation définitive des écritures SQL
            DB::commit();

            // 5. Redirection d'origine vers votre formulaire de billetterie
            return redirect()->route('bookings.shows.form')
                ->with('success', 'Le spectacle et sa grille tarifaire ont été programmés avec succès !');

        } catch (\Exception $e) {
            // En cas de problème technique, on annule toutes les opérations pour éviter les incohérences
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage()]);
        }
    }
}
