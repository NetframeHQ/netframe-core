<?php

namespace App\Helpers;

/**
 *
 *
 * Manage Acl
 *
 */
class AclHelper
{
    /**
     * check user rights on action
     *
     */
    public static function getRights($profilType, $idProfil, $idRole = 3)
    {
        $profilType = str_replace('App\\', '', $profilType);
        $profilType = str_replace('app\\', '', $profilType);
        $profilType = strtolower($profilType);
        if (isset(session('acl')[strtolower($profilType)][$idProfil])
            && session('acl')[strtolower($profilType)][$idProfil] <= $idRole) {
            return session('acl')[strtolower($profilType)][$idProfil];
        }
        return false;
    }
}
