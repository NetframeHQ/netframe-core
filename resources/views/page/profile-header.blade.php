{{-- HEADER OF ENTITY/GROUPS/PROJECTS/CHANNELS/USER & DOCUMENTS --}}

@section('favicon')
{{$profile->profileImageSrc()}}
@endsection

<div class="main-header">

    <div class="main-header-infos">

        @if(isset($fromMediaExplorer) && $fromMediaExplorer)
            <span class="svgicon icon-profileheader">
                @include('macros.svg-icons.doc_big')
            </span>
        @else

            @if($profile->profileImage)
                <span class="avatar">
                    {!! HTML::thumbnail($profile->profileImage, '40', '40', array('class' => 'float-left'), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
                </span>

            @else
                <span class="svgicon icon-profileheader">
                    @include('macros.svg-icons.'.$profile->getType().'_big')
                </span>
            @endif
        @endif
        <div class="main-header-title">
            @if($profile->confidentiality == 0)
            <span class="private svgicon" title="{{ trans('messages.private') }}">
                @include('macros.svg-icons.private')
            </span>
            @endif
            <h2>
                <a
                    href="{{$profile->getUrl()}}"
                    @if($profile->getType() === 'house')
                        title="{{ trans('house.backToHouse') }}"
                    @elseif($profile->getType() === 'community')
                        title="{{ trans('community.backToCommunity') }}"
                    @elseif($profile->getType() === 'project')
                        title="{{ trans('project.backToProject') }}"
                    @else
                        title="{{ trans('channels.backToChannel') }}"
                    @endif
                >
                    {{ $profile->getNameDisplay() }}
               </a>
            </h2>
        </div>
        @if($profile->getType() != 'user' && $profile->description != '')
            <div class="main-header-subtitle">
                <p>
                    {!! \App\Helpers\StringHelper::collapsePostText($profile->description, 200) !!}
                </p>
                {{--
                @if($profile->tags != null && count($profile->tags) > 0)
                    <ul class="list-unstyled tags-list">
                        @foreach($profile->tags as $tag)
                            @if($tag->name != null)
                                <li><a href="{{ URL::Route('tags.page', ['tagId' => $tag->id, 'tagName' => str_slug($tag->name)]) }}">#{{ $tag->name }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                @endif
                --}}
            </div>
        @endif
    </div>


    <ul class="nf-actions">
        {{--
        <li class="nf-action">
            <div class="nf-lbl" title="{{ $profile->nbUsers() }} {{ trans_choice('page.members', $profile->users()->count()) }}">
                <span class="svgicon lbl-img">
                    @include('macros.svg-icons.members-xs')
                </span>
                <span class="lbl-txt">
                    {{ $profile->nbUsers() }}
                </span>
            </div>
        </li>
        --}}

        @php
            $refFolder = isset($refFolder) ? $refFolder : null;
            $refFolderId = $refFolder ? $refFolder->id : null;
            $profileIsCurrentUser = $profile->getType()=='user' && $profile->id == auth('web')->user()->id;
        @endphp

        {{-- CREATE/UPLOAD FOLDER/FILE --}}
        @if(
            isset($fromMediaExplorer) && $fromMediaExplorer

            && (
                // ne concerne pas les profils de type channel et user
                (!in_array($profile->getType(), ['channel', 'user']))
                // doit avoir les droits
                && $rights && $rights <= 4 && $confidentiality == 1
            )

            || (
                // ou que c'est bien un répertoire d'utilisateur
                $refFolder != null && $refFolder->personnal_folder == 1
                // si le répertoire appartient à l'utilisateur
                && $refFolder->personnal_user_folder == auth('web')->user()->id
            )

            || $profileIsCurrentUser
        )

            <li class="nf-action">
                <a href="#" class="nf-btn btn-ico btn-submenu">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.plus')
                    </span>
                </a>

                <div class="submenu-container submenu-right">
                    <ul class="submenu">

                    @if (\App\Instance::find(session('instanceId'))->hasApplication('office'))
                        <li>
                            <a href="{{ url()->route('office.create', ['documentType' => 'document', 'profileType' => $profile->getType(), 'profileId' => $profile->id, 'mediasFolder' => $refFolderId]) }}"
                               alt="{{ trans('xplorer.file.add.title') }}"
                               class="fn-add-file nf-btn"
                               data-toggle="modal"
                               data-target="#modal-ajax"
                            >
                                <span class="svgicon btn-img add-office-text">
                                    @include('macros.svg-icons.add-file')
                                </span>
                                <span class="btn-txt add-office-text">
                                    {{ trans('xplorer.plusMenu.addDoc') }}
                                </span>
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ url()->route('office.create', ['documentType' => 'spreadsheet', 'profileType' => $profile->getType(), 'profileId' => $profile->id, 'mediasFolder' => $refFolderId]) }}"
                                class="fn-add-file nf-btn"
                                alt="{{ trans('xplorer.file.add.title') }}"
                                data-toggle="modal"
                                data-target="#modal-ajax"
                            >
                                <span class="svgicon btn-img add-office-sheet">
                                    @include('macros.svg-icons.add-file')
                                </span>
                                <span class="btn-txt add-office-sheet">
                                    {{ trans('xplorer.plusMenu.addXls') }}
                                </span>
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ url()->route('office.create', ['documentType' => 'presentation', 'profileType' => $profile->getType(), 'profileId' => $profile->id, 'mediasFolder' => $refFolderId]) }}"
                                class="fn-add-file nf-btn"
                                alt="{{ trans('xplorer.file.add.title') }}"
                                data-toggle="modal"
                                data-target="#modal-ajax"
                            >
                                <span class="svgicon btn-img add-office-slide">
                                    @include('macros.svg-icons.add-file')
                                </span>
                                <span class="btn-txt add-office-slide">
                                    {{ trans('xplorer.plusMenu.addPpt') }}
                                </span>
                            </a>
                        </li>
                        @endif

                        <li>
                            <a href="{{ url()->route('xplorer_add_file', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder, 'driveFolder' => $driveFolder]) }}"
                                class="fn-add-file nf-btn" data-toggle="modal" data-target="#modal-files"
                                alt="{{ trans('xplorer.file.add.title') }}"
                            >
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.export')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('xplorer.plusMenu.importFile') }}
                                </span>
                            </a>
                        </li>

                        @if(!isset($driveFolders))
                            <li>
                                <a href="{{ url()->route('xplorer_import_folder', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder]) }}"
                                    class="fn-import-file nf-btn"
                                    alt="{{ trans('xplorer.file.add.title') }}"
                                >
                                    <span class="svgicon btn-img">
                                        @include('macros.svg-icons.import')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('xplorer.plusMenu.linkDrive') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        <li class="sep"><i></i></li>

                        <li>
                            @if(isset($driveFolder))
                                <a href="{{ url()->route('xplorer_edit_folder', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder, 'driveFolder' => $driveFolder]) }}"
                                    class="fn-add-folder nf-btn" data-toggle="modal" data-target="#modal-ajax">
                            @else
                                <a href="{{ url()->route('xplorer_edit_folder', ['profileType' => $profileType, 'profileId' => $profileId]) }}?parent={{ $idFolder }}"
                                    class="fn-add-folder nf-btn" data-toggle="modal" data-target="#modal-ajax"
                                    lt="{{ trans('xplorer.folder.add.title') }}">
                            @endif
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.add-folder')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('xplorer.plusMenu.createFolder') }}
                                </span>
                            </a>
                        </li>


                    </ul>
                </div>
            </li>
        @endif

        @if(isset($fromMediaExplorer) && $fromMediaExplorer)
            {{-- CUSTOM NAV GRID/LIST --}}
            <li class="nf-action nf-custom-nav">
                <a href="#" class="nf-btn btn-ico btn-submenu">
                    <span class="svgicon btn-img {{ (session('userXplorerView') == 'grid') ? 'd-none' : '' }}">
                        @include('macros.svg-icons.list-line')
                    </span>
                    <span class="svgicon btn-img {{ (session('userXplorerView') == 'list') ? 'd-none' : '' }}">
                        @include('macros.svg-icons.list-grid')
                    </span>
                </a>
                <div class="submenu-container submenu-right">
                    <ul class="submenu">
                        <li>
                            <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'list') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-list-wrapper" data-view-slug="list">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.list-line')
                                </span>
                                <span class="btn-txt">
                                    Visualiser en ligne
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'grid') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-grid-wrapper" data-view-slug="grid">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.list-grid')
                                </span>
                                <span class="btn-txt">
                                    Visualiser en grille
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif


        {{-- CUSTOM LINKS PROFILE ENTITY/GROUPS/PROJECTS/CHANNELS --}}


        {{-- ••• MENU --}}
        <li class="nf-action nf-custom-nav">
            <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.menu')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">

                    {{-- CUSTOM LINK ASSOCIATED PAGES FOR CHANNEL - USER PROFILE  --}}
                    @if($profile->getType() == 'channel' && $profile->personnal == 1)
                        <li class="submenu-linked">
                            <a class="nf-btn" href="{{ $profile->profile->getUrl() }}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.'.$profile->profile->getType())
                                </span>
                                <span class="btn-txt">
                                    @if($profile->profile->getType() === 'house')
                                        {{ trans('house.backToHouse') }}
                                    @elseif($profile->profile->getType() === 'community')
                                        {{ trans('community.backToCommunity') }}
                                    @elseif($profile->profile->getType() === 'project')
                                        {{ trans('project.backToProject') }}
                                    @else
                                        {{ trans('netframe.viewProfile') }}
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endif

                    {{-- CUSTOM LINK ENTITE/GROUPES/PROJETS/CHANNEL --}}
                    @if(isset($fromMediaExplorer) && $fromMediaExplorer && $profile->getType() != 'user')
                        <li>
                            <a href="{{ $profile->getUrl() }}" class="nf-btn">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.back')
                                </span>
                                <span class="btn-txt">
                                    @if($profile->getType() === 'house')
                                        {{ trans('house.backToHouse') }}
                                    @elseif($profile->getType() === 'community')
                                        {{ trans('community.backToCommunity') }}
                                    @elseif($profile->getType() === 'project')
                                        {{ trans('project.backToProject') }}
                                    @else
                                        {{ trans('channels.backToChannel') }}
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endif

                    {{-- CUSTOM LINK ASSOCIATED PAGES  --}}
                    @if($profile->getType() != 'channel' && $profile->getType() != 'user' )
                        @foreach($profile->channels()->getResults() as $channel )
                            <li class="submenu-linked">
                                <a class="nf-btn" href="{{ $channel->getUrl() }}">
                                    <span class="svgicon btn-img">
                                        @include('macros.svg-icons.'.$channel->getType())
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('netframe.channel') }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    @endif

                    {{-- CUSTOM LINK DOCUMENT GROUPS/ENTITY/PROJECTS --}}
                    @if($profile->getType() != 'user' && !isset($fromMediaExplorer) || !$fromMediaExplorer)
                        <li>
                            <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.doc')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.documents') }}
                                </span>
                            </a>
                        </li>
                    @endif

                    @if($rights && $rights < 3 && $profile->getType() === 'user')
                        {{-- CUSTOM LINK USER --}}
                        @if(isset($fromMediaExplorer) && $fromMediaExplorer)
                            <li>
                                <a href="{{ $profile->getUrl() }}" class="nf-btn">
                                    <span class="btn-img svgicon">
                                        @include('macros.svg-icons.user')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('netframe.navUser') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        {{-- CUSTOM LINK DIRECTORY USER
                        <li>
                            <a class="nf-btn" href="{{ url()->route('directory.home') }}">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.members')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.myFriends') }}
                                </span>
                            </a>
                        </li>
                        --}}

                        @if(!isset($fromMediaExplorer) && !$fromMediaExplorer)
                            {{-- CUSTOM LINK SETTINGS MY PROFILE --}}
                            <li>
                                <a class="nf-btn" href="{{ url()->route('account.account') }}">
                                    <span class="svgicon btn-img">
                                        @include('macros.svg-icons.settings')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('netframe.myInstance') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @endif

                    {{-- CUSTOM LINK SETTINGS GROUPS/ENTITY/PROJECTS/CHANNELS --}}
                    @if($rights && $rights < 3 && $profile->getType() != 'user' && !isset($fromMediaExplorer))
                        <li>
                            <a class="nf-btn" href="{{ url()->route($profile->getType().'.edit', [$profile->id]) }}">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.settings')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.myInstance') }}
                                </span>
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
        </li>
        {{-- SIDEBAR TOGGLE --}}
        @if($profile->active == 1)
            <li class="nf-action">
                <a href="#" class="content-sidebar-toggle nf-btn btn-ico">
                    <span class="btn-img svgicon fn-close">
                        @include('macros.svg-icons.sidebar-close2')
                    </span>
                    <span class="btn-txt fn-close">
                        {{ trans('netframe.close_sidebar') }}
                    </span>
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.sidebar-open')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.open_sidebar') }}
                    </span>
                </a>
            </li>
        @endif
    </ul>
</div>