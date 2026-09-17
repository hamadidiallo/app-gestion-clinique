/**
 * Script d'autocomplétion dynamique pour la recherche de patients.
 *
 * Emplacement : resources/js/patient-autocomplete.js
 * Ce script écoute la saisie de l'utilisateur, effectue une requête AJAX (Fetch)
 * vers le backend Laravel, et affiche les résultats de manière interactive.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Récupération de l'élément de saisie texte de recherche du patient
    const searchInput = document.getElementById('patient_search_input');

    // Récupération du champ caché stockant l'identifiant (ID) du patient sélectionné
    const hiddenPatientId = document.getElementById('patient_id');

    // Récupération du conteneur HTML affichant les résultats de l'autocomplétion
    const resultsContainer = document.getElementById('patient_results_list');

    // Si les éléments requis ne sont pas présents sur la page actuelle, on arrête l'exécution du script
    if (!searchInput || !hiddenPatientId || !resultsContainer) {
        return;
    }

    // Récupération de l'URL de recherche d'autocomplétion depuis l'attribut data-url ou valeur par défaut
    const searchUrl = searchInput.dataset.url || '/patients/search';

    // Déclaration du timer pour la technique du Debounce (limitation de requêtes)
    let debounceTimer = null;

    /**
     * Écouteur d'événement déclenché à chaque frappe dans le champ de recherche
     */
    searchInput.addEventListener('input', function () {
        // Nettoyage et suppression des espaces superflus
        const query = searchInput.value.trim();

        // Réinitialisation de l'ID patient si l'utilisateur modifie la saisie
        hiddenPatientId.value = '';

        // Annulation du minuteur précédent si la frappe continue
        clearTimeout(debounceTimer);

        // Masquage et vidage de la liste si le terme contient moins de 2 caractères
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
            return;
        }

        // Lancement de la recherche après un délai de 300 millisecondes (Debounce)
        debounceTimer = setTimeout(function () {
            // Exécution de la requête HTTP AJAX vers l'API Laravel
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    // Vérification de la réussite de la requête HTTP
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des données patients');
                    }
                    return response.json();
                })
                .then(patients => {
                    // Vidage du conteneur avant d'insérer les nouvelles suggestions
                    resultsContainer.innerHTML = '';

                    // Cas où aucun patient ne correspond aux critères de recherche
                    if (patients.length === 0) {
                        const noResultDiv = document.createElement('div');
                        noResultDiv.className = 'list-group-item text-muted small py-2 px-3';
                        noResultDiv.textContent = 'Aucun patient trouvé avec ce nom.';
                        resultsContainer.appendChild(noResultDiv);
                    } else {
                        // Parcours des patients retournés par le serveur
                        patients.forEach(patient => {
                            // Création du bouton de suggestion pour chaque patient
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3';

                            // Contenu HTML de l'élément avec le nom complet, téléphone et badge assurance
                            const badgeAssurance = patient.statut === 'assure'
                                ? `<span class="badge bg-success-subtle text-success border border-success-subtle small ms-2"><i class="bi bi-shield-check"></i> ${patient.assurance_nom || 'Assuré'} (${patient.taux_couverture || 80}%)</span>`
                                : `<span class="badge bg-light text-muted border small ms-2">Non Assuré</span>`;

                            item.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <span class="fw-semibold">${patient.nom_complet}</span>
                                    ${badgeAssurance}
                                </div>
                                <span class="badge bg-light text-dark border small">${patient.telephone}</span>
                            `;

                            // Action lors du clic sur un patient suggéré
                            item.addEventListener('click', function () {
                                // Affectation du nom complet dans le champ de recherche
                                searchInput.value = patient.nom_complet;
                                // Stockage de l'ID du patient dans le champ masqué
                                hiddenPatientId.value = patient.id;

                                // Notification aux observateurs et formulaires
                                hiddenPatientId.dispatchEvent(new Event('change', { bubbles: true }));

                                // Auto-remplissage de l'assurance et du taux de couverture si présents sur la page
                                const assuranceSelect = document.getElementById('assurance_id');
                                const tauxInput = document.getElementById('taux_assurance');
                                const numeroAssureInput = document.getElementById('numero_assure');

                                if (assuranceSelect) {
                                    if (patient.assurance_id) {
                                        assuranceSelect.value = patient.assurance_id;
                                    } else {
                                        assuranceSelect.value = '';
                                    }
                                    assuranceSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                }

                                if (tauxInput) {
                                    tauxInput.value = patient.statut === 'assure' ? (patient.taux_couverture || 80) : 0;
                                    tauxInput.dispatchEvent(new Event('input', { bubbles: true }));
                                }

                                if (numeroAssureInput && patient.numero_assure) {
                                    numeroAssureInput.value = patient.numero_assure;
                                }

                                // Masquage de la liste de suggestions
                                resultsContainer.innerHTML = '';
                                resultsContainer.style.display = 'none';
                            });

                            // Ajout de l'élément dans la liste des résultats
                            resultsContainer.appendChild(item);
                        });
                    }

                    // Affichage du conteneur de suggestions
                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur autocomplétion patients :', error);
                });
        }, 300);
    });

    /**
     * Fermeture de la liste des résultats lors d'un clic en dehors du composant
     */
    document.addEventListener('click', function (event) {
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    /**
     * Réouverture de la liste des résultats si le champ est à nouveau ciblé
     */
    searchInput.addEventListener('focus', function () {
        if (searchInput.value.trim().length >= 2 && resultsContainer.children.length > 0) {
            resultsContainer.style.display = 'block';
        }
    });
});
