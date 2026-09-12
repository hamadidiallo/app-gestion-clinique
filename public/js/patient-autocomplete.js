/**
 * Script d'autocomplétion dynamique pour la recherche de patients.
 *
 * Emplacement : public/js/patient-autocomplete.js
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

                            // Contenu HTML de l'élément avec le nom complet, le téléphone et l'assurance
                            const badgeCarte = patient.carte_reference ? `<span class="badge bg-info-subtle text-info border border-info-subtle ms-1">Carte: ${patient.carte_reference}</span>` : '';
                            const badgeAssurance = patient.assurance_nom ? `<span class="badge bg-success-subtle text-success border border-success-subtle me-1">${patient.assurance_nom} (${patient.taux_couverture}%)</span>` : '';
                            item.innerHTML = `
                                <div>
                                    <span class="fw-semibold">${patient.nom_complet}</span> ${badgeCarte}
                                    <small class="d-block text-muted">${badgeAssurance}</small>
                                </div>
                                <span class="badge bg-light text-dark border small">${patient.telephone}</span>
                            `;

                            // Action lors du clic sur un patient suggéré
                            item.addEventListener('click', function () {
                                // Affectation du nom complet dans le champ de recherche
                                searchInput.value = patient.nom_complet;
                                // Stockage de l'ID du patient dans le champ masqué
                                hiddenPatientId.value = patient.id;

                                // Si le champ de référence du ticket est présent
                                const inputReferenceTicket = document.getElementById('reference');
                                if (inputReferenceTicket) {
                                    if (patient.statut === 'assure' && patient.carte_reference) {
                                        inputReferenceTicket.value = patient.carte_reference;
                                    } else if (window.defaultTicketReference) {
                                        inputReferenceTicket.value = window.defaultTicketReference;
                                    }
                                }

                                // Si un champ assurance_id est présent sur la page
                                const hiddenAssuranceId = document.getElementById('assurance_id');
                                if (hiddenAssuranceId && patient.assurance_id) {
                                    hiddenAssuranceId.value = patient.assurance_id;
                                }

                                // Si un champ taux_assurance est présent sur la page
                                const inputTauxAssurance = document.getElementById('taux_assurance');
                                if (inputTauxAssurance && patient.taux_couverture !== undefined) {
                                    inputTauxAssurance.value = patient.taux_couverture;
                                    // Déclencher le recalcul de la ventilation
                                    inputTauxAssurance.dispatchEvent(new Event('input'));
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
