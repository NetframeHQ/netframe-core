@extends('instances.main')

@section('title')
    {{ trans('instances.subscription.title') }} • {{ $globalInstanceName }}
@stop

@section('subcontent')
    @php
       $subscribeValid = $instance->subscribeValid();
    @endphp
    @if($subscribeValid != 1)
        <div class="card alert alert-danger">
           {{ trans('billing.paymentStatus.'.$subscribeValid, ['remainigDays' => $instance->remainingDays()]) }}
        </div>
    @endif

    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.subscription.title') }}
        </div>
        <div class="nf-form-informations">
            <p>{{ trans('instances.subscription.billing.'.$billingOffer.'.intro') }}</p>
            @if($billingOffer == 'free')
                <hr>
                @if($instance->begin_date != null)
                    <p>{{ trans('instances.subscription.billing.free.maxDate') }} {{ $instance->begin_date->format('d/m/Y') }}</p>
                    <p>{{ trans('instances.subscription.billing.free.increaseLimit') }}</p>
                @endif
            @elseif($billingOffer == 'normal')
                <hr>
                <p>{{ trans('instances.subscription.billing.'.$billingOffer.'.users') }} :
                {{ $instance->users->count() }}</p>

                <p>{{ trans('instances.subscription.billing.'.$billingOffer.'.maxStorageUser') }} :
                {{ $instanceQuota['userQuota'] }} {{ trans('instances.subscription.quotaUnit') }}</p>
            @endif
        </div>

        {{ Form::open() }}
            @if($card!='')
                <strong>{{ trans('welcome.actual-card') }}</strong>
                <div class="form-group"><br>
                    {{$card}}
                    <button type="submit" class="button primary float-right" name="delete">{{ trans('welcome.delete') }}</button>
                </div>
            @else

                @if(isset($errorCb))
                    <div class="alert alert-danger">
                        {{ trans('stripe.'.$errorCb) }}
                    </div>
                @endif

                <label class="nf-form-cell nf-cell-full @if($errors->has('card_number')) nf-cell-error @endif">
                    <input type="text" class="nf-form-input" id="card_number" name="card_number" value="{{ request()->old('card_number') }}" placeholder=" ">
                    <span class="nf-form-label">
                        {{ trans('welcome.card-number')}}
                    </span>
                    {!! $errors->first('card_number', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <label class="nf-form-cell @if($errors->has('card_expiry')) nf-cell-error @endif">
                    <input type="text" class="nf-form-input" id="card_expiry" name="card_expiry" value="{{ request()->old('card_expiry') }}" placeholder="--/--">
                    <span class="nf-form-label">
                        {{ trans('welcome.card-expiry')}}
                    </span>
                    {!! $errors->first('card_expiry', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <label class="nf-form-cell @if($errors->has('card_crypto')) nf-cell-error @endif">
                    <input type="text" class="nf-form-input" id="card_crypto" name="card_crypto" value="{{ request()->old('card_crypto') }}" placeholder=" ">
                    <span class="nf-form-label">
                        {{ trans('welcome.card-crypto')}}
                    </span>
                    {!! $errors->first('card_crypto', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>

                <div class="nf-form-validation">
                    <button type="submit" class="nf-btn btn-primary btn-xxl">
                        <div class="btn-txt">
                            {{ trans('form.save') }}
                        </div>
                        <div class="svgicon btn-img">
                            @include('macros.svg-icons.arrow-right')
                        </div>
                    </button>
                </div>
            @endif
        {{ Form::close()}}
    </div>

    <!-- ADRESSE DE FACTURATION -->
    {{--
        <div class="nf-form nf-col-2">
            <div class="nf-settings-title">
                Adresse de facturation
            </div>

            <label class="nf-form-cell @if($errors->has('firstname')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="firstname" name="firstname" placeholder=" ">
                <span class="nf-form-label">
                    {{ trans('form.setting.firstname')}}
                </span>
                {!! $errors->first('firstname', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell @if($errors->has('lastname')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="lastname" name="lastname" placeholder=" ">
                <span class="nf-form-label">
                    {{ trans('form.setting.name')}}
                </span>
                {!! $errors->first('lastname', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell nf-cell-full @if($errors->has('address1')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="address1" name="address1" placeholder=" ">
                <span class="nf-form-label">
                    {{ trans('form.infos.address')}}
                </span>
                {!! $errors->first('address1', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell nf-cell-full @if($errors->has('address2')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="address2" name="address2" placeholder=" ">
                <span class="nf-form-label">
                    {{ trans('form.infos.address')}}
                </span>
                {!! $errors->first('address2', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell @if($errors->has('zipcode')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="zipcode" name="zipcode" placeholder=" ">
                <span class="nf-form-label">
                    {{ trans('form.infos.codepostal')}}
                </span>
                {!! $errors->first('zipcode', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <label class="nf-form-cell @if($errors->has('city')) nf-cell-error @endif">
                <input type="text" class="nf-form-input" id="city" name="city" placeholder=" ">
                <span class="nf-form-label">
                    {{ trans('form.infos.city')}}
                </span>
                {!! $errors->first('city', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>

            <div class="nf-form-validation">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('form.save') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
        </div>
    --}}

    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.subscription.delegate_access.title') }}
        </div>
        <div class="nf-form-informations">
            {{ trans('instances.subscription.delegate_access.text') }}
            @if($instance->accountents!=null)
                <ul>
                    @foreach($instance->accountents as $acc)
                        <li>
                            <p>
                                {{$acc->email}}
                            </p>
                            <a href="{{url()->route('instance.subscription', ['action' => $acc->id])}}" class="nf-btn btn-ico">
                                <div class="btn-img svgicon">
                                    @include('macros.svg-icons.trash')
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <hr>

            <a href="{{ url()->route('accountent.login') }}" class="nf-btn btn-xxl btn-full" target="_blank">
                <div class="btn-txt">
                    {{ trans('instances.subscription.delegate_access.connect') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </a>
        </div>
        @if($instance->accountents!=null)
            {{ Form::open() }}
                <!-- EMAIL -->
                <label class="nf-form-cell nf-cell-full @if($errors->has('email')) nf-cell-error @endif">
                    <input type="text" class="nf-form-input" id="email" name="email" value="{{ request()->old('email') }}" placeholder=" ">
                    <span class="nf-form-label">
                        {{ trans('channels.feeds.add') }} {{ trans('form.setting.email') }}
                    </span>
                    {!! $errors->first('email', '<p class="invalid-feedback">:message</p>') !!}
                    <div class="nf-form-cell-fx"></div>
                </label>
                <div class="nf-form-validation">
                    <button type="submit" name="delegate_access" class="nf-btn btn-primary btn-xxl">
                        <div class="btn-txt">
                            {{ trans('form.save') }}
                        </div>
                        <div class="svgicon btn-img">
                            @include('macros.svg-icons.arrow-right')
                        </div>
                    </button>
                </div>
            {{ Form::close() }}
        @else
            <p>{{ trans('instances.subscription.bills.noBill') }}</p>
        @endif
    </div>



    <!-- <div class="panel panel-default">
        <div class="panel-heading">
            <h2>{{-- trans('instances.subscription.bills.title') --}}</h2>
        </div>

        <div class="panel-body table-hover">
            {{--@if($billings!=null)--}}
                <table class="table">
                    <thead>
                        <th>{{-- trans('instances.subscription.bills.header.number') --}}</th>
                        <th>{{-- trans('instances.subscription.bills.header.total') --}}</th>
                        <th>{{-- trans('instances.subscription.bills.header.paid') --}}</th>
                        <th>{{-- trans('instances.subscription.bills.actions') --}}</th>
                    </thead>
                    <tbody>
                        {{--@foreach($billings as $bill)--}}
                            <tr>
                                <td>{{--$bill->number--}}</td>
                                <td>{{--$bill->total--}}</td>
                                {{--@if($bill->paid)--}}
                                    <td>{{trans('instances.subscription.bills.paid')}}</td>
                                    <td><a href="{{-- url()->route('instance.billing', ['number'=>$bill->number]) --}}" target="_blank"></a></td>
                               {{-- @else--}}
                                    <td>{{--trans('instances.subscription.bills.not_paid')--}}</td>
                                    <td><a href="{{-- url()->route('instance.pay') --}}" title="Payer la facture"></a></td>

                                {{--@endif--}}

                            </tr>
                        {{--@endforeach--}}
                </tbody>
            </table>
            {{-- @else--}}
                <p>{{-- trans('instances.subscription.bills.noBill') --}}</p>
            {{--@endif--}}
        </div>
    </div> -->
    <!-- <input type="hidden" name="oauth_version" value="1.0" />
      <input type="hidden" name="oauth_nonce" value="d4d02954fe8e14383902c9ac29a44aea" />
      <input type="hidden" name="oauth_timestamp" value="1537450211" />
      <input type="hidden" name="oauth_consumer_key" value="tMmOCA2fj12KMlYmpu5D19rCTDN2yr5aLypBBc4WP" />
      <input type="hidden" name="lti_message_type" value="basic-lti-launch-request" />
      <input type="hidden" name="lti_version" value="LTI-1p0" />
      <input type="hidden" name="resource_link_id" value="429785226" />
      <input type="hidden" name="resource_link_title" value="Phone home" />
      <input type="hidden" name="resource_link_description" value="Will ET phone home, or not; click to discover more." />
      <input type="hidden" name="user_id" value="29123" />
      <input type="hidden" name="roles" value="Instructor" />
      <input type="hidden" name="lis_person_name_full" value="John Logie Baird" />
      <input type="hidden" name="lis_person_name_family" value="Baird" />
      <input type="hidden" name="lis_person_name_given" value="John" />
      <input type="hidden" name="lis_person_contact_email_primary" value="jbaird@uni.ac.uk" />
      <input type="hidden" name="lis_person_sourcedid" value="sis:942a8dd9" />
      <input type="hidden" name="user_image" value="http://ltiapps.net/test/images/lti.gif" />
      <input type="hidden" name="context_id" value="S3294476" />
      <input type="hidden" name="context_type" value="CourseSection" />
      <input type="hidden" name="context_title" value="Telecommuncations 101" />
      <input type="hidden" name="context_label" value="ST101" />
      <input type="hidden" name="lis_course_offering_sourcedid" value="DD-ST101" />
      <input type="hidden" name="lis_course_section_sourcedid" value="DD-ST101:C1" />
      <input type="hidden" name="tool_consumer_info_product_family_code" value="jisc" />
      <input type="hidden" name="tool_consumer_info_version" value="1.2" />
      <input type="hidden" name="tool_consumer_instance_guid" value="vle.uni.ac.uk" />
      <input type="hidden" name="tool_consumer_instance_name" value="University of JISC" />
      <input type="hidden" name="tool_consumer_instance_description" value="A Higher Education establishment in a land far, far away." />
      <input type="hidden" name="tool_consumer_instance_contact_email" value="vle@uni.ac.uk" />
      <input type="hidden" name="tool_consumer_instance_url" value="https://vle.uni.ac.uk/" />
      <input type="hidden" name="launch_presentation_return_url" value="http://ltiapps.net/test/tc-return.php" />
      <input type="hidden" name="launch_presentation_css_url" value="http://ltiapps.net/test/css/tc.css" />
      <input type="hidden" name="launch_presentation_locale" value="en-GB" />
      <input type="hidden" name="launch_presentation_document_target" value="frame" />
      <input type="hidden" name="lis_outcome_service_url" value="http://ltiapps.net/test/tc-outcomes.php" />
      <input type="hidden" name="lis_result_sourcedid" value="afe2d038c4d48fb29cfb112988270fbe:::S3294476:::29123:::dyJ86SiwwA9" />
      <input type="hidden" name="ext_ims_lis_basic_outcome_url" value="http://ltiapps.net/test/tc-ext-outcomes.php" />
      <input type="hidden" name="ext_ims_lis_resultvalue_sourcedids" value="decimal" />
      <input type="hidden" name="ext_ims_lis_memberships_url" value="http://ltiapps.net/test/tc-ext-memberships.php" />
      <input type="hidden" name="ext_ims_lis_memberships_id" value="afe2d038c4d48fb29cfb112988270fbe:::4jflkkdf9s" />
      <input type="hidden" name="ext_ims_lti_tool_setting_url" value="http://ltiapps.net/test/tc-ext-setting.php" />
      <input type="hidden" name="ext_ims_lti_tool_setting_id" value="afe2d038c4d48fb29cfb112988270fbe:::d94gjklf954kj" />
      <input type="hidden" name="custom_tc_profile_url" value="http://ltiapps.net/test/tc-profile.php/afe2d038c4d48fb29cfb112988270fbe" />
      <input type="hidden" name="custom_system_setting_url" value="http://ltiapps.net/test/tc-settings.php/system/afe2d038c4d48fb29cfb112988270fbe" />
      <input type="hidden" name="custom_context_setting_url" value="http://ltiapps.net/test/tc-settings.php/context/afe2d038c4d48fb29cfb112988270fbe" />
      <input type="hidden" name="custom_link_setting_url" value="http://ltiapps.net/test/tc-settings.php/link/afe2d038c4d48fb29cfb112988270fbe" />
      <input type="hidden" name="custom_lineitems_url" value="http://ltiapps.net/test/tc-outcomes2.php/afe2d038c4d48fb29cfb112988270fbe/S3294476/lineitems" />
      <input type="hidden" name="custom_results_url" value="http://ltiapps.net/test/tc-outcomes2.php/afe2d038c4d48fb29cfb112988270fbe/S3294476/lineitems/dyJ86SiwwA9/results" />
      <input type="hidden" name="custom_lineitem_url" value="http://ltiapps.net/test/tc-outcomes2.php/afe2d038c4d48fb29cfb112988270fbe/S3294476/lineitems/dyJ86SiwwA9" />
      <input type="hidden" name="custom_result_url" value="http://ltiapps.net/test/tc-outcomes2.php/afe2d038c4d48fb29cfb112988270fbe/S3294476/lineitems/dyJ86SiwwA9/results/29123" />
      <input type="hidden" name="custom_context_memberships_url" value="http://ltiapps.net/test/tc-memberships.php/context/afe2d038c4d48fb29cfb112988270fbe" />
      <input type="hidden" name="custom_link_memberships_url" value="http://ltiapps.net/test/tc-memberships.php/link/afe2d038c4d48fb29cfb112988270fbe" />
      <input type="hidden" name="oauth_callback" value="about:blank" />
      <input type="hidden" name="oauth_signature_method" value="HMAC-SHA1" />
      <input type="hidden" name="oauth_signature" value="RPf0h7yggdGvoRg8YnCSg9ZyZN4=" /> -->
@stop