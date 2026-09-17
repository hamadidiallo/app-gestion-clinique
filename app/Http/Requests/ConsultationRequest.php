<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'date_consultation' => 'required|date',

            // Constantes vitales
            'tension_arterielle' => 'nullable|string|max:20',
            'temperature' => 'nullable|numeric|between:30,45',
            'poids' => 'nullable|numeric|between:1,300',
            'taille' => 'nullable|integer|between:20,250',
            'pouls' => 'nullable|integer|between:30,220',
            'frequence_respiratoire' => 'nullable|integer|between:5,60',
            'glycemie' => 'nullable|numeric|between:0.1,10.0',
            'saturation_oxygene' => 'nullable|integer|between:50,100',

            // Dossier Médical / Antécédents (mise à jour facultative du dossier)
            'groupe_sanguin' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'antecedents_personnels' => 'nullable|string',
            'antecedents_familiaux' => 'nullable|string',

            // Partie Médicale
            'motif_consultation' => 'required|string|max:255',
            'histoire_maladie' => 'nullable|string',
            'examen_physique' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'conduite_a_tenir' => 'nullable|string',
            'statut' => 'nullable|string|in:en_attente,en_cours,terminee,annulee',

            // Ordonnance Médicale
            'instructions_generales' => 'nullable|string',
            'prescriptions' => 'nullable|array',
            'prescriptions.*.medicament' => 'required_with:prescriptions|string|max:255',
            'prescriptions.*.forme' => 'nullable|string|max:100',
            'prescriptions.*.dosage' => 'nullable|string|max:100',
            'prescriptions.*.posologie' => 'required_with:prescriptions|string|max:255',
            'prescriptions.*.duree' => 'nullable|string|max:100',
            'prescriptions.*.instructions' => 'nullable|string',
        ];
    }
}
