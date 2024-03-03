<?php
return array(
    'instanceClosed' => "Votre espace de travail est arrivé à expiration,<br>il est désormais fermé.<br><br><a href='https://www.netframe.co'>netframe.co</a>",
    'parameters' => 'Gestion de l\'espace de travail',

    'menu' => array(
        'subscription' => 'Formule d\'abonnement',
        'boarding' => 'Informations générales',
        'rights' => 'Gestion des droits',
        'invite' => 'Inviter des utilisateurs',
        'users' => 'Utilisateurs',
        'visitors' => 'Invités',
        'projects' => 'Projets',
        'communities' => 'Groupes',
        'houses' => 'Entités',
        'autoSubscribe' => 'Abonnements utilisateurs',
        'apps' => 'Applications',
        'graphical' => 'Personnalisation graphique',
        'translations' => 'Traductions',
        'usersdata' => 'Données utilisateurs',
        'create' => 'Créer des utilisateurs',
        'groups' => 'Groupes d\'utilisateurs',
        'stats' => 'Statistiques',
    ),

    'apps' => array(
        'title' => 'Applications',
        'submit' => 'Valider les applications'
    ),

    'autoSubscribe' => array(
        'title' => 'Abonnements utilisateurs',
        'intro' => 'Ces pages vous permettent de determiner à quels profils vos utilisateurs vont être automatiquement abonnés. Cette fonctionnalité n\'est disponible que pour les profils publics.',
        'introProfile' => 'Seuls les profils publics apparaissent dans la liste suivante.',
        'updatedDone' => 'Mise à jour effectuée',
        'submit' => 'Valider',
    ),

    'boarding' => array(
        'title' => 'Processus d\'accueil',
        'publicUrlTitle' => 'URL d\'accès à votre espace de travail',
        'publicUrl' => 'Lien public d\'accès à votre espace de travail :',
        'publicKeyTitle' => 'Clé publique d\'enregistrement',
        'publicKey' => 'Clé publique d\'enregistrement de votre espace de travail:',
        'publicKeyLink' => 'Lien d\'enregistrement que vous pouvez communiquer à vos collaborateurs afin qu\'ils rejoignent cet espace de travail :',
        'publicKeyLinkCopy' => 'Copier le lien',
        'keyRegenerate' => 'Regénérer la clé',
        'createWithKeyDisable' => 'Désactiver le lien',
        'createWithKeyEnable' => 'Activer le lien',
        'validateUsersTitle' => 'Approbation des nouveaux utilisateurs',
        'validateUsersOn' => 'Vous devez approuver les nouveaux comptes utilisateur',
        'validateUsersOff' => 'Les nouveaux comptes créés sont approuvés automatiquement',
        'validateUserSwitchOn' => 'Changer pour une approbation manuelle',
        'validateUserSwitchOff' => 'Changer pour une approbation automatique',
        'result' => array(
            'newKeyGenerated' => 'la nouvelle clé a bien été générée'
        ),
        'consentCharter' => [
            'title' => 'Ajouter votre charte interne',
            'state' => 'Activer la charte interne',
            'content' => 'Contenu de la charte',
        ],
    ),
    'rights' => array(
        'title' => 'Gestion des droits',
        'god' => array(
            'title' => 'Pleins pouvoirs',
            'intro' => 'Cette fonction vous permet d\'avoir les droits de gestion sur tous les profils créés sur l\'espace de travail',
            'becomeGod' => 'Obtenir les pleins pouvoirs',
            'godPassword' => 'Merci de saisir votre mot de passe',
            'youAreGod' => 'Vous avez les pleins pouvoirs',
            'disableGod' => 'Désactiver les pleins pouvoirs'
        ),
        'authProfiles' => array(
            'title' => 'Autorisation à la création de profils',
            'intro' => 'Ce tableau vous permet de déterminer quels types de profils peuvent être créés à partir d\'autres profils :',
            'submit' => 'Enregistrer les autorisations'
        ),
        'banPostTimeline' => 'Empecher les utilisateurs de poster depuis la page d\'accueil',
    ),
    'subscription' => array(
        'title' => 'Formule d\'abonnement',
        'billing' => array(
            'free' => array(
                'intro' => 'Vous disposez de la formule Découverte',
                'maxUsers' => 'Limite utilisateurs',
                'maxStorage' => 'Limite espace de stockage',
                'maxDate' => 'La date de fin d\'essai est le : ',
                'increaseLimit' => 'Pour continuer d\'utiliser Netfame après cette date, vous pouvez saisir une carte bancaire ci dessous',
            ),
            'normal' => array(
                'intro' => 'Vous disposez de la formule Premium',
                'users' => 'Nombre d\'utilisateurs inscrits',
                'maxStorageUser' => 'Limite espace de stockage par utilisateur'
            ),
            'custom' => array(
                'intro' => 'Vous disposez de la formule Sur-Mesure'
            ),
            'forever' => array(
                'intro' => 'Vous disposez de la formule Sur-Mesure'
            ),
        ),
        'quotaUnit' => 'Go',
        "delegate_access" => [
            "title" => "Accès délégué",
            "text" => "L'accès délégué vous permet à des personnes non membres de votre instance de consulter sur une interface spécifique les facture et de modifier les moyens de paiement.",
            "header" => [
                "email" => "Adresse email",
            ],
            "delete" => "Supprimer",
            "connect" => "Connexion à l'espace délégué",
        ],

        "pay" => [
            "title" => "Paiement de la facture",
        ],

        "paymentinfos" =>[
            "title" => "Informations de contact",
        ],
    ),
    'invite' => array(
        'title' => 'Inviter des utilisateurs',
        'userLimitReach' => 'Vous avez atteint la limite d\'utilisateur sur votre espace de travail',
        'intro' => 'Ou saisissez plusieurs adresses email afin de leur envoyer des invitations ci-dessous :',
        'emailAddress' => 'Adresse email',
        'send' => 'Envoyer les invitations',
        'notSended' => 'Les invitations suivantes n\'ont pas été envoyées (utilisateurs déjà existants)',
        'sended' => 'Les invitations suivantes ont bien été envoyées'
    ),
    'profiles' => array(
        'titles' => array(
            'communities' => 'Groupes',
            'houses' => 'Entités',
            'projects' => 'Projets',
            'users' => 'Utilisateurs'
        ),
        'manage-user' => 'Gestion des droits',
        'manage' => 'Gérer les membres',
        'createdAt' => 'Créé le',
        'disable' => 'Désactiver',
        'disabled' => 'Désactivé',
        'enable' => 'Activer',
        'add' => 'Ajouter',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'addVisitor' => 'Ajouter un invité',
        'email' => 'Adresse email',
        'firstname' => 'Prénom',
        'lastname' => 'Nom',
        'visitor' => 'Invité',
        'create-communities' => 'Créer un groupe',
        'success-communities' => 'Groupe créé avec succès',
        'create-projects' => 'Créer un projet',
        'success-projects' => 'Projet créé avec succès',
        'create-houses' => 'Créer une entitée',
        'success-houses' => 'Entité créée avec succès',
        'change' => array(
            'edit' => 'Modifier',
        ),
        'manageVirtualUser' => 'Gérer les accès additionnels',
        'rights' => array(
            'title' => 'Gérer les droits',
            'houses' => 'Entités',
            'projects' => 'Projets',
            'communities' => 'Groupes',
            'submit' => 'Valider',
        ),
    ),
    'manage' => [
        'title' => 'Gestion des droits',
        'chooseRole' => 'Choisissez un rôle',
        'member' => 'Membre',
        'members' => 'Membres',
        'searchUsers' => 'Rechercher des utilisateurs...',
        'addMembers' => 'Ajouter des membres',
        'user' => 'Utilisateur',
        'type' => 'Type',
        'add' => 'Ajouter',
        'add-member' => 'Ajouter un membre',
        'titles' => array(
            'houses' => 'Entités',
            'projects' => 'Projets',
            'communities' => 'Groupes',
            'channels' => 'Fils de discussion'
        ),
    ],
    'edit' => [
        'title' => 'Modifier',
        'success' => 'Modification effectuée avec succès!',
    ],
    'visitors' => array(
        'title' => 'Invités',
        'rights' => 'Droits',
        'name' => 'Nom',
        'choice' => 'Choix',
    ),
    'graphical' => array(
        'title' => 'Personnalisez votre interface',
        'titleLogos' => 'Logos',
        'titleLight' => 'Thème clair',
        'titleDark' => 'Thème sombre',
        'themesTxt' => 'Choisissez un thème prêt à l\'emploi OU choisissez vos couleurs ci-dessous',
        'menuLogo' => 'Logo menu',
        'mainLogo' => 'Logo connexion',
        'mainLogoBackground' => 'Couleur de fond du logo',
        'backgroundColor' => 'Couleur de fond',
        'navTheme' => [
            'title' => 'Thème navigation',
            'dark' => 'Sombre',
            'light' => 'Clair',
        ],
        'themes' => [
            'title' => 'Thèmes',
            'groups' => [
                'standards' => 'Thèmes standards',
                'colored' => 'Thèmes colorés',
            ],
            'themes' => [
                'standard' => 'Netframe',
                'pink' => 'Pink',
                'blue' => 'Bleu',
            ],
        ],
        'actionColor' => 'Couleur d\'action',
        'validTheme' => 'Valider le thème',
        'validBgLogo' => 'Enregistrer les couleurs',
        'titleColors' => 'Couleurs internes',
        'introColors' => 'Choisissez vos couleurs, visualisez l\'aperçu et validez les changement',
        'principalColor' => 'Couleur principale',
        'principalTextColor' => 'Couleur des textes',
        'secondaryColor' => 'Couleur d\'action',
        'colorLinks' => 'Couleur des liens',
        'valid' => 'Valider les changements',
        'titleBackground' => 'Fonds d\'écrans',
        'bgScreen' => 'Fond d\'écran de connexion',
        'addBackgroundImage' => 'Ajouter une image de fond',
        'yes' => 'Oui',
        'no' => 'Non',
        'likeButton' => 'Boutons "J\'aime"',
        'reactions' => 'Ajouter 5 réactions dans l\'ordre',
        'buttonsSize' => 'Veuillez entrer exactement 5 réactions',
        'resetColors' => 'Ré-initialiser les couleurs par défaut',
        'testLightMode' => 'Prévisualiser le mode clair',
        'testDarkMode' => 'Prévisualiser le mode sombre',
        'disableMode' => 'Désactiver le thème clair',
        'disableModeDark' => 'Désactiver le thème sombre',
    ),
    'usersdata' => array(
        'title' => 'Données utilisateurs',
        'dataLabel' => 'Label de la donnée',
        'dataType' => 'Type de donnée',
        'name' => 'Nom',
        'type' => 'Type',
        'add' => 'Ajouter',
        'save' => 'Enregistrer',
        'delete' => 'Supprimer',
        'deletePermanently' => 'Supprimer définitivement',
        'restore' => 'Restaurer',
        'success' => 'Données utilisateurs enregistrées',
        'inputType' => array(
            'input' => 'Text court',
            'textarea' => 'Texte long',
            'checkbox' => 'Case à cocher'
        ),
    ),
    'create' => array(
        'title' => 'Créer des utilisateurs',
        'create' => 'Créer',
        'importFrom' => 'Importer depuis un fichier CSV',
        'import' => 'Importer',
        'file' => 'Fichier',
        'name' => 'Nom',
        'firstname' => 'Prénom',
        'email' => 'Adresse email',
        'success' => 'Utilisateur créé avec succès',
        'file-success' => 'Utilisateurs importés avec succès',
        'file-error' => 'Les utilisateurs listés n\'ont pas été importés car comportant des erreurs ou ont déjà un compte associé',
        'description' => 'Importer un fichier contenant impérativement les colonnes suivantes ainsi que leurs titres: nom, prénom, email.',
        'cols' => [
            'name'=>'Nom',
            'firstname'=>'Prenom',
            'email'=>'Email'
        ],
        'error' => [
            'title' => 'Les colonnes suivantes n\'ont pas été trouvés'
        ]
    ),
    'groups' => [
        'name' => 'Nom du groupe',
        'title' => 'Créer des groupes',
        'owner' => 'Propriétaire',
        'success' => 'Groupe créé avec succès',
        'addButton' => 'Ajouter groupe'
    ],
    'virtualUsers' => [
        'title' => 'Accès additionnels pour',
        'explain' => 'Vous pouvez ajouter des logins virtuels pour vos utilisateurs afin de permettre l\'accès à un même compte avec plusieurs nom d\'utilisateurs et mots de passe',
        'deleteConfirm' => 'Etes vous sûr de vouloir supprimer ce login additionnel ?',
        'create' => 'Créer un accès additionnel pour',
        'active' => 'Activer l\'accès',
    ],
);