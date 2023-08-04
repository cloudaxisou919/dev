@extends('panel.layout.app')
@section('title', 'Stable Diffusion Settings')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('OpenAI Settings') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <form id="settings_form" onsubmit="return openaiSettingsSave();" enctype="multipart/form-data">
                        <h3 class="mb-[25px] text-[20px]">{{ __('Stable Diffusion Settings') }}</h3>
                        <div class="row">
                            <!-- TODO OPENAI API KEY -->
                            @if (env('APP_STATUS') == 'Demo')
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('OpenAi API Secret') }}</label>
                                        <input type="text" class="form-control" id="openai_api_secret"
                                            name="openai_api_secret" value="*********************">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('OpenAi API Secret') }}</label>
                                        <input type="text" class="form-control" id="openai_api_secret"
                                            name="openai_api_secret" value="{{ $setting->openai_api_secret }}">
                                    </div>
                                </div>
                            @endif


                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Default Openai Model') }}</label>
                                    <select class="form-select" name="openai_default_model" id="openai_default_model">
                                        <!--
              <option value="text-ada-001" {{ $setting->openai_default_model == 'text-ada-001' ? 'selected' : null }}>{{ __('Ada (Cheapest &amp; Fastest)') }}</option>
              <option value="text-babbage-001" {{ $setting->openai_default_model == 'text-babbage-001' ? 'selected' : null }}>{{ __('Babbage') }}</option>
              <option value="text-curie-001" {{ $setting->openai_default_model == 'text-curie-001' ? 'selected' : null }}>{{ __('Curie') }}</option>
              -->
                                        <option value="text-davinci-003"
                                            {{ $setting->openai_default_model == 'text-davinci-003' ? 'selected' : null }}>
                                            {{ __('Davinci (Most Expensive &amp; Most Capable)') }}</option>
                                        <option value="gpt-3.5-turbo"
                                            {{ $setting->openai_default_model == 'gpt-3.5-turbo' ? 'selected' : null }}>
                                            {{ __('ChatGPT (Most Expensive & Fastest & Most Capable)') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Default Openai Language') }}</label>
                                    <select class="form-select" name="openai_default_language" id="openai_default_language">
                                        @include('panel.admin.settings.languages')
                                    </select>
                                </div>
                            </div>


                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Maximum Input Length') }}</label>
                                    <input type="number" class="form-control" id="openai_max_input_length"
                                        name="opena_max_input_length" min="10" max="1500"
                                        value="{{ $setting->openai_max_input_length }}" required>
                                    <span
                                        class="block p-2 mt-1 rounded-md text-danger bg-[rgba(var(--tblr-danger-rgb),0.1)]">{{ __('In Characters') }}</span>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Image Count') }}</label>
                                    <input type="number" class="form-control" id="openai_max_output_length"
                                        name="opena_max_output_length" min="1" max="10"
                                        value="{{ $setting->openai_max_output_length }}" required>
                                    <span
                                        class="block p-2 mt-1 rounded-md text-danger bg-[rgba(var(--tblr-danger-rgb),0.1)]">{{ __('In Words. OpenAI has a hard limit based on Token limits for each model. Refer to OpenAI documentation to learn more. As a recommended by OpenAI, max result length is capped at 1500 words') }}</span>
                                </div>
                            </div>

                        </div>
                        <button form="settings_form" id="settings_button" class="btn btn-primary w-100">
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/assets/js/panel/settings.js"></script>
@endsection
