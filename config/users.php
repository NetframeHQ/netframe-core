<?php
/**
 *
 * TLENTER config file
 * config file for profile user
 *
 */
return array(

    'notificationsDevices' => [
        'mail' => 'days',
        'browser' => 'days',
        //'fcm' => 'otf',
    ],

    'defaultMailPlanning' => [
        '2',
        '4',
    ],

    // profile for navigation array in session
    'session' => array(
        'profiles' => array(
            'default' => array(),
            'current' => array(),
            'as' => array()
        )
    ),

    'documents' => array(
        'mangopay' => array(
            'types' => array(  //type, allowed
                'IDENTITY_PROOF' => 1,
                'REGISTRATION_PROOF' => 0,
                'ARTICLES_OF_ASSOCIATION' => 0,
                'SHAREHOLDER_DECLARATION' => 0,
                'ADDRESS_PROOF' => 0,
            ),
            'status' => array(   //type, default
                'CREATED' => 1,
                'VALIDATION_ASKED' => 0,
                'VALIDATED' => 0,
                'REFUSED' => 0,
            ),
            'accepted_mime' => array(
                'image/jpeg',
                'image/pjpeg',
                'image/jpeg',
                'image/pjpeg',
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',
            ),
        ),
    ),

);
