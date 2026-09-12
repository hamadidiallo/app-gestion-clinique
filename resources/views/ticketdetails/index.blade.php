@extends('layout')

@section('title', 'Lignes de Tickets')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Détails des Prestations par Ticket</h1>
            <a href="{{ route('ticketdetails.create') }}" class="btn btn-primary">Ajouter une ligne</a>
        </div>

        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Ticket</th>
                    <th scope="col">Prestation</th>
                    <th scope="col">Quantité</th>
                    <th scope="col">Prix Unitaire</th>
                    <th scope="col">Total Ligne</th>
                    <th scope="col">Charge Patient</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticketDetails as $detail)
                    <tr>
                        <th scope="row">{{ $detail->id }}</th>
                        <td><code>{{ $detail->ticket ? $detail->ticket->reference : 'N/A' }}</code></td>
                        <td><strong>{{ $detail->prestation ? $detail->prestation->nom : 'N/A' }}</strong></td>
                        <td>{{ $detail->quantite }}</td>
                        <td>{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} FCFA</td>
                        <td>{{ number_format($detail->montant_total, 2, ',', ' ') }} FCFA</td>
                        <td>{{ number_format($detail->montant_patient, 2, ',', ' ') }} FCFA</td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('ticketdetails.show', $detail) }}" class="btn btn-sm btn-info text-white">Voir</a>
                                <a href="{{ route('ticketdetails.edit', $detail) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDetailModal{{ $detail->id }}">Supprimer</button>
                            </div>

                            <div class="modal fade" id="deleteDetailModal{{ $detail->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmation de suppression</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Êtes-vous sûr de vouloir retirer cette ligne du ticket ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form action="{{ route('ticketdetails.destroy', $detail) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucune ligne de détail enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
