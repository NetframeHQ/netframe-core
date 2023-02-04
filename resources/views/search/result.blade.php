                        <div class="nf-search-result" data-profile-id="{{ $result['_id'] }}" data-profile-type="{{ $result['_type'] }}">
                            {{-- DOCUMENT --}}
                            @if ('medias' === $result['_type'])


                                @if ($result['_source']['isDocument'])
                                @php
                                if($result['_source']['hasOffice'] && $activeOffice && "application/pdf" !== $result['_source']['mime_type']) {
                                    $link = url()->route('office.document', array('documentId' => $result['_id']));
                                    $download = false;

                                /* ouvre les docs office avec le lecteur pdf */
                                } elseif (!$result['_source']['hasOffice'] && $activeOffice && $result['_source']['thumbnail']['feed_path'] != null) {
                                    $link = url()->route('media.pdf.viewer').'?file='.urlencode(URL::route('media_download', ['id' => $result['_id'], 'feed' => true]));
                                    $download = false;

                                /* Ouvre un PDF avec la visionneuse */
                                } elseif ("application/pdf" === $result['_source']['mime_type']) {
                                    $link = url()->route('media.pdf.viewer').'?file='.URL::route('media_download', ['id' => $result['_id']]);
                                    $download = false;

                                /* Télécharge le document */
                                }else {
                                    $link = url()->route('media_download', array('id' => $result['_id']));
                                    $download = true;
                                }
                                @endphp
                                <a class="nf-invisiblink"
                                   href="{{ $link }}"
                                   target="_blank"
                                   @if($download) download @endif></a>
                                @elseif (($result['_source']['isTypeDisplay']))
                                <a class="nf-invisiblink viewMedia"
                                   data-media-name="{{ $result['_source']['name'] }}"
                                   data-media-id="{{ $result['_id'] }}"
                                   data-media-type="{{ $result['_source']['type'] }}"
                                   data-media-platform="{{ $result['_source']['platform'] }}"
                                   data-media-mime-type="{{ $result['_source']['mime_type'] }}"
                                   @if ($result['_source']['platform'] !== 'local')
                                       data-media-file-name="{{ $result['_source']['file_name'] }}"
                                   @endif
                                   href="{{ url()->route('media_download', array('id' => $result['_id'])) }}"
                                   ></a>
                                @else
                                <a class="nf-invisiblink"
                                   target="_blank"
                                   href="{{ url()->route('media_download', array('id' => $result['_id'])) }}"></a>
                                @endif

                                @php
                                    $profile = explode('-', $result['_source']['profile_id']);
                                    $rightsMedia = \App\Lib\Acl::getRights($profile[0], $profile[1], 5)
                                @endphp

                                @include('media.partials.menu-actions', [
                                    'rights' => $rightsMedia,
                                    'profileType' => $profile[0],
                                    'profileId' => $profile[1],
                                    'media' => App\Media::find($result['_id']),
                                    'openLocation' => true
                                ])

                                <div class="search-result-ico">
                                    {!! HTML::thumbImage($result['_id'], 100, 100, [], $result['_type']) !!}
                                </div>
                                <div class="search-result-infos">
                                    <h3 class="search-result-title">{{ $result['_source']['name'] }}</h3>
                                    <p class="search-result-subtitle">{{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}</p>
                                </div>

                            {{-- USER --}}
                            @elseif ('users' === $result['_type'])
                                <a class="nf-invisiblink"href="{{ $result['_source']['url'] }}"></a>
                                <div class="search-result-ico">
                                    {!! HTML::thumbImage(
                                        $result['_source']['profile_media_id'],
                                        40,
                                        40,
                                        [],
                                        'user',
                                        'avatar',
                                        (empty($result['_source']['profile_media_id'])) ? \App\User::find($result['_source']['id']) : ''
                                    ) !!}
                                </div>
                                <div class="search-result-infos">
                                    <h3 class="search-result-title">{{ $result['_source']['fullname'] }}</h3>
                                    <p class="search-result-subtitle">{{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}</p>
                                </div>

                            {{-- PROFILE (PROJECT|HOUSE|COMMUNITY) --}}
                            @elseif (in_array($result['_type'], ['projects', 'houses', 'community']))
                                <a class="nf-invisiblink" href="{{ $result['_source']['url'] }}"></a>
                                <div class="search-result-ico">
                                    {!! HTML::thumbImage(
                                        $result['_source']['profile_media_id'],
                                        40,
                                        40,
                                        [],
                                        $result['_type'],
                                        ''
                                    ) !!}
                                </div>

                                <div class="search-result-infos">
                                    <h3 class="search-result-title">{{ ('projects' === $result['_type']) ? $result['_source']['title'] : $result['_source']['name'] }}</h3>
                                    <p class="search-result-subtitle">{{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}</p>
                                </div>

                            {{-- CHANNEL --}}
                            @elseif ("channels" === $result['_type'])
                                <a class="nf-invisiblink" href="{{ $result['_source']['url'] }}"></a>
                                <div class="search-result-ico"></div>
                                <div class="search-result-infos">
                                    <h3 class="search-result-title">
                                        @if ('personnal' == $result['_source']['name'])
                                            @foreach ($result['_source']['users'] as $user)
                                                @if ($user['id'] != auth()->guard('web')->user()->id)
                                                    {{ $user['fullname'] }}
                                                @endif
                                            @endforeach
                                        @else
                                            {{ $result['_source']['name'] }}
                                        @endif
                                    </h3>
                                    <p class="search-result-subtitle">{{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}</p>
                                </div>
                                <!-- <div class="search-result-content">
                                    {{ $result['_source']['description'] }}
                                </div> -->

                            {{-- NEWS --}}
                            @elseif ("news" === $result['_type'])
                            @php $defaultThumbs = ['App\Community' => 'community', 'App\Project' => 'project', 'App\House' => 'house', 'App\User' => 'user']; @endphp
                                <a class="nf-invisiblink" href="{{ $result['_source']['url'] }}"></a>
                                <div class="search-result-ico">
                                    @php
                                    if(isset($result['_source']['profile_type']) && $result['_source']['profile_type'] == 'user') {
                                        $profileRef = $result['_source']['author'];
                                        $authorType = 'user';
                                        $mainName = (array_key_exists('fullname', $result['_source']['author'])) ? $result['_source']['author']['fullname'] : $result['_source']['author']['name'];
                                        $byName = '';
                                    } else {
                                        $profileRef = $result['_source']['profile'];
                                        $authorType = $result['_source']['profile_type'];
                                        $mainName = ($result['_source']['profile_type'] == 'project') ? $result['_source']['profile']['title'] : $result['_source']['profile']['name'];
                                        $byName = (array_key_exists('fullname', $result['_source']['author'])) ? $result['_source']['author']['fullname'] : $result['_source']['author']['name'];
                                    }
                                    @endphp

                                    {!! HTML::thumbImage(
                                        $profileRef['profile_media_id'],
                                        40,
                                        40,
                                        [],
                                        $authorType,
                                        '',
                                        ($authorType == 'user') ? App\User::find($result['_source']['author']['id']) : ''
                                    ) !!}
                                </div>
                                <div class="search-result-infos">
                                    <h3 class="search-result-title">
                                        {{ $mainName }}
                                    </h3>
                                    <p class="search-result-subtitle">
                                        {{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}
                                        @if (!empty($byName) && $byName != $mainName)
                                            • {{ $byName }}
                                        @endif
                                    </p>
                                </div>
                                <div class="search-result-content">
                                    @php
                                    $content = $result['_source']['content'];
                                    $matches = [];
                                    $r = preg_match_all("/(.*)@\[(.*)\]\(.*\)(.*)/", $content, $matches);
                                    if($r === 1) {
                                        $content = $matches[1][0].'@'.$matches[2][0].$matches[3][0];
                                    }
                                    $r = preg_match_all("/@\[(.*)\]\(.*\)(.*)/", $content, $matches);
                                    if($r === 1) {
                                        $content = '@'.$matches[1][0].$matches[2][0];
                                    }
                                    @endphp
                                    {{ strip_tags($content) }}
                                </div>

                            {{-- EVENT --}}
                            @elseif ("events" === $result['_type'])
                                <a class="nf-invisiblink" href="{{ $result['_source']['url'] }}"></a>
                                <div class="search-result-ico">
                                    @php
                                    if(isset($result['_source']['profile_type']) && $result['_source']['profile_type'] == 'user') {
                                        $profileRef = $result['_source']['author'];
                                        $authorType = 'user';
                                        $mainName = (array_key_exists('fullname', $result['_source']['author'])) ? $result['_source']['author']['fullname'] : $result['_source']['author']['name'];
                                        $byName = '';
                                    } else {
                                        $profileRef = $result['_source']['profile'];
                                        $authorType = $result['_source']['profile_type'];
                                        $mainName = ($result['_source']['profile_type'] == 'project') ? $result['_source']['profile']['title'] : $result['_source']['profile']['name'];
                                        $byName = (array_key_exists('fullname', $result['_source']['author'])) ? $result['_source']['author']['fullname'] : $result['_source']['author']['name'];
                                    }
                                    @endphp

                                    {!! HTML::thumbImage(
                                        $profileRef['profile_media_id'],
                                        40,
                                        40,
                                        [],
                                        $authorType,
                                        '',
                                        ($authorType == 'user') ? App\User::find($result['_source']['author']['id']) : ''
                                    ) !!}
                                </div>
                                <div class="search-result-infos">
                                    <h3 class="search-result-title">
                                        {{ $mainName }}
                                    </h3>
                                    <p class="search-result-subtitle">
                                        {{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}
                                        @if (!empty($byName) && $byName != $mainName)
                                            • {{ $byName }}
                                        @endif
                                    </p>
                                </div>
                                <div class="search-result-content">
                                    {{ strip_tags($result['_source']['content']) }}
                                </div>

                            {{-- OTHER --}}
                            @else
                                <a class="nf-invisiblink" href="{{ $result['_source']['url'] }}"></a>
                                <div class="search-result-ico">
                                    {!! HTML::thumbImage($result['_id'], 40, 40, [], $result['_type']) !!}
                                </div>
                                <div class="search-result-infos">
                                    <h3 class="search-result-title">{{ $result['_source']['author']['fullname'] }}</h3>
                                    <p class="search-result-subtitle">{{ \App\Helpers\DateHelper::xplorerDate($result['_source']['created_at']) }}</p>
                                </div>
                                <div class="search-result-content">
                                    {{ strip_tags($result['_source']['content']) }}
                                </div>
                            @endif

                        </div>
