<?php

return [
    'accepted' => 'Le champ :attribute doit etre accepte.',
    'active_url' => 'Le champ :attribute doit etre une URL valide.',
    'array' => 'Le champ :attribute doit etre un tableau.',
    'boolean' => 'Le champ :attribute doit etre vrai ou faux.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute doit etre une date valide.',
    'email' => 'Le champ :attribute doit etre une adresse e-mail valide.',
    'exists' => 'La valeur selectionnee pour :attribute est invalide.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'in' => 'La valeur selectionnee pour :attribute est invalide.',
    'integer' => 'Le champ :attribute doit etre un entier.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max elements.',
        'file' => 'Le fichier :attribute ne doit pas depasser :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas etre superieur a :max.',
        'string' => 'Le champ :attribute ne doit pas depasser :max caracteres.',
    ],
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min elements.',
        'file' => 'Le fichier :attribute doit contenir au moins :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre au moins de :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caracteres.',
    ],
    'numeric' => 'Le champ :attribute doit etre un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une lettre majuscule et une lettre minuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le :attribute fourni apparait dans une fuite de donnees. Veuillez en choisir un autre.',
    ],
    'required' => 'Le champ :attribute est obligatoire.',
    'required_if' => 'Le champ :attribute est obligatoire quand :other vaut :value.',
    'required_with' => 'Le champ :attribute est obligatoire quand :values est present.',
    'same' => 'Le champ :attribute et :other doivent correspondre.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size elements.',
        'file' => 'Le fichier :attribute doit faire :size kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre de :size.',
        'string' => 'Le champ :attribute doit contenir :size caracteres.',
    ],
    'string' => 'Le champ :attribute doit etre une chaine de caracteres.',
    'unique' => 'La valeur du champ :attribute est deja utilisee.',
    'url' => 'Le format du champ :attribute est invalide.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'attributes' => [
        'name' => 'nom',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'token' => 'jeton',
        'code' => 'code',
    ],
];
