<div class="panel panel-default">
    <div class="panel-body">
        <a class="float-right fn-close-panel"><span class="glyphicon glyphicon-remove"></span></a>
        @if(session()->has('instanceRoleId') && session('instanceRoleId') == 1)
            <p>
                Su espacio de trabajo está listo, puede comenzar a publicar contenido, crear grupos, compartir información.
            </p>
            <p>
                Para invitar a usuarios, personalice su espacio de trabajo, vaya al menú en la esquina superior derecha y haga clic en "<a href="{{ url()->route('instance.parameters') }}">configuración del espacio de trabajo</a>"
            </p>
        @else
            <p>
                Se creó su cuenta, puede comenzar a publicar contenido, crear grupos, compartir información.
            </p>
        @endif
    </div>
</div>