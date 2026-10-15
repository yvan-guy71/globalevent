<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Types d'évènements
    |--------------------------------------------------------------------------
    | Chaque type possède un libellé, une icône FontAwesome, une couleur
    | d'accent (palette Tailwind) et une image utilisée sur la page d'accueil.
    */
    'types' => [
        'concert' => [
            'label' => 'Concert',
            'icon' => 'fa-music',
            'color' => 'indigo',
            'image' => 'images/categories/DJ.jpg',
            'description' => 'Concerts, lives et tournées musicales.',
        ],
        'festival' => [
            'label' => 'Festival',
            'icon' => 'fa-tents',
            'color' => 'violet',
            'image' => 'images/categories/deco.png',
            'description' => 'Festivals et grands rassemblements.',
        ],
        'competition' => [
            'label' => 'Compétition',
            'icon' => 'fa-trophy',
            'color' => 'amber',
            'image' => 'images/categories/organe.png',
            'description' => 'Tournois, championnats et concours.',
        ],
        'sport' => [
            'label' => 'Sport',
            'icon' => 'fa-futbol',
            'color' => 'emerald',
            'image' => 'images/categories/sport.jpg',
            'description' => 'Matchs, courses et rencontres sportives.',
        ],
        'conference' => [
            'label' => 'Conférence',
            'icon' => 'fa-microphone-lines',
            'color' => 'blue',
            'image' => 'images/categories/photo.jpg',
            'description' => 'Conférences, séminaires et ateliers.',
        ],
        'spectacle' => [
            'label' => 'Spectacle',
            'icon' => 'fa-masks-theater',
            'color' => 'pink',
            'image' => 'images/categories/spectacle.jpg',
            'description' => 'Théâtre, danse et arts vivants.',
        ],
        'exposition' => [
            'label' => 'Exposition',
            'icon' => 'fa-palette',
            'color' => 'orange',
            'image' => 'images/categories/exposition.jfif',
            'description' => 'Expositions, salons et foires.',
        ],
        'soiree' => [
            'label' => 'Soirée',
            'icon' => 'fa-champagne-glasses',
            'color' => 'rose',
            'image' => 'images/categories/location.webp',
            'description' => 'Soirées privées, gala et afterworks.',
        ],
        'formation' => [
            'label' => 'Formation',
            'icon' => 'fa-graduation-cap',
            'color' => 'cyan',
            'image' => 'images/categories/formation.jfif',
            'description' => 'Formations, masterclass et bootcamps.',
        ],
        'autre' => [
            'label' => 'Autre',
            'icon' => 'fa-calendar-star',
            'color' => 'slate',
            'image' => 'images/categories/fetes.jfif',
            'description' => 'Tout autre type d\'évènement.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Villes proposées (Togo + international)
    |--------------------------------------------------------------------------
    */
    'cities' => [
        'Lomé',
        'Tsévié',
        'Aného',
        'Kpalimé',
        'Atakpamé',
        'Sokodé',
        'Kara',
        'Dapaong',
        'Bassar',
        'Autre',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pays proposés (Togo + international)
    |--------------------------------------------------------------------------
    */
    'countries' => [
        'Togo',
        'Bénin',
        'Ghana',
        'Côte d\'Ivoire',
        'Burkina Faso',
        'Nigeria',
        'Sénégal',
        'Mali',
        'Niger',
        'Cameroun',
        'France',
        'Autre',
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuts
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'published' => 'Publié',
        'draft' => 'Brouillon',
        'cancelled' => 'Annulé',
    ],

    /*
    |--------------------------------------------------------------------------
    | Devises
    |--------------------------------------------------------------------------
    */
    'currencies' => [
        'FCFA' => 'FCFA',
        'EUR' => 'Euro (€)',
        'USD' => 'Dollar ($)',
    ],
];
