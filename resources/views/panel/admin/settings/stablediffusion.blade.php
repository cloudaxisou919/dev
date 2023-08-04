@extends('panel.layout.app')
@section('title', __('StableDiffusion Settings'))
@section('additional_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
					<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}" class="page-pretitle flex items-center">
						<svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
						</svg>
						{{__('Back to dashboard')}}
					</a>
                    <h2 class="page-title mb-2">
                        {{__('StableDiffusion Settings')}}
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
					<form id="settings_form" onsubmit="return stablediffusionSettingsSave();" enctype="multipart/form-data">
						<h3 class="mb-[25px] text-[20px]">{{__('StableDiffusion Settings')}}</h3>
						<div class="row">
                            <!-- TODO stablediffusion API KEY -->
                            @if(env('APP_STATUS') == 'Demo')
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{__('StableDiffusion API Secret')}}</label>
                                    <input type="text" class="form-control" id="stable_diffusion_api_key" name="stable_diffusion_api_key" value="*********************">
                                </div>
                            </div>
                            @else
                                <div class="col-md-12">
                                    <div class="form-control border-none p-0 mb-3 [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em] [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius]">
                                        <label class="form-label">{{__('StableDiffusion API Secret')}}</label>
                                        <select class="form-control select2" name="stable_diffusion_api_key" id="stable_diffusion_api_key" multiple>
                                            @foreach(explode(',', $settings_two->stable_diffusion_api_key) as $secret)
                                                <option value="{{$secret}}" selected>{{$secret}}</option>
                                            @endforeach
                                        </select>
										<div class="bg-blue-100 text-blue-600 rounded-xl !p-3 !mt-2 dark:bg-blue-600/20 dark:text-blue-200">
											<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
											{{__('You can enter as much API KEY as you want. Click "Enter" after each api key.')}}
										</div>
										<div class="bg-orange-100 text-orange-600 rounded-xl !p-3 !mt-2 dark:bg-orange-600/20 dark:text-orange-200">
											<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"></path> <path d="M12 9v4"></path> <path d="M12 17h.01"></path> </svg>
											{{__('Please ensure that your stable diffusion API key is fully functional and billing defined on your stable diffusion account.')}}
										</div>
                                        <a href="{{route('dashboard.admin.settings.stablediffusion.test')}}" target="_blank" class="btn btn-primary w-100 mt-2 mb-2">
                                            {{__('After Saving Setting, Click Here to Test Your Api Keys')}}
                                        </a>
                                    </div>
                                </div>
                            @endif

							{{-- <div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{__('Default stablediffusion Model')}}</label>
									<select class="form-select" name="stablediffusion_default_model" id="stablediffusion_default_model">
										<!--
										<option value="text-ada-001" {{$setting->stablediffusion_default_model == 'text-ada-001' ? 'selected' : null}}>{{__('Ada (Cheapest &amp; Fastest)')}}</option>
										<option value="text-babbage-001" {{$setting->stablediffusion_default_model == 'text-babbage-001' ? 'selected' : null}}>{{__('Babbage')}}</option>
										<option value="text-curie-001" {{$setting->stablediffusion_default_model == 'text-curie-001' ? 'selected' : null}}>{{__('Curie')}}</option>
										-->
										<option value="text-davinci-003" {{$setting->stablediffusion_default_model == 'text-davinci-003' ? 'selected' : null}}>{{__('Davinci (Expensive &amp; Capable)')}}</option>
										<option value="gpt-3.5-turbo" {{$setting->stablediffusion_default_model == 'gpt-3.5-turbo' ? 'selected' : null}}>{{__('ChatGPT (Most Expensive & Fastest & Most Capable)')}}</option>
										<option value="gpt-4" {{$setting->stablediffusion_default_model == 'gpt-4' ? 'selected' : null}}>{{__('ChatGPT-4 (Most Expensive & Fastest & Most Capable)')}}</option>
									</select>
									<div class="bg-blue-100 text-blue-600 rounded-xl !p-3 !mt-2 dark:bg-blue-600/20 dark:text-blue-200">
										<svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
										{{__('Please note GPT-4 is not working with every api_key. You have to have an api key which can work with GPT-4.')}}
										<br>
										{{__('Also please note that Chat models works with ChatGPT and GPT-4 models. So if you choose below it will automatically use ChatGPT.')}}
									</div>
								</div>
							</div> --}}

							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{__('Default stablediffusion Language')}}</label>
									<select class="form-select" name="stablediffusion_default_language" id="stablediffusion_default_language">
										<option value="ar-AE" {{$settings_two->stablediffusion_default_language == 'ar-AE' ? 'selected' : null}}>{{__('Arabic')}}</option>
										<option value="cmn-CN"{{$settings_two->stablediffusion_default_language == 'cmn-CN' ? 'selected' : null}}>{{__('Chinese (Mandarin)')}}</option>
										<option value="hr-HR" {{$settings_two->stablediffusion_default_language == 'hr-HR' ? 'selected' : null}}>{{__('Croatian (Croatia)')}}</option>
										<option value="cs-CZ" {{$settings_two->stablediffusion_default_language == 'cs-CZ' ? 'selected' : null}}>{{__('Czech (Czech Republic)')}}</option>
										<option value="da-DK" {{$settings_two->stablediffusion_default_language == 'da-DK' ? 'selected' : null}}>{{__('Danish (Denmark)')}}</option>
										<option value="nl-NL" {{$settings_two->stablediffusion_default_language == 'nl-NL' ? 'selected' : null}}>{{__('Dutch (Netherlands)')}}</option>
										<option value="en-US" {{$settings_two->stablediffusion_default_language == 'en-US' ? 'selected' : null}}>{{__('English (USA)')}}</option>
										<option value="et-EE" {{$settings_two->stablediffusion_default_language == 'et-EE' ? 'selected' : null}}>{{__('Estonian (Estonia)')}}</option>
										<option value="fi-FI" {{$settings_two->stablediffusion_default_language == 'fi-FI' ? 'selected' : null}}>{{__('Finnish (Finland)')}}</option>
										<option value="fr-FR" {{$settings_two->stablediffusion_default_language == 'fr-FR' ? 'selected' : null}}>{{__('French (France)')}}</option>
										<option value="de-DE" {{$settings_two->stablediffusion_default_language == 'de-DE' ? 'selected' : null}}>{{__('German (Germany)')}}</option>
										<option value="el-GR" {{$settings_two->stablediffusion_default_language == 'el-GR' ? 'selected' : null}}>{{__('Greek (Greece)')}}</option>
										<option value="he-IL" {{$settings_two->stablediffusion_default_language == 'he-IL' ? 'selected' : null}}>{{__('Hebrew (Israel)')}}</option>
										<option value="hi-IN" {{$settings_two->stablediffusion_default_language == 'hi-IN' ? 'selected' : null}}>{{__('Hindi (India)')}}</option>
										<option value="hu-HU" {{$settings_two->stablediffusion_default_language == 'hu-HU' ? 'selected' : null}}>{{__('Hungarian (Hungary)')}}</option>
										<option value="is-IS" {{$settings_two->stablediffusion_default_language == 'is-IS' ? 'selected' : null}}>{{__('Icelandic (Iceland)')}}</option>
										<option value="id-ID" {{$settings_two->stablediffusion_default_language == 'id-ID' ? 'selected' : null}}>{{__('Indonesian (Indonesia)')}}</option>
										<option value="it-IT" {{$settings_two->stablediffusion_default_language == 'it-IT' ? 'selected' : null}}>{{__('Italian (Italy)')}}</option>
										<option value="ja-JP" {{$settings_two->stablediffusion_default_language == 'ja-JP' ? 'selected' : null}}>{{__('Japanese (Japan)')}}</option>
										<option value="kk-KZ" {{$settings_two->stablediffusion_default_language == 'kk-KZ' ? 'selected' : null}}>{{__('Kazakh')}}</option>
										<option value="ko-KR" {{$settings_two->stablediffusion_default_language == 'ko-KR' ? 'selected' : null}}>{{__('Korean (South Korea)')}}</option>
										<option value="ms-MY" {{$settings_two->stablediffusion_default_language == 'ms-MY' ? 'selected' : null}}>{{__('Malay (Malaysia)')}}</option>
										<option value="nb-NO" {{$settings_two->stablediffusion_default_language == 'nb-NO' ? 'selected' : null}}>{{__('Norwegian (Norway)')}}</option>
										<option value="pl-PL" {{$settings_two->stablediffusion_default_language == 'pl-PL' ? 'selected' : null}}>{{__('Polish (Poland)')}}</option>
										<option value="pt-BR" {{$settings_two->stablediffusion_default_language == 'pt-BR' ? 'selected' : null}}>{{__('Portuguese (Brazil)')}}</option>
										<option value="pt-PT" {{$settings_two->stablediffusion_default_language == 'pt-PT' ? 'selected' : null}}>{{__('Portuguese (Portugal)')}}</option>
										<option value="ro-RO" {{$settings_two->stablediffusion_default_language == 'ro-RO' ? 'selected' : null}}>{{__('Romanian (Romania)')}}</option>
										<option value="ru-RU" {{$settings_two->stablediffusion_default_language == 'ru-RU' ? 'selected' : null}}>{{__('Russian (Russia)')}}</option>
										<option value="sl-SI" {{$settings_two->stablediffusion_default_language == 'sl-SI' ? 'selected' : null}}>{{__('Slovenian (Slovenia)')}}</option>
										<option value="es-ES" {{$settings_two->stablediffusion_default_language == 'es-ES' ? 'selected' : null}}>{{__('Spanish (Spain)')}}</option>
										<option value="sw-KE" {{$settings_two->stablediffusion_default_language == 'sw-KE' ? 'selected' : null}}>{{__('Swahili (Kenya)')}}</option>
										<option value="sv-SE" {{$settings_two->stablediffusion_default_language == 'sv-SE' ? 'selected' : null}}>{{__('Swedish (Sweden)')}}</option>
										<option value="tr-TR" {{$settings_two->stablediffusion_default_language == 'tr-TR' ? 'selected' : null}}>{{__('Turkish (Turkey)')}}</option>
										<option value="vi-VN" {{$settings_two->stablediffusion_default_language == 'vi-VN' ? 'selected' : null}}>{{__('Vietnamese (Vietnam)')}}</option>
									</select>
								</div>
							</div>
{{-- 
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{__('Default Tone of Voice')}}</label>
									<select class="form-select" name="stablediffusion_default_tone_of_voice" id="stablediffusion_default_tone_of_voice">
										<option value="Professional" {{$setting->stablediffusion_default_tone_of_voice == 'Professional' ? 'selected' : null}}>{{__('Professional')}}</option>
										<option value="Funny" {{$setting->opena_default_tone_of_voice == 'Funny' ? 'selected' : null}}>{{__('Funny')}}</option>
										<option value="Casual" {{$setting->stablediffusion_default_tone_of_voice == 'Casual' ? 'selected' : null}}>{{__('Casual')}}</option>
										<option value="Excited" {{$setting->stablediffusion_default_tone_of_voice == 'Excited' ? 'selected' : null}}>{{__('Excited')}}</option>
										<option value="Witty" {{$setting->stablediffusion_default_tone_of_voice == 'Witty' ? 'selected' : null}}>{{__('Witty')}}</option>
										<option value="Sarcastic" {{$setting->stablediffusion_default_tone_of_voice == 'Sarcastic' ? 'selected' : null}}>{{__('Sarcastic')}}</option>
										<option value="Feminine" {{$setting->stablediffusion_default_tone_of_voice == 'Feminine' ? 'selected' : null}}>{{__('Feminine')}}</option>
										<option value="Masculine" {{$setting->stablediffusion_default_tone_of_voice == 'Masculine' ? 'selected' : null}}>{{__('Masculine')}}</option>
										<option value="Bold" {{$setting->stablediffusion_default_tone_of_voice == 'Bold' ? 'selected' : null}}>{{__('Bold')}}</option>
										<option value="Dramatic" {{$setting->stablediffusion_default_tone_of_voice == 'Dramatic' ? 'selected' : null}}>{{__('Dramatic')}}</option>
										<option value="Grumpy" {{$setting->stablediffusion_default_tone_of_voice == 'Grumpy' ? 'selected' : null}}>{{__('Grumpy')}}</option>
										<option value="Secretive" {{$setting->stablediffusion_default_tone_of_voice == 'Secretive' ? 'selected' : null}}>{{__('Secretive')}}</option>
									</select>
								</div>
							</div> --}}

						</div>
						<button form="settings_form" id="settings_button" class="btn btn-primary w-100">
							{{__('Save')}}
						</button>
					</form>
				</div>
			</div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/assets/js/panel/settings.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
