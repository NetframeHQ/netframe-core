<?php
return array(
    'signatureTeam' => 'netframe team',
    'resetPassword' => array(
        'subject' => 'netframe :: forgotten password',
        'welcome' => 'Hello :userName',
        'toLink' => 'To reset your password, complete the form by clicking on this link:',
        'linkExpire' => 'This link will expire in :expireTime hours, so don\'t wait to do it.'
    ),
    'deleteProfile' => array(
        'subject' => 'Your netframe profiles',
        'content' => 'It seems that the informations you completed on your netframe profile are pretty much the same several times. Putting together all contents in one profile should enable netframe users to better appreciate who you are - Let netframe team do that for you .'
    ),
    'cron' => array(
        'workflowAction' => array(
            'subject' => 'You have file waiting validation',
            'content' => 'You must always validate the following file : ',
            'before' => 'before',
            'validInfos' => 'You will find the validation information in your notifications on Netframe',
        ),
        'welcome' => 'Hi :userName',
        'welcomeSingle' => 'Hello',
        'myNetframeAccount' => 'my netframe account',
        'signature' => 'netframe team',
        'signature2' => 'Valentin<br>Netframe',
        'notifMessages' => array(
            'subject' => 'Waiting on netframe',
            'content' => 'You\'ve got unread information on your netframe account',
            'contentNotif' => ':nbNotif notification|:nbNotif notifications',
            'contentMessages' => ':nbMsg message|:nbMsg messages',
            'contentChanMessages' => ':nbChanMessages message in channels|:nbChanMessages messages in channels',
            'goOnNetframe' => 'See you soon on netframe.',
            'manageNotifs' => 'You can manage your notifications frequency on you netframe account. - menu - my account - notification settings'
        ),
        'boardingAdminCancel' => array(
            'subject' => 'Your netframe workspace',
            'content' => 'Hello,<br><br>You started creating your Netframe workspace and interrupted it.. it\'s a shame !<br><br>You can relaunch  your netframe installation at any time in here'
        ),
        'welcomeAdmin' => array(
            'subject' => 'Welcome on Netframe',
            'welcome' => array(
                'content' => 'Hello, <br><br>
                            You have opened a workspace on Netframe and we thank you.  You can now customize your workspace by going to "Workspace Settings" in the user menu at the top right below your profile picture.<br><br>
                            But also :<br>
                            <ul>
                            <li>invite users to join you directly</li>
                            <li>manage the configuration of your space and associated rights</li>
                            <li>manage the applications you want to activate on your platform, including channels and mapping</li>
                            <li>graphically set your workspace to match your graphic charter</li>
                            </ul>
                            Good installation in your workspace,
Have a nice day'
            ),
            'userManual' => array(
                'content' => 'Hello,<br><br>
                            To find out how your Netframe workspace works, please find attached a manual.br>br>
       If you have any questions, do not hesitate to contact us at a href="mailto:contact@netframe.fr">contact us/a>. br>br>
                        Have a nice day!'
            ),
            '\'' => array(
                '$welcomeStep' => array(
                    '\'' => array(
                        'content' => 'Welcome'
                    )
                )
            )
        ),
        'boardingUserCanceled' => array(
            'subject' => 'Your netframe workspace !',
            'content' => 'You have been invited to join the workspace <strong>:instanceName</strong> by <strong>:instanceAdmin</strong>, join now <a href=":boardingLink">here</a> <br><br>
                     Have a good day,'
        ),
        'notConnectedUser' => array(
            'subject' => 'Your netframe workspace!',
            'content' => 'Hello,<br><br>
                      We have not seen you on your Netframe workspace :instanceName since :nbDays days. Lack of time? Do not want to? Not used to it?<br><br>
                      Whatever your reason, it\'s time to get started. Connect <a href=":instanceUrl">here</a>.<br><br>
                      If you do not want to come back, do not hesitate to <a href="mailto:contact@netframe.fr">write us</a> why.<br><br>
                      Have a nice day,'
        ),
        'notConnectedAdmin' => array(
            'subject' => 'Your netframe workspace!',
            'nbWeeks' => 'a week|two weeks',
            'content' => 'Hello,<br><br>
                    You have opened a Netframe workspace :nbWeek ago, and yet you did not come back to see us. Perhaps you would like to ask us a few questions and discuss with us to go further in the definition of your project?<br><br>
                    It\'s easy, <a href="mailto:contact@netframe.fr">write us</a> !<br><br>
                    Have a good day,'
        ),
        'becomeBuzz' => array(
            'subject' => 'congratulations - you have now a netframe account',
            'txt1' => 'N/A',
            'txt2' => 'N/A',
            'txtLinkHref' => 'connect to netframe'
        ),
        'completeProfile' => array(
            'content' => 'complete your profile',
            'goOnNetframe' => 'complete your profile',
            'subject' => 'complete your profile'
        ),
        'endtrial' => array(
            'content' => 'Hello, br>br>Your trial period expires in ... days, please enter a means of payment. br>br> Once the trial period is over, your access to the platform will be restricted. Click a href=":instance_subscription">here/a> to enter a payment method.br>br> Have a  nice day.',
            'subject' => 'Your trial period is running out'
        ),
        'lastPostsMedias' => array(
            'lastMedias' => 'lest content',
            'subject' => 'on netframe this week',
            'txt1' => 'last post',
            'txtLinkHref' => 'last post',
            'lastPosts' => 'last content'
        ),
        'noNetframeProfile' => array(
            1 => array(
                'subject' => 'your profile netframe'
            ),
            2 => array(
                'txt1' => 'last netframe registred profiles',
                'txtLinkHref' => 'connect to netframe',
                'subject' => 'your netframe profile'
            ),
            'content' => 'contents',
            'goOnNetframe' => 'create your reframe profile here',
            'subject' => 'your netframe profile'
        ),
        'noPost' => array(
            1 => array(
                'subject' => 'you netframe posts',
                'txtLinkHref' => 'rajouter des informations sur netframe

add more posts on netframe'
            ),
            2 => array(
                'txt1' => 'According to serious studies, the ideal length for a social media publication is between 40 and 120 characters. Beyond that, a lot of people don’t read.'
            ),
            'goOnNetframe' => 'Why not start right away by writing your first post here:',
            'subject' => 'Your posts on netframe'
        ),
        'noProfile' => array(
            'content' => 'You have not created your netframe profile yet'
        ),
        'paymenterror' => array(
            'content' => 'Hello, br>br>An error occurred while attempting to pay the last invoice. br>br>Click a href=":instance_subscription">here/a> to enter a payment method.br>br> Have a nice day.',
            'subject' => 'Payment error'
        ),
        'paymentsuccess' => array(
            'content' => 'Hello, br>br>Your last invoice has been paid.br>br>Please find attached the debit invoice.br>br> Have a nice day.',
            'subject' => 'Payment of an invoice'
        ),
        'visitors' => array(
            'subject' => 'your visit on netframe',
            'content' => 'You recently visited a href="http://netframe.com">netframe.com/a> the social network of enthusiasts and talents, we thank you! br/>br/>At the beginning of our story, every visitor is very important to us, and we wanted to tell you. Every day the site evolves, progresses, improves taking into account our users, so we need you! br/>br/>You have not concluded your registration on netframe, not convinced? , not interested? , not time? , come back to see us, we are only at the beginning of our history together, a href="http://netframe.com/auth/register">come and conclude it here/a>. br />br />'
        ),
        'weeklyInfo' => array(
            'contentTalents' => 'The netframe community gets a bit richer every day ! Here are the latest profiles created in netframe, come and visit them:',
            'contentBuzz' => 'Weekly info content',
            'txtLinkHref' => 'See you soon in Netframe'
        )
    ),
    'newAccountent' => array(
        'subject' => 'Netframe :: new delegated access',
        'welcome' => 'Hello ',
        'access' => 'Delegate access has been created for you on netframe: ',
        'toLink' => 'You can access it directly by clicking on the following link: '
    ),
    'api' => array(
        '\'' => array(
            '$result' => array(
                '\'' => array(
                    'content' => 'content'
                )
            )
        ),
        'errorKyc' => array(
            'content' => 'The identity documents you have transferred have not been validated. You can return to your account to upload new documents',
            'subject' => 'Profile approved'
        ),
        'validKyc' => array(
            'content' => 'Identity documents transferred have been approved',
            'subject' => 'approved account'
        )
    ),
    'visitor' => array(
        'invite' => array(
            'content1' => 'To join the workspace, please click on the link below',
            'join' => 'Joining the workspace',
            'rememberKey' => 'If you cannot click on the link above, here is the login address to copy/paste into your browser',
            'title' => 'invites you to join his workspace'
        ),
        'subject' => 'Invitation to join Netframe'
    )
);
