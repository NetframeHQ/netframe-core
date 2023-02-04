<div class="card">
    <div class="card-body">
        @if(session()->has('instanceRoleId') && session('instanceRoleId') == 1)
            <p>
                Votre espace de travail est prêt, vous pouvez commencer à publier du contenu, créer des groupes, partager des informations.
            </p>
            <p>
                Pour inviter des utilisateurs, personnaliser votre espace de travail, allez dans le menu en haut à droite et cliquer sur "<a href="{{ url()->route('instance.parameters') }}">paramètres</a>"
            </p>
        @else
            <p>
                Votre compte est créé, vous pouvez commencer à publier du contenu, créer des groupes, partager des informations.
            </p>
        @endif
    </div>
</div>