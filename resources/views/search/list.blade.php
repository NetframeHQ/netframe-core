<ul class="list-unstyled search-results">
    @foreach($results as $profile)
        @if(in_array(class_basename($profile), config('search.profilesModels')))
            @if($profile->active == 1)
                <li data-profile-id="{{ $profile->id }}" data-profile-type="{{ $profile->getType() }}">
                    <div class="search-results-wrapper">
                        <div class="d-flex">
                            {!! HTML::thumbImage($profile->profileImage, 40, 40, [], strtolower(class_basename($profile)).'_big', 'search-result-icon') !!}
                            <div class="search-results-infos">
                                <h3 class="search-results-title"><a href="{{ $profile->getUrl() }}">{{ $profile->getNameDisplay() }}</a></h3>
                                <p>
                                    @if(class_basename($profile) != 'User' && $profile->users()->count() > 0)
                                        {{ $profile->users()->count() }} {{ trans_choice('page.members', $profile->users()->count()) }}
                                        @if($profile->tags()->count() > 0)
                                            •
                                            @foreach($profile->tags as $tag)
                                                #{{ $tag->name }}
                                            @endforeach

                                        @endif
                                    @endif
                                 </p>
                            </div>
                        </div>
                        <div class="search-results-actions">
                            @php
                                $user = auth()->guard('web')->user()
                            @endphp
                            @if(in_array($profile->getType(), ['channel', 'house', 'project', 'community']))
                                @if(in_array($profile->getType(), ['house', 'project', 'community']))
                                    {!! HTML::subscribeBtnProfile($profile, $profile->isFollowedByCurrentUser(), $profile->followers()->count()) !!}
                                @endif
                                {!! HTML::joinProfileBtn($profile->id, $profile->getType(), $user->id, array_key_exists($profile->id, $user->relations()['membership'][$profile->getType()]), $profile->confidentiality, $profile->users()->count()) !!}
                            @endif
                            <!-- Add friend button -->
                            @if(in_array($profile->getType(), ['user']))
                                {!! HTML::addFriendBtn(['author_id' => $profile->id,'user_from' => $user->id,'author_type'  => 'user'], \App\Friends::relation($profile->id) ) !!}
                            @endif
                        </div>
                    </div>
                </li>
            @endif
        @elseif(class_basename($profile) == 'Media')
            <li data-profile-id="{{ $profile->id }}" data-profile-type="{{ $profile->getType() }}">
                <div class="search-results-wrapper">
                    <div class="d-flex">
                        <div class="search-results-icon">
                            @if (!$profile->isTypeDisplay())
                                <a href="{{ url()->route('media_download', array('id' => $profile->id)) }}" target="_blank" class="item-link">
                            @else
                                <a class="viewMedia item-link"
                                    data-media-name="{{ $profile->name }}"
                                    data-media-id="{{ $profile->id }}"
                                    data-media-type="{{ $profile->type }}"
                                    data-media-platform="{{ $profile->platform }}"
                                    data-media-mime-type="{{ $profile->mime_type }}"

                                    @if ($profile-> !== platform'local')
                                        data-media-file-name="{{ $profile->file_name }}"
                                    @endif
                                >
                            @endif
                                {!! HTML::thumbnail($profile, '', '', []) !!}
                            </a>
                        </div>
                        <div class="search-results-infos">
                            <h3 class="search-results-title">{{ $profile->getNameDisplay() }}</h3>
                            {{ \App\Helpers\DateHelper::xplorerDate($profile->created_at, $profile->updated_at) }}
                        </div>
                    </div>
                    <div class="search-results-actions">
                        @if (!$profile->isTypeDisplay())
                                <a href="{{ url()->route('media_download', array('id' => $profile->id)) }}" target="_blank" class="button primary">
                            @else
                                <a class="viewMedia button primary"
                                    data-media-name="{{ $profile->name }}"
                                    data-media-id="{{ $profile->id }}"
                                    data-media-type="{{ $profile->type }}"
                                    data-media-platform="{{ $profile->platform }}"
                                    data-media-mime-type="{{ $profile->mime_type }}"

                                    @if ($profile-> !== platform'local')
                                        data-media-file-name="{{ $profile->file_name }}"
                                    @endif
                                >
                            @endif
                            {{ trans('search.open') }}
                        </a>
                        @php
                            $profileFolder = $profile->folder();
                        @endphp

                        <a href="{{route('medias_explorer',[
                            'profileType' => $profileFolder->getType(),
                            'profileId' => $profileFolder->id,
                            'folder' => $profileFolder->medias_folders_id
                        ])}}" target="_blank" class="button primary">

                            {{ trans('search.access') }}
                        </a>
                    </div>
                </div>
            </li>
        @else
            <li data-profile-id="{{ $profile->id }}" data-profile-type="{{ $profile->getType() }}">
                <div class="search-results-preview">
                    <p>
                        @if(isset($profile->title))
                            <strong>{{ $profile->title }}</strong><br>
                        @endif
                        @if(!empty($profile->description))
                            {!! \App\Helpers\StringHelper::collapsePostText($profile->description) !!}
                        @elseif(method_exists($profile, "getDescription") && !empty($profile->getDescription()))
                            {!! \App\Helpers\StringHelper::collapsePostText($profile->getDescription()) !!}
                        @endif
                    </p>
                </div>
                <div class="search-results-wrapper">
                    <div class="d-flex">
                        {!! HTML::thumbImage($profile->author->profileImage, 40, 40, [], strtolower(class_basename($profile->author)).'_big', 'search-result-icon') !!}
                        <div class="search-results-infos">
                            <h3 class="search-results-title">{{ $profile->author->getNameDisplay() }}</h3>
                            <p>
                                @if(class_basename($profile->author) != 'User' && $profile->author->users()->count() > 0)
                                    {{ $profile->author->users()->count() }} {{ trans_choice('page.members', $profile->author->users()->count()) }}
                                    @if($profile->author->tags()->count() > 0)
                                        •
                                        @foreach($profile->author->tags as $tag)
                                            #{{ $tag->name }}
                                        @endforeach

                                    @endif
                                @endif
                             </p>
                        </div>
                    </div>
                    <div class="search-results-actions">
                        <a href="{{ $profile->getUrl() }}" class="button primary">
                            {{ trans('search.viewPost') }}
                        </a>
                    </div>
                </div>
            </li>
        @endif
    @endforeach
</ul>
