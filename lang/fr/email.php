<?php
return array(
    'cron' => array(
        'workflowAction' => array(
            'subject' => 'Vous avez des fichiers en attente de validation',
            'content' => 'Vous devez toujours valider le fichier suivant : ',
            'before' => 'avant le',
            'validInfos' => 'Vous retrouverez les informations de validation dans vos notifications sur Netframe',
        ),
        'boardingUserCanceled' => array(
            'subject' => 'Votre espace de travail Netframe !',
            'content' => 'Vous avez été invité à rejoindre l\'espace de travail <strong>:instanceName</strong> par <strong>:instanceAdmin</strong>, rejoignez le dès maintenant <a href=":boardingLink">ici</a> <br><br>
                 Bonne journée,'
        ),
        'welcomeAdmin' => array(
            'subject' => 'Bienvenue sur Netframe',
            '\'' => array(
                '$welcomeStep' => array(
                    '\'' => array(
                        'content' => 'Bienvenue'
                    )
                )
            ),
            'welcome' => array(
                'content' => 'Bonjour, <br><br>
                        Vous avez ouvert un espace de travail sur Netframe et nous vous en remercions.  Vous pouvez dès à présent personnaliser votre espace de travail en allant dans "Paramètres de l\'espace de travail" dans le menu utilisateur situé en haut à droite en dessous de votre photo de profil.<br><br>
                        Mais aussi :<br>
                        <ul>
                        <li>inviter des utilisateurs à vous rejoindre directement</li>
                        <li>gérer la configuration de votre espace et les droits associés</li>
                        <li>gérer les applications que vous souhaitez activer sur votre plateforme, notamment les fils de discussion et la fonction cartographie</li>
                        <li>paramétrer graphiquement votre espace de travail pour correspondre à votre charte graphique</li>
                        </ul>
                        Bonne installation dans votre espace de travail,'
            ),
            'userManual' => array(
                'content' => 'Bonjour,<br><br>
                        Pour découvrir le fonctionnement de votre espace de travail Netframe vous trouverez ci-joint un mode d\'emploi.<br><br>
                        Si vous avez des questions, n\'hésitez pas à <a href="mailto:contact@netframe.fr">nous contacter</a>.<br><br>
                        Bonne journée!'
            )
        ),
        'paymentsuccess' => array(
            'subject' => 'Paiement d\'une facture',
            'content' => 'Bonjour, <br><br>Votre dernière facture a été payée.<br><br>Vous trouverez ci-joint la facture du prélèvement.<br><br> Bonne journée.'
        ),
        'paymenterror' => array(
            'subject' => 'Erreur de paiement',
            'content' => 'Bonjour, <br><br>Une erreur est survenue lors d\'une tentative de paiement de la dernière facture. <br><br>Cliquez <a href=":instance_subscription">ici</a> pour entrer un moyen de paiement.<br><br> Bonne journée.'
        ),
        'boardingAdminCancel' => array(
            'subject' => 'Votre espace de travail netframe',
            'content' => 'Bonjour,<br><br>Vous avez commencé à créer votre espace de travail Netframe et vous êtes interrompu... c\'est dommage !<br><br>Vous pouvez reprendre à tout moment votre installation chez Netframe ici'
        ),
        'notConnectedAdmin' => array(
            'subject' => 'Votre espace de travail Netframe !',
            'content' => 'Bonjour,<br><br>
                Vous avez ouvert un espace de travail Netframe il y a :nbWeek, et pourtant vous n\'êtes pas revenu nous voir. Vous souhaitez peut être nous poser quelques questions et discuter avec nous pour avancer plus dans la définition de votre projet ?<br><br>
                C\'est très simple, <a href="mailto:contact@netframe.fr">écrivez-nous</a> !<br><br>
                Bonne journée,',
            'nbWeeks' => 'une semaine|deux semaines'
        ),
        'notifMessages' => array(
            'subject' => 'En attente sur netframe',
            'content' => 'Vous avez des informations non lues sur votre espace de travail',
            'contentNotif' => ':nbNotif notification|:nbNotif notifications',
            'contentMessages' => ':nbMsg message|:nbMsg messages',
            'contentChanMessages' => ':nbChanMessages message dans les fils de discussion|:nbChanMessages messages dans les fils de discussion',
            'goOnNetframe' => 'Connectez-vous à votre espace',
            'manageNotifs' => 'Vous pouvez gérer la fréquence des notification sur votre compte netframe :<br> menu principal > Mon compte > Paramètres de notifications'
        ),
        'notConnectedUser' => array(
            'subject' => 'Votre espace de travail Netframe !',
            'content' => 'Bonjour,<br><br>
                  Nous ne vous avons pas vu sur votre espace Netframe :instanceName depuis :nbDays jours. Manque de temps ? Pas envie ? Pas pris l\'habitude ?<br><br>
                  Quelle que soit votre raison, il est temps de s\'y mettre. Connectez vous <a href=":instanceUrl">ici</a>.<br><br>
                  Si vous ne souhaitez pas revenir, n\'hésitez pas à <a href="mailto:contact@netframe.fr">nous écrire</a> pourquoi.<br><br>
                  Bonne journée,'
        ),
        'endtrial' => array(
            'subject' => 'Votre période d\'essai arrive à terme',
            'content' => 'Bonjour, <br><br>Votre période d\'essai arrive à terme dans ... jours, veuillez rentrer un moyen de paiement. <br><br> Une fois la période d\'essai terminée, l\'accès à la plateforme vous sera restreint. Cliquez <a href=":instance_subscription">ici</a> pour entrer un moyen de paiement.<br><br> Bonne journée.'
        ),
        'welcome' => 'Bonjour :userName',
        'signature' => 'L\'équipe netframe',
        'signature2' => 'Valentin<br>Netframe',
        'welcomeSingle' => 'Bonjour',
        'myNetframeAccount' => 'mon compte netframe',
        'becomeBuzz' => array(
            'subject' => 'Bravo, votre profile netframe a été approuvé',
            'txt1' => 'N/A',
            'txt2' => 'N/A',
            'txtLinkHref' => 'Connexion à netframe'
        ),
        'completeProfile' => array(
            'content' => 'Complétez votre profil',
            'goOnNetframe' => 'Complétez votre profil',
            'subject' => 'Complétez votre profil'
        ),
        'lastPostsMedias' => array(
            'lastMedias' => 'dernier contenu',
            'subject' => 'cette semaine sur netframe',
            'txt1' => 'dernière publication',
            'txtLinkHref' => 'dernière publication',
            'lastPosts' => 'dernier contenu'
        ),
        'noNetframeProfile' => array(
            1 => array(
                'subject' => 'ton profil netframe'
            ),
            2 => array(
                'txt1' => 'ici les derniers profils netframe qui on tété créés',
                'txtLinkHref' => 'connectez vous a netframe',
                'subject' => 'ton profil netframe'
            ),
            'content' => 'contenus',
            'goOnNetframe' => 'créez votre profil netframe ici :',
            'subject' => 'votre compte netframe'
        ),
        'noPost' => array(
            1 => array(
                'subject' => 'vos publication sur Netframe',
                'txtLinkHref' => 'rajouter des informations sur netframe'
            ),
            2 => array(
                'txt1' => 'Selon de sérieuses études, la longueur idéale pour une publication sur les réseaux sociaux est comprise entre 40 et 120 caractères. Au-delà, beaucoup de gens ne lisent pas.'
            ),
            'goOnNetframe' => 'Pourquoi ne pas commencer tout de suite en rédigeant ton premier post ici :',
            'subject' => 'Vos posts sur netframe'
        ),
        'noProfile' => array(
            'content' => 'Vous n\'avez pas encore votre profil netframe'
        ),
        'visitors' => array(
            'subject' => 'votre visite sur netframe',
            'content' => 'Vous avez récemment navigué sur <a href="http://netframe.com">netframe.com</a> le réseau social des passionnés et des talents, nous vous en remercions!<br /><br />Au début de notre histoire, chaque visiteur compte beaucoup pour nous, et nous voulions vous le dire. Chaque jour le site évolue, progresse, s\'améliore en tenant compte de nos utilisateurs, alors nous avons besoin de vous !<br /><br />Vous n\'avez pas conclu votre inscription sur netframe, pas convaincu ?, pas intéressé ?, pas le temps ?, allez revenez-nous voir, nous n\'en sommes qu\'au début de notre histoire ensemble, <a href="http://netframe.com/auth/register">venez la conclure ici</a>.<br /><br />'
        ),
        'weeklyInfo' => array(
            'contentTalents' => 'La communauté netframe s’enrichit chaque jour un peu plus ! Voici les derniers profils créés sur netframe, venez les visiter :',
            'contentBuzz' => 'Contenus info de la semaine',
            'txtLinkHref' => 'Bientôt sur netframe'
        )
    ),
    'newAccountent' => array(
        'subject' => 'Netframe :: nouvel accès délégué',
        'welcome' => 'Bonjour ',
        'access' => 'Un accès délégué a été créé pour vous sur netframe : ',
        'toLink' => 'Vous pouvez y accéder directement en cliquant sur le lien suivant : '
    ),
    'visitor' => array(
        'subject' => 'Invitation à rejoindre Netframe',
        'invite' => array(
            'title' => 'vous invite à rejoindre son espace de travail',
            'content1' => 'Afin de rejoindre l\'espace de travail, veuillez cliquer sur le lien ci dessous',
            'join' => 'Rejoindre l\'espace de travail',
            'rememberKey' => 'Si vous ne pouvez pas cliquer sur le lien ci dessus, voici l\'adresse de connexion à copier/coller dans votre navigateur'
        )
    ),
    'resetPassword' => array(
        'subject' => 'netframe : mot de passe oublié',
        'welcome' => 'Bonjour :userName',
        'toLink' => 'Pour réinitialiser votre mot de passe, complétez le formulaire en cliquant sur le lien suivant :',
        'linkExpire' => 'N\'attendez pas, car le lien ne sera plus actif au-delà de :expireTime heures.'
    ),
    'api' => array(
        '\'' => array(
            '$result' => array(
                '\'' => array(
                    'content' => 'contenu'
                )
            )
        ),
        'errorKyc' => array(
            'subject' => 'profil approuvé',
            'content' => 'Les documents d’identité que vous avez transférés n’ont pas été approuvées. Vous pouvez retourner sur votre compte pour télécharger de nouveaux documents'
        ),
        'validKyc' => array(
            'content' => 'Les documents d’identité transférés ont été approuvés',
            'subject' => 'compte approuvé'
        )
    ),
    'signatureTeam' => 'L\'équipe netframe',
    'deleteProfile' => array(
        'subject' => 'Tes profils sur netframe',
        'content' => 'Il semble que des informations que tu as mises en ligne soient les mêmes plusieurs fois, nous allons remédier à cela pour que ton profil netframe, te valorise le mieux possible.'
    )
);
