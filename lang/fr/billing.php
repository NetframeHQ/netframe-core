<?php

return array (
  'overQuota' => 
  array (
    'title' => 'Quota atteint',
    'unit' => 'Go',
    'getMore' => 
    array (
      '\'' => 
      array (
        'session(\'instanceOffer' => 'dépassement de la limite de votre session
obtenir plus d\'info',
      ),
      'user' => 'Rapprochez vous du propriétaire de l\'espace de travail afin de faire augmenter les limites.',
      'free' => 
      array (
        'text' => 'Vous pouvez augmenter les limites en vous rendant dans les paramètres de votre espace de travail',
      ),
      'normal' => 
      array (
        'text' => 'Contactez-nous pour augmenter les limites de votre espace de travail',
      ),
    ),
    'user' => 'Vous avez atteint le quota autorisé par utilisateur',
    'instance' => 'Vous avez atteint le quota autorisé sur votre espace de travail',
  ),
  'paymentStatus' => 
  array (
    0 => 'Votre période d\'essai est terminée, merci de saisir un moyen de paiement afin de poursuivre l\'utilisation de la plateforme',
    2 => 'Vous vous êtes désabonné de netframe, vous pouvez réactiver votre abonnement ci-dessous',
    3 => 'Attention il y a eu une erreur de paiement, les informations de la carte ne sont plus à jour, veuillez entrer une nouvelle carte. L\'accès à la plateforme sera restreint dans ...  jours',
    4 => 'Période dépassée, l\'accès est restreint pour tous les utilisateurs.',
  ),
);
