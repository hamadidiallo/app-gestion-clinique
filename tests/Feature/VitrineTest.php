<?php

it('affiche la vitrine à un visiteur non connecté, avec les tarifs de la configuration', function () {
    $this->get('/')
        ->assertOk()
        ->assertViewIs('vitrine.index')
        ->assertSee('Créer ma clinique')
        ->assertSee('35 000')
        ->assertSee('75 000')
        ->assertSee('150 000')
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false);
});

it('envoie un utilisateur connecté directement sur son tableau de bord', function () {
    connecterAdmin();

    $this->get('/')->assertRedirect(route('dashboard'));
});

it('sert le tableau de bord sur /dashboard', function () {
    connecterAdmin();

    $this->get('/dashboard')->assertOk();
});

it('masque les canaux de contact non configurés', function () {
    config(['medigest.contact' => ['whatsapp' => null, 'telephone' => null, 'email' => null]]);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('wa.me', false)
        ->assertDontSee('mailto:', false)
        ->assertDontSee('Demander une démonstration');
});

it('affiche les canaux de contact configurés', function () {
    config(['medigest.contact' => [
        'whatsapp' => '+223 70 00 00 00',
        'telephone' => '+223 20 00 00 00',
        'email' => 'contact@medigest.test',
    ]]);

    $this->get('/')
        ->assertOk()
        ->assertSee('https://wa.me/22370000000', false)
        ->assertSee('tel:+22320000000', false)
        ->assertSee('mailto:contact@medigest.test', false);
});

it('affiche sur la page Abonnement les mêmes formules que la vitrine', function () {
    connecterAdmin();

    $this->get(route('abonnement.index'))
        ->assertOk()
        ->assertViewHas('formules', config('medigest.formules'));
});
