/**
 * Script d'autocomplétion dynamique pour la recherche de services médicaux.
 *
 * Emplacement : resources/js/service-autocomplete.js
 * Ce script écoute la saisie de l'utilisateur, effectue une requête AJAX (Fetch)
 * vers l'API Laravel des services, et affiche les résultats de manière interactive.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Récupération de l'élément de saisie texte de recherche du service
    const searchInput = document.getElementById('service_search_input');

    // Récupération du champ caché stockant l'identifiant (ID) du service sélectionné
    const hiddenServiceId = document.getElementById('service_id');

    // Récupération du conteneur HTML affichant les résultats de l'autocomplétion
    const resultsContainer = document.getElementById('service_results_list');

    // Si les éléments requis ne sont pas présents sur la page actuelle, on arrête l'exécution du script
    if (!searchInput || !hiddenServiceId || !resultsContainer) {
        return;
    }

    // Récupération de l'URL de recherche d'autocomplétion depuis l'attribut data-url ou valeur par défaut
    const searchUrl = searchInput.dataset.url || '/services/search';

    // Déclaration du timer pour la technique du Debounce (limitation du nombre de requêtes HTTP)
    let debounceTimer = null;

    /**
     * Écouteur d'événement déclenché à chaque frappe dans le champ de recherche
     */
    searchInput.addEventListener('input', function () {
        // Nettoyage et suppression des espaces superflus autour de la recherche
        const query = searchInput.value.trim();

        // Réinitialisation de l'ID service si l'utilisateur modifie la saisie texte
        hiddenServiceId.value = '';

        // Annulation du minuteur précédent si la frappe de l'utilisateur continue
        clearTimeout(debounceTimer);

        // Masquage et vidage de la liste si le terme contient moins de 2 caractères
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
            return;
        }

        // Lancement de la recherche après un délai de 300 millisecondes (Debounce)
        debounceTimer = setTimeout(function () {
            // Exécution de la requête HTTP AJAX vers l'API Laravel de recherche de services
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    // Vérification de la réponse HTTP du serveur
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des services médicaux');
                    }
                    return response.json();
                })
                .then(services => {
                    // Vidage du conteneur avant d'insérer les nouvelles suggestions
                    resultsContainer.innerHTML = '';

                    // Cas où aucun service médical ne correspond au critère saisi
                    if (services.length === 0) {
                        const noResultDiv = document.createElement('div');
                        noResultDiv.className = 'list-group-item text-muted small py-2 px-3';
                        noResultDiv.textContent = 'Aucun service médical trouvé avec ce nom ou ce code.';
                        resultsContainer.appendChild(noResultDiv);
                    } else {
                        // Parcours des services retournés par la recherche SQL
                        services.forEach(service => {
                            // Création du bouton de suggestion interactive pour chaque service
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3';

                            // Contenu HTML affichant le nom du service et son code identifiant
                            item.innerHTML = `
                                <div>
                                    <span class="fw-semibold">${service.nom}</span>
                                </div>
                                <span class="badge bg-secondary small">${service.code}</span>
                            `;

                            // Action exécutée lors du clic sur un service suggéré
                            item.addEventListener('click', function () {
                                // Affectation du nom et code du service dans le champ texte de recherche
                                searchInput.value = `${service.nom} (${service.code})`;
                                // Enregistrement de l'identifiant ID du service dans le champ masqué du formulaire
                                hiddenServiceId.value = service.id;
                                // Masquage de la liste déroulante des résultats
                                resultsContainer.innerHTML = '';
                                resultsContainer.style.display = 'none';
                            });

                            // Ajout de l'élément dans le conteneur de résultats
                            resultsContainer.appendChild(item);
                        });
                    }

                    // Affichage du bloc des suggestions
                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur autocomplétion services :', error);
                });
        }, 300);
    });

    /**
     * Masquage de la liste de suggestions lors d'un clic à l'extérieur du composant
     */
    document.addEventListener('click', function (event) {
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    /**
     * Réaffichage de la liste si le champ est à nouveau ciblé et contient une recherche
     */
    searchInput.addEventListener('focus', function () {
        if (searchInput.value.trim().length >= 2 && resultsContainer.children.length > 0) {
            resultsContainer.style.display = 'block';
        }
    });
});
