/**
 * Script d'autocomplétion dynamique pour la recherche de patients et création rapide.
 *
 * Emplacement : resources/js/patient-autocomplete.js
 */

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

window.selectPatientInForm = function (patient) {
    if (!patient) return;
    const searchInput = document.getElementById('patient_search_input');
    const hiddenPatientId = document.getElementById('patient_id');

    const fullName = patient.nom_complet || `${patient.prenom || ''} ${patient.nom || ''}`.trim();
    if (searchInput) {
        searchInput.value = fullName;
    }
    if (hiddenPatientId) {
        hiddenPatientId.value = patient.id;
    }

    const inputReferenceTicket = document.getElementById('reference');
    if (inputReferenceTicket) {
        if (patient.statut === 'assure' && patient.carte_reference) {
            inputReferenceTicket.value = patient.carte_reference;
        } else if (window.defaultTicketReference) {
            inputReferenceTicket.value = window.defaultTicketReference;
        }
    }

    const hiddenAssuranceId = document.getElementById('assurance_id');
    if (hiddenAssuranceId) {
        hiddenAssuranceId.value = patient.assurance_id || '';
    }

    const inputTauxAssurance = document.getElementById('taux_assurance');
    if (inputTauxAssurance) {
        inputTauxAssurance.value = patient.taux_couverture !== undefined ? patient.taux_couverture : 0;
        inputTauxAssurance.dispatchEvent(new Event('input'));
    }

    const badgeAssure = document.getElementById('ticket_patient_assure_badge');
    const textNumAssure = document.getElementById('ticket_patient_assure_num');
    const numAssure = patient.numero_assure || patient.carte_reference;
    if (badgeAssure && textNumAssure) {
        if (patient.statut === 'assure' && numAssure) {
            textNumAssure.textContent = numAssure;
            badgeAssure.classList.remove('d-none');
        } else {
            badgeAssure.classList.add('d-none');
        }
    }
};

window.openQuickPatientModal = function (query = '') {
    const modalEl = document.getElementById('quickPatientModal');
    if (!modalEl) return;

    const trimmed = (query || '').trim();
    const inputPrenom = document.getElementById('qp_prenom');
    const inputNom = document.getElementById('qp_nom');
    const inputTel = document.getElementById('qp_telephone');

    if (trimmed) {
        if (/^[0-9+\s()-]+$/.test(trimmed)) {
            if (inputTel) inputTel.value = trimmed;
        } else {
            const parts = trimmed.split(/\s+/);
            if (parts.length > 1) {
                if (inputPrenom) inputPrenom.value = parts[0];
                if (inputNom) inputNom.value = parts.slice(1).join(' ');
            } else {
                if (inputNom) inputNom.value = trimmed;
                if (inputPrenom && !inputPrenom.value) inputPrenom.value = '';
            }
        }
    }

    if (window.bootstrap && bootstrap.Modal) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('patient_search_input');
    const hiddenPatientId = document.getElementById('patient_id');
    const resultsContainer = document.getElementById('patient_results_list');

    if (!searchInput || !hiddenPatientId || !resultsContainer) {
        return;
    }

    const searchUrl = searchInput.dataset.url || '/patients/search';
    let debounceTimer = null;

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();
        hiddenPatientId.value = '';
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(function () {
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des données patients');
                    }
                    return response.json();
                })
                .then(patients => {
                    resultsContainer.innerHTML = '';

                    if (patients.length === 0) {
                        const noResultDiv = document.createElement('div');
                        noResultDiv.className = 'list-group-item py-2 px-3 bg-light border-0';
                        noResultDiv.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="text-muted small">Aucun patient trouvé pour "<strong>${escapeHtml(query)}</strong>"</span>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 btn-trigger-quick-patient fw-semibold shadow-sm" style="background: #0f766e; border-color: #0f766e;">
                                    + Créer ce patient
                                </button>
                            </div>
                        `;
                        const btnQuick = noResultDiv.querySelector('.btn-trigger-quick-patient');
                        if (btnQuick) {
                            btnQuick.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                resultsContainer.innerHTML = '';
                                resultsContainer.style.display = 'none';
                                window.openQuickPatientModal(query);
                            });
                        }
                        resultsContainer.appendChild(noResultDiv);
                    } else {
                        patients.forEach(patient => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3';

                            const badgeCarte = patient.carte_reference ? `<span class="badge bg-info-subtle text-info border border-info-subtle ms-1">Carte: ${escapeHtml(patient.carte_reference)}</span>` : '';
                            const badgeAssurance = patient.assurance_nom ? `<span class="badge bg-success-subtle text-success border border-success-subtle me-1">${escapeHtml(patient.assurance_nom)} (${patient.taux_couverture}%)</span>` : '';
                            item.innerHTML = `
                                <div>
                                    <span class="fw-semibold">${escapeHtml(patient.nom_complet)}</span> ${badgeCarte}
                                    <small class="d-block text-muted">${badgeAssurance}</small>
                                </div>
                                <span class="badge bg-light text-dark border small">${escapeHtml(patient.telephone)}</span>
                            `;

                            item.addEventListener('click', function () {
                                window.selectPatientInForm(patient);
                                resultsContainer.innerHTML = '';
                                resultsContainer.style.display = 'none';
                            });

                            resultsContainer.appendChild(item);
                        });

                        // Bouton d'ajout rapide également en bas des résultats trouvés
                        const quickCreateDiv = document.createElement('button');
                        quickCreateDiv.type = 'button';
                        quickCreateDiv.className = 'list-group-item list-group-item-action py-2 px-3 bg-light text-teal fw-semibold border-top d-flex align-items-center justify-content-between';
                        quickCreateDiv.innerHTML = `
                            <span class="small text-muted">+ Patient non listé ? Enregistrer "<strong>${escapeHtml(query)}</strong>"</span>
                            <span class="badge rounded-pill text-white" style="background:#0f766e;">+ Nouveau</span>
                        `;
                        quickCreateDiv.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            resultsContainer.innerHTML = '';
                            resultsContainer.style.display = 'none';
                            window.openQuickPatientModal(query);
                        });
                        resultsContainer.appendChild(quickCreateDiv);
                    }

                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur autocomplétion patients :', error);
                });
        }, 300);
    });

    document.addEventListener('click', function (event) {
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.style.display = 'none';
        }
    });

    searchInput.addEventListener('focus', function () {
        if (searchInput.value.trim().length >= 2 && resultsContainer.children.length > 0) {
            resultsContainer.style.display = 'block';
        }
    });
});
