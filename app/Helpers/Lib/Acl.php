<?php  namespace App\Helpers\Lib;

class Acl
{

    public function __construct()
    {
    }

    /**
     * check user rights on action
     *
     * @return Response
     */
    public static function getRights($profilType, $idProfil, $idRole = 3)
    {
        $profilType = str_replace('App\\', '', $profilType);
        $profilType = str_replace('app\\', '', $profilType);
        if (isset(session('acl')[strtolower($profilType)][$idProfil])
            && session('acl')[strtolower($profilType)][$idProfil] <= $idRole) {
            return session('acl')[strtolower($profilType)][$idProfil];
        }
        return false;
    }
}
