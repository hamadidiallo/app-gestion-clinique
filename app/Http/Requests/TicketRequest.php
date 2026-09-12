<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour un ticket de facturation.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le patient associé est obligatoire et doit exister
            'patient_id' => 'required|exists:patients,id',

            // L'organisme d'assurance associé (facultatif si paiement direct)
            'assurance_id' => 'nullable|exists:assurances,id',

            // Le service médical concerné (facultatif)
            'service_id' => 'nullable|exists:services,id',

            // Le médecin traitant (facultatif)
            'medecin_id' => 'nullable|exists:medecins,id',

            // L'utilisateur agent/caissier créateur est facultatif dans la requête (auto-assigné)
            'user_id' => 'nullable|exists:users,id',

            // La référence unique du ticket est facultative dans la requête (auto-générée si vide)
            'reference' => 'nullable|string|max:100',

            // La date d'émission du ticket
            'date_ticket' => 'required|date',

            // La date d'expiration (7 jours par défaut)
            'date_expiration' => 'nullable|date|after_or_equal:date_ticket',

            // Montants de facturation et taux de prise en charge en pourcentage (%)
            'montant_total' => 'required|numeric|min:0',
            'taux_assurance' => 'nullable|numeric|min:0|max:100',
            'montant_assurance' => 'nullable|numeric|min:0',
            'montant_patient' => 'required|numeric|min:0',
            'montant_paye' => 'nullable|numeric|min:0',
            'reste_a_payer' => 'nullable|numeric|min:0',

            // Statut du ticket (ex: paye, impaye, partiel, annule)
            'statut' => 'required|string|max:50',

            // Description optionnelle
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages de validation personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Le choix d\'un patient est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
            'user_id.required' => 'L\'agent créateur est obligatoire.',
            'reference.required' => 'La référence du ticket est obligatoire.',
            'date_ticket.required' => 'La date du ticket est obligatoire.',
            'montant_total.required' => 'Le montant total est obligatoire.',
            'montant_patient.required' => 'Le montant à la charge du patient est obligatoire.',
            'statut.required' => 'Le statut du ticket est obligatoire.',
        ];
    }
}
