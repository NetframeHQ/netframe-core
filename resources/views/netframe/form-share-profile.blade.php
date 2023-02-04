<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('form.newShare') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->
<div class="modal-body modal-share">
    {{ Form::open(['url'=> '/netframe/publish-share-profile', 'id' => 'form-share-profile']) }}

    <div id="publish-as-hidden-sh">
        {{ Form::hidden("author_id", $share->default_author->author_id) }}
        {{ Form::hidden("author_type", $share->default_author->author_type) }}
    </div>

    <div id="publish-as-hidden-shas">
        {{ Form::hidden("true_author_id", $share->true_author->author_id) }}
        {{ Form::hidden("true_author_type", $share->true_author->author_type) }}
    </div>

    {{ Form::hidden("profileType", get_class($profile)) }}
    {{ Form::hidden("profileId", $profile->id) }}
    {{ Form::hidden("edit", (isset($inputOld['edit']) ? $inputOld['edit'] : $edit)) }}
    {{ Form::hidden("id_share", (isset($inputOld['id_share']) ? $inputOld['id_share'] : $shareId)) }}

    {{ Form::message() }}
    <div class="form-group">
        {{ Form::label('content', trans('form.message')) }}
        <br />
        {{ Form::textarea( 'content', (isset($inputOld['content']) ? $inputOld['content'] : $shareContent), ['rows'=>'7', 'class'=>'form-control mentions '.(($errors->has('content')) ? 'is-invalid' : ''), 'id' => 'form-share-content'] ) }}
        {{ $errors->first('content', '<p class="invalid-feedback">:message</p>') }}

        <ul class="nf-actions">
            @include('components.emojis.emojis', ['emojiTarget' => '#form-share-profile #form-share-content'])
        </ul>
    </div>

    <div class="form-group clearfix modal-btns">
        <ul class="publisher">
            <li class="posting-publish-as list-inline-item">
                <span>{{ trans('netframe.publishOn') }} :</span>
                {!! HTML::publishAs('#form-share-profile', [
                        'allProfiles' => $NetframeProfiles,
                        'userChannels' => $UserChannels,
                    ], [
                        'id'=>'author_id',
                        'type'=>'author_type',
                        'postfix'=>'sh'
                    ],
                    true,[
                        'id' => $share->default_author->author_id,
                        'type' => $share->default_author->author_type
                        ]
                ) !!}
            </li>

            <li class="posting-publish-as list-inline-item tl-publish-as-choice {{ ($share->default_author->author_type != 'user') ? '' : 'd-none' }}">
                <span>{{ trans('netframe.publishAs') }} :</span>
                {!! HTML::publishAs('#form-share-profile',
                    $NetframeProfiles, [
                        'id'=>'true_author_id',
                        'type'=>'true_author_type',
                        'postfix'=>'shas',
                        'secondary' => true
                    ],
                    true,[
                        'id' => $share->true_author->author_id,
                        'type' => $share->true_author->author_type
                    ]
                ) !!}
            </li>
        </ul>

        <div class="float-right">
            <button type="submit" class="nf-btn btn-i btn-lg">
                <span class="btn-img svgicon d-none fn-spinner">
                    @include('macros.svg-icons.spinner')
                </span>
                <span class="btn-txt fn-submit">
                    {{ trans('form.publish') }}
                </span>
                <span class="btn-img svgicon">
                    @include('macros.svg-icons.arrow-right')
                </span>
            </button>
        </div>
    </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->

<script>
$("textarea.mentions").mentionsInput({source: laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels'});
</script>