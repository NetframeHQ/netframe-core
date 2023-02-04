<!-- START SOURCE -->

    <!-- Modal -->
    <div class="modal th-onboarding fade modal-boarding-form" id="modal-onboarding" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <!-- Tools modal-->
    <div class="modal modal--tool th-onboarding fade" id="toolNav" tabindex="-1" role="dialog" aria-labelledby="toolNavTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="steps">
                <li class="is-active">1</li>
                <li>2</li>
                <li>3</li>
                <li>4</li>
                <li>5</li>
                <li>6</li>
                </ul>
                <h5 class="text-white" id="toolNavTitle">{{ trans('welcome.tooltips.tooltip1.title') }}</h5>
                <p class="mb-2">{{ trans('welcome.tooltips.tooltip1.txt1') }}</p>
                <p>{{ trans('welcome.tooltips.tooltip1.txt2') }}</p>
                <div class="box mb-4">
                    <img src="{{ asset('assets/img/boarding/tooltips/'.App::getLocale().'/tool-navigation.jpg') }}" />
                </div>
                <button class="btn btn-primary btn-block" type="button" data-dismiss="modal" data-toggle="modal" data-target="#toolShortcut" data-backdrop="">{{ trans('welcome.tooltips.next') }}</button>
            </div>
            <div class="modal-arrow modal-arrow--left d-none d-md-block"></div>
            <div class="modal-arrow d-md-none"></div>
        </div>
        </div>
    </div>

    <div class="modal modal--tool th-onboarding fade" id="toolShortcut" tabindex="-1" role="dialog" aria-labelledby="toolShortcutTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="steps">
                <li>1</li>
                <li  class="is-active">2</li>
                <li>3</li>
                <li>4</li>
                <li>5</li>
                <li>6</li>
                </ul>
                <h5 class="text-white" id="toolShortcutTitle">{{ trans('welcome.tooltips.tooltip2.title') }}</h5>
                <p>{{ trans('welcome.tooltips.tooltip2.txt1') }}</p>
                <div class="box mb-4">
                    <img src="{{ asset('assets/img/boarding/tooltips/'.App::getLocale().'/tool-shortcut.png') }}" />
                </div>
                <button class="btn btn-primary btn-block" type="button" data-dismiss="modal" data-toggle="modal" data-target="#toolNotification" data-backdrop="">{{ trans('welcome.tooltips.next') }}</button>
            </div>
            <div class="modal-arrow modal-arrow--left d-none d-md-block"></div>
            <div class="modal-arrow d-md-none"></div>
        </div>
        </div>
    </div>

    <div class="modal modal--tool th-onboarding fade" id="toolNotification" tabindex="-1" role="dialog" aria-labelledby="toolNotificationTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="steps">
                <li>1</li>
                <li>2</li>
                <li class="is-active">3</li>
                <li>4</li>
                <li>5</li>
                <li>6</li>
                </ul>
                <h5 class="text-white" id="toolNotificationTitle">{{ trans('welcome.tooltips.tooltip3.title') }}</h5>
                <p>{{ trans('welcome.tooltips.tooltip3.txt1') }}</p>
                <div class="box mb-4">
                    <img src="{{ asset('assets/img/boarding/tooltips/'.App::getLocale().'/tool-notification.jpg') }}" />
                </div>
                <button class="btn btn-primary btn-block" type="button" data-dismiss="modal" data-toggle="modal" data-target="#toolMenu" data-backdrop="">{{ trans('welcome.tooltips.next') }}</button>
            </div>
            <div class="modal-arrow"></div>
        </div>
        </div>
    </div>

    <div class="modal modal--tool th-onboarding fade" id="toolMenu" tabindex="-1" role="dialog" aria-labelledby="toolMenuTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="steps">
                <li>1</li>
                <li>2</li>
                <li>3</li>
                <li class="is-active">4</li>
                <li>5</li>
                <li>6</li>
                </ul>
                <h5 class="text-white" id="toolMenuTitle">{{ trans('welcome.tooltips.tooltip4.title') }}</h5>
                <p>{{ trans('welcome.tooltips.tooltip4.txt1') }}</p>
                <div class="box mb-4">
                    <img src="{{ asset('assets/img/boarding/tooltips/'.App::getLocale().'/tool-menu.jpg') }}" />
                </div>
                <button class="btn btn-primary btn-block" type="button" data-dismiss="modal" data-toggle="modal" data-target="#toolPanel" data-backdrop="">{{ trans('welcome.tooltips.next') }}</button>
            </div>
            <div class="modal-arrow"></div>
        </div>
        </div>
    </div>

    <div class="modal modal--tool th-onboarding fade" id="toolPanel" tabindex="-1" role="dialog" aria-labelledby="toolPanelTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="steps">
                <li>1</li>
                <li>2</li>
                <li>3</li>
                <li>4</li>
                <li class="is-active">5</li>
                <li>6</li>
                </ul>
                <h5 class="text-white" id="toolPanelTitle">{{ trans('welcome.tooltips.tooltip5.title') }}</h5>
                <p class="mb-4">{{ trans('welcome.tooltips.tooltip5.txt1') }}</p>
                <p class="mb-4">{{ trans('welcome.tooltips.tooltip5.txt2') }}</p>
                <button class="btn btn-primary btn-block" type="button" data-dismiss="modal" data-toggle="modal" data-target="#toolPost" data-backdrop="">{{ trans('welcome.tooltips.next') }}</button>
            </div>
            <div class="modal-arrow modal-arrow--right d-none d-md-block"></div>
            <div class="modal-arrow d-md-none"></div>
        </div>
        </div>
    </div>

    <div class="modal modal--tool th-onboarding fade" id="toolPost" tabindex="-1" role="dialog" aria-labelledby="toolPostTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="steps">
                <li>1</li>
                <li>2</li>
                <li>3</li>
                <li>4</li>
                <li>5</li>
                <li class="is-active">6</li>
                </ul>
                <h5 class="text-white" id="toolPostTitle">{{ trans('welcome.tooltips.tooltip6.title') }}</h5>
                <p>{{ trans('welcome.tooltips.tooltip6.txt1') }}</p>
                <div class="box mb-4">
                    <img src="{{ asset('assets/img/boarding/tooltips/'.App::getLocale().'/tool-post.jpg') }}" />
                </div>
                <button class="btn btn-primary btn-block" type="button" data-dismiss="modal" >{{ trans('welcome.tooltips.end') }}</button>
            </div>
            <div class="modal-arrow modal-arrow--center"></div>
        </div>
        </div>
    </div>

    <!-- END SOURCE -->

