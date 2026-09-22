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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // 1. Normalisation des constantes vitales (virgules, unités courantes)
        if ($this->filled('temperature')) {
            $val = str_replace(',', '.', trim((string) $this->temperature));
            $merge['temperature'] = is_numeric($val) ? (float) $val : $this->temperature;
        }

        if ($this->filled('poids')) {
            $val = str_replace(',', '.', trim((string) $this->poids));
            $merge['poids'] = is_numeric($val) ? (float) $val : $this->poids;
        }

        if ($this->filled('taille')) {
            $val = str_replace(',', '.', trim((string) $this->taille));
            if (is_numeric($val)) {
                $tailleNum = (float) $val;
                // Si saisie en mètres (ex: 1.75 ou 1.8), convertir en centimètres (175 ou 180)
                if ($tailleNum > 0.4 && $tailleNum < 3.0) {
                    $tailleNum = round($tailleNum * 100);
                }
                $merge['taille'] = (int) round($tailleNum);
            }
        }

        if ($this->filled('glycemie')) {
            $val = str_replace(',', '.', trim((string) $this->glycemie));
            if (is_numeric($val)) {
                $glycemieNum = (float) $val;
                // Si saisie en mg/dL (ex: 95 mg/dL ou 110 mg/dL), convertir en g/L (0.95 ou 1.10)
                if ($glycemieNum > 15.0) {
                    $glycemieNum = round($glycemieNum / 100, 2);
                }
                $merge['glycemie'] = $glycemieNum;
            }
        }

        // 2. Nettoyage des lignes de prescriptions vides
        if ($this->has('prescriptions') && is_array($this->prescriptions)) {
            $cleaned = array_values(array_filter($this->prescriptions, function ($item) {
                if (! is_array($item)) {
                    return false;
                }
                $medicament = trim((string) ($item['medicament'] ?? ''));
                $posologie = trim((string) ($item['posologie'] ?? ''));
                $dosage = trim((string) ($item['dosage'] ?? ''));
                $duree = trim((string) ($item['duree'] ?? ''));

                return $medicament !== '' || $posologie !== '' || $dosage !== '' || $duree !== '';
            }));

            $merge['prescriptions'] = ! empty($cleaned) ? $cleaned : null;
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
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
            'taille' => 'nullable|numeric|between:20,250',
            'pouls' => 'nullable|integer|between:30,220',
            'frequence_respiratoire' => 'nullable|integer|between:5,60',
            'glycemie' => 'nullable|numeric|between:0.1,15.0',
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
            'prescriptions.*.medicament' => 'required|string|max:255',
            'prescriptions.*.forme' => 'nullable|string|max:100',
            'prescriptions.*.dosage' => 'nullable|string|max:100',
            'prescriptions.*.posologie' => 'required|string|max:255',
            'prescriptions.*.duree' => 'nullable|string|max:100',
            'prescriptions.*.instructions' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Le choix d\'un patient est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné est introuvable.',
            'date_consultation.required' => 'La date et l\'heure de la consultation sont obligatoires.',
            'motif_consultation.required' => 'Le motif de la consultation est obligatoire.',

            // Constantes vitales
            'temperature.numeric' => 'La température doit être un nombre valide (ex: 37.2).',
            'temperature.between' => 'La température doit être comprise entre :min°C et :max°C.',
            'poids.numeric' => 'Le poids doit être un nombre valide en kg (ex: 70 ou 70.5).',
            'poids.between' => 'Le poids doit être compris entre :min kg et :max kg.',
            'taille.numeric' => 'La taille doit être comprise entre :min cm et :max cm (ex: 175 cm).',
            'taille.between' => 'La taille doit être comprise entre :min cm et :max cm (ex: 175 cm).',
            'pouls.integer' => 'Le pouls doit être un nombre entier de battements par minute (ex: 75).',
            'pouls.between' => 'Le pouls doit être compris entre :min bpm et :max bpm.',
            'frequence_respiratoire.integer' => 'La fréquence respiratoire doit être un entier (ex: 18).',
            'frequence_respiratoire.between' => 'La fréquence respiratoire doit être comprise entre :min et :max cpm.',
            'glycemie.numeric' => 'La glycémie doit être un nombre valide (ex: 0.95 g/L ou 95 mg/dL).',
            'glycemie.between' => 'La glycémie doit être comprise entre :min g/L et :max g/L (ex: 0.95 g/L ou 95 mg/dL).',
            'saturation_oxygene.integer' => 'La saturation en oxygène (SpO2) doit être un entier (ex: 98).',
            'saturation_oxygene.between' => 'La saturation en oxygène doit être comprise entre :min% et :max%.',

            // Prescriptions
            'prescriptions.*.medicament.required' => 'Le nom du médicament est requis pour chaque prescription.',
            'prescriptions.*.posologie.required' => 'La posologie est obligatoire pour chaque médicament prescrit.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'patient_id' => 'patient',
            'medecin_id' => 'médecin',
            'date_consultation' => 'date de consultation',
            'motif_consultation' => 'motif de consultation',
            'temperature' => 'température',
            'poids' => 'poids',
            'taille' => 'taille',
            'pouls' => 'pouls',
            'frequence_respiratoire' => 'fréquence respiratoire',
            'glycemie' => 'glycémie',
            'saturation_oxygene' => 'saturation en oxygène',
            'tension_arterielle' => 'tension artérielle',
            'prescriptions.*.medicament' => 'nom du médicament',
            'prescriptions.*.posologie' => 'posologie',
        ];
    }
}
