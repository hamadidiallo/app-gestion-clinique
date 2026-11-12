<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
 * Un nom de route mal orthographié dans une vue ne casse la page qu'à l'affichage,
 * et seulement si la ligne concernée est rendue (ex. une prestation liée à un patient).
 * Ce test parcourt tout le code pour détecter ces erreurs avant qu'un utilisateur ne les voie.
 */
it('n\'utilise que des noms de routes qui existent', function () {
    $fichiers = collect(File::allFiles(resource_path('views')))
        ->merge(File::allFiles(app_path()))
        ->filter(fn ($fichier) => str_ends_with($fichier->getFilename(), '.php'));

    $inconnus = [];

    foreach ($fichiers as $fichier) {
        // route('nom'…), to_route('nom'…) et ->route('nom'…) sur une redirection.
        // $this->route('param') dans les Requests lit un paramètre d'URL : il est exclu.
        preg_match_all(
            "/(?<![\\w>])(?:route|to_route)\\(\\s*'([A-Za-z0-9_.-]+)'|redirect\\(\\)->route\\(\\s*'([A-Za-z0-9_.-]+)'/",
            $fichier->getContents(),
            $correspondances,
            PREG_SET_ORDER
        );

        foreach ($correspondances as $correspondance) {
            $nom = $correspondance[2] ?? '' ?: $correspondance[1];

            if (! Route::has($nom)) {
                $inconnus[] = $nom.' dans '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $fichier->getPathname());
            }
        }
    }

    expect(array_unique($inconnus))->toBe([]);
});
