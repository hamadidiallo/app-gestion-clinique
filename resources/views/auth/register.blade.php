@include('auth.template', [
    'defaultScreen' => ($activeTab === 'Rejoindre' ? 'M0c' : ($activeTab === 'Clinique' ? 'M0b' : ($activeTab === 'Connexion' ? 'M1' : 'M0'))),
    'defaultTab' => $activeTab ?? 'Bienvenue'
])
