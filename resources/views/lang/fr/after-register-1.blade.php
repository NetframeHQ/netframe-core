<p>
Tu es maintenant inscrit sur netframe. Tu peux naviguer sans restriction sur le site, commenter, aimer, partager.
</p>

<p class="text-center">
    <br />
    <a href="{{ url()->route('account.account') }}?directCreation=1" class="btn btn-border-default btn-lg">
        Complète ton profil netframe
    </a>
    <br /><br />
    <a href="{{ auth()->user()->getUrl() }}">Compléter plus tard</a>
</p>

