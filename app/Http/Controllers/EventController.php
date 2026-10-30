<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Show; // Représente vos Salles de spectacle
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Afficher le formulaire de création d'un spectacle.
     */
    public function create()
    {
        // On récupère toutes les salles de spectacle pour la liste déroulante
        $venues = Show::all();

        return view('admin.events.create', compact('venues'));
    }

    /**
     * Enregistrer le nouveau spectacle dans la base de données.
     */
    public function store(Request $request)
    {
        // 1. Validation rigoureuse des données envoyées
        $request->validate([
            'show_id'          => 'required|exists:shows,id', // La salle sélectionnée doit exister
            'title'            => 'required|string|max:191',
            'description'      => 'nullable|string',
            'performance_date' => 'required|date',
            'ticket_price'     => 'nullable|numeric|min:0', // RENDU OPTIONNEL (Accepté si vide)
        ]);

        // 2. Assignation de masse et insertion
        Event::create([
            'show_id'          => $request->show_id,
            'title'            => $request->title,
            'description'      => $request->description,
            'performance_date' => $request->performance_date,
            // Si le prix du ticket est laissé vide, on enregistre 0.00 automatiquement
            'ticket_price'     => $request->ticket_price ?? 0.00,
        ]);

        // 3. Redirection avec un message de succès
        return redirect()->route('bookings.shows.form')
            ->with('success', 'Le spectacle a été programmé avec succès !');
    }
}
