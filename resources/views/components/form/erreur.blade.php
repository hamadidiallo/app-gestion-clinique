{{-- Composant réutilisable pour l'affichage de la liste des erreurs de validation --}}
@if ($errors->any())
    <div class="alert alert-danger my-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

