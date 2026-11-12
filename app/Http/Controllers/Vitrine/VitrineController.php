<?php

namespace App\Http\Controllers\Vitrine;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VitrineController extends Controller
{
    /**
     * Page de présentation publique de MediGest.
     * Un utilisateur déjà connecté est envoyé directement sur son tableau de bord.
     */
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $contact = array_filter(config('medigest.contact', []));

        return view('vitrine.index', [
            'formules' => config('medigest.formules'),
            'contact' => $contact,
        ]);
    }
}