@section('javascripts')
@parent
<script>
@if(session()->has('instanceRoleId') && session('instanceRoleId') <= 2)
    $( document ).ready(function() {
        $('#modal-onboarding .modal-content').load('{{ url()->route('welcome.modal.welcome') }}',function(){
            $('#modal-onboarding').appendTo("body").modal('show');
        });
    });
@else
    $('#toolNav').appendTo("body").modal({
        show: true,
        backdrop: false
    });
@endif
(function($) {
    $(document).on('click', '.load-form-call', function(e){
        $(this).addClass('d-none');
        $('#form-phone').removeClass('d-none');
    });


    $(document).on('click', 'button.boarding-load-modal', function(e){
        var newUrl = $(this).data('href');
        $('#modal-onboarding .modal-content').load(newUrl);
    });

    $(document).on('click', 'a.boarding-load-modal', function(e){
        e.preventDefault();
        var newUrl = $(this).attr('href');
        $('#modal-onboarding .modal-content').load(newUrl);
    });

  //========= Post Ajax Form for Modal publish and return response
    $('.modal-boarding-form').on('click', 'button[type="submit"]', function(event) {
        _form = $(this).parents('.modal-content').find('form');
        if(!_form.hasClass('no-auto-submit')){
            submitModal(event, _form);
        }
    });

    $('.modal-boarding-form').on('submit', 'form', function(event) {
        if(!$(this).hasClass('no-auto-submit')){
            submitModal(event,$(this));
        }
    });

    $(document).on('click', '.add-email', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var nbFields = $('.emails-list input[type="text"').length + 1;
        $('#invite-users input[name="nbFields"]').val(nbFields);
        var htmlInvite = '<input class="form-control mb-2" placeholder="nom@exemple.fr" name="email'+nbFields+'" type="text" value="">';
        $('.emails-list').append(htmlInvite);
    });

    $(document).on('click', '.copyKey', function(e) {
        e.preventDefault();
        $('#hiddenKey').select();
        document.execCommand("copy");
    });
})(jQuery);
</script>
@stop