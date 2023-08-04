@extends('panel.layout.app')
@section('title', __('Manage Languages'))

@section('content')
<div class="page-header">
	<div class="container-xl">
		<div class="row g-2 items-center">
			<div class="col col-12 col-lg-6">
				<div class="hstack gap-1">
					<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}" class="page-pretitle flex items-center">
						<svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
						</svg>
						{{__('Back to dashboard')}}
					</a>
					<a href="{{LaravelLocalization::localizeUrl( route('amamarul.translations.home') )}}" class="page-pretitle flex items-center">
						/ {{__('Manage Languages')}}
					</a>
				</div>
				<h2 class="page-title mb-2">
					{{__('Manage Languages')}}
				</h2>
			</div>
			<div class="col col-12 col-lg-6">
				<div class="flex">
					@if(!activeRoute('amamarul.translations.lang'))
						<a href="{{route('amamarul.translations.lang.reinstall')}}" class="btn btn-default flex space-x-2 mr-2 lg:ml-auto">
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M19.933 13.041a8 8 0 1 1 -9.925 -8.788c3.899 -1 7.935 1.007 9.425 4.747"></path><path d="M20 4v5h-5"></path></svg>
							{{__('Reinstall Language Files')}}
						</a>
					@else 

					<button @if(env('APP_STATUS') == 'Demo') type="button" onclick="return toastr.info('This feature is disabled in Demo version.')" @else type="submit" @endif class="btn btn-default flex space-x-2 mr-2 lg:ml-auto" onClick="return handlePromptInput()">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<path d="M12 5l0 14"></path>
							<path d="M5 12l14 0"></path>
						</svg>
						{{'New String'}}
					</button>

					<form action="{{ route('amamarul.translations.lang.newString') }}" class="relative hidden" id="new-string-form" method="GET">
						<input type="text" class="form-control rounded-full bg-[#F1EDFF]" name="newString" id="new-string" placeholder="{{__('Add new string. Ex. Hello')}}">
					</form>
					
					<script>
						function handlePromptInput() {
							var newString = prompt('Enter the new string'); 
							if (newString) {
								document.getElementById('new-string').value = newString;
								document.getElementById("new-string-form").submit(); 
							}
						}
					</script>
					
					@endif

					<a @if(env('APP_STATUS') == 'Demo') href="javascript:void(0);" onclick="return toastr.info('This feature is disabled in Demo version.')" @else href="{{route('amamarul.translations.lang.publishAll')}}" @endif class="btn btn-primary flex space-x-2">
						<svg class="ml-2" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8"></path><path d="M3 10h18"></path><path d="M10 3v18"></path><path d="M16 22l5 -5"></path><path d="M21 21.5v-4.5h-4.5"></path></svg>
						{{__('Publish All JSON Files')}}
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Page body -->
<div class="page-body pt-6">
	<div class="container-xl">
		<div class="col-md-8 mx-auto">
            @include('langs::includes.nav')
            @include('langs::includes.messages')

            @yield('content_translation')

		</div>
	</div>
</div>
@endsection
@section('script')

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
	<script>
		$(document).ready(function() {
			"use strict";
			$('.lang-switcher .form-check-input').click(function() {
				var demo = @json( env('APP_STATUS') == 'Demo' ? true : false );
				if ( demo == true ){
					toastr.info('This feature is disabled in Demo version.');
					return false;
				}
				var formData = new FormData();
				formData.append( 'lang', $(this).attr('id') );
				formData.append( 'state', $(this).prop('checked') ? 1 : 0 );
				console.log(formData);

				$.ajax( {
					type: "post",
					url: "/translations/lang-save",
					data: formData,
					contentType: false,
					processData: false,
					success: function ( data ) {
						toastr.success( 'Saved. Redirecting...' );
						setTimeout( function () {
							location.href = '/dashboard/admin/translations';
						}, 1000 );
					},
					error: function ( data ) {
						var err = data.responseJSON.errors;
						$.each( err, function ( index, value ) {
							toastr.error( value );
						} );
					}
				} );
		
			});
		});
	</script>
    @yield('scripts')
@endsection
