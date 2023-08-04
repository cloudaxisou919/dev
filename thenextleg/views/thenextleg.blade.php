@extends('panel.layout.app')
@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Create stunning images in seconds.
                    </div>
                    <h2 class="page-title mb-2">
                        AI Image Generator
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body page-generator pt-6">
        <div class="container-xl">
                @include('image')
        </div>
    </div>
@endsection
@section('script')
    <script>
        function disableNewProgress(){
            $(".variation").prop("disabled", true);
            $(".upscale").prop("disabled", true);
            $(".generate-button").prop("disabled", true);
            
        }
        function enableNewProgress(){
            $(".variation").prop("disabled", false);
            $(".upscale").prop("disabled", false);
            $(".generate-button").prop("disabled", false);
            $(".upscale").html("Upscale");
            $(".variation").html("Variation");
            $(".generate-button").html("Regenerate");
        }

        $(document).ready(function() {
            $(document).on("click", ".upscale", function(e) {
                e.preventDefault();
                disableNewProgress();

                dataIdValue = $(this).data("id");
                dataIndexValue = $(this).data("index");
                $.ajax({
                    type: "post",
                    url: "/dashboard/user/thenextleg/button",
                    contentType: "application/json",
                    data: JSON.stringify({
                        button: "B"+ dataIndexValue,
                        buttonMessageId: dataIdValue
                    }),
                    success: function(data) {
                        // toastr.success('Please wait for the response.');
                        setTimeout(function() {
                        if (data.messageId) {
                            var result = upscaleImage(data.messageId);
                        } else {
                            enableNewProgress();
                            toastr.error("messageId is missing or null.");
                        }
                        }, 750);
                    },
                    error: function(data) {
                        if (data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function(index, value) {
                            toastr.error(value);
                        });
                        } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                        }
                    },
                    complete: function() {
                        upscaleButton.prop("disabled", false);
                        upscaleButton.html("Upscale");
                    }
                });
            });
        });

        $(document).ready(function() {
            $(document).on("click", ".variation", function(e) {
                e.preventDefault();
                disableNewProgress();

                dataIdValue = $(this).data("id");
                dataIndexValue = $(this).data("index");
                $.ajax({
                    type: "post",
                    url: "/dashboard/user/thenextleg/button",
                    contentType: "application/json",
                    data: JSON.stringify({
                        button: "V"+ dataIndexValue,
                        buttonMessageId: dataIdValue
                    }),
                    success: function(data) {
                        toastr.info('Please wait for the response. It may take 4-5 minutes.');
                        setTimeout(function() {
                        if (data.messageId) {
                            var result = checkProgress(data.messageId);
                        } else {
                            enableNewProgress();
                            toastr.error("messageId is missing or null.");
                        }
                        }, 750);
                    },
                    error: function(data) {
                        if (data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function(index, value) {
                            toastr.error(value);
                        });
                        } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                        }
                    }
                });
            });
        });
        function checkProgress(messageId){
            disableNewProgress();
            document.getElementById("openai_generator_button").disabled = true;
            document.getElementById("openai_generator_button").innerHTML = "Please Wait";
			document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
            $.ajax({
                type: "get",
                url:  "/dashboard/user/thenextleg/generate/"+messageId,
                dataType:"json",
                success: function (data){
                    if(data.progress == 100 ){
                        toastr.success('Generated Successfully!');
                        document.getElementById("openai_generator_button").disabled = false;
                        document.getElementById("openai_generator_button").innerHTML = "Regenerate";
                        document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
                        
                        $("#append-images").prepend(data.data);
                        enableNewProgress();
                        return data;
                            
                    }else if(data.progress == 'incomplete'){
                        enableNewProgress();
                        document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
                        toastr.error('Progress incomplete. Please try again after some time.');
                    }
                    else{
                        setTimeout(function(){
                            checkProgress(messageId);
                        }, 10000 );
                    }
                },
                error: function (data) {
                    if ( data.responseJSON.errors ) {
						$.each(data.responseJSON.errors, function(index, value) {
							toastr.error(value);
						});
					} else if ( data.responseJSON.message ) {
						toastr.error(data.responseJSON.message);
					}
                    document.getElementById("openai_generator_button").disabled = false;
                    document.getElementById("openai_generator_button").innerHTML = "Generate";
					document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
					document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                }
            });
            return false;
        }
        function upscaleImage(messageId){
            disableNewProgress();
			document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
            $.ajax({
                type: "post",
                url:  "/dashboard/user/thenextleg/upscale/"+messageId,
                dataType:"json",
                success: function (data){
                    if (data.progress == 100 && data.data.original.response.length > 0  ) {
                        toastr.success("Upscaled Successfully!");
                        document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');

                        $("#append-images").prepend(data.data);
                        enableNewProgress();
                    } else if (data.progress === 'incomplete') {
                        enableNewProgress();
                        document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
                        setTimeout(function() {
                            upscaleImage(messageId);
                        }, 10000);
                    }else {
                        enableNewProgress();
                        toastr.info("Image is already upscaled!");
                        document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
                    }
                },
                error: function (data) {
                    if ( data.responseJSON.errors ) {
						$.each(data.responseJSON.errors, function(index, value) {
							toastr.error(value);
						});
					} else if ( data.responseJSON.message ) {
						toastr.error(data.responseJSON.message);
					}
					document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
					document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                }
            });
            return false;
        }
        function sendOpenaiGeneratorForm(ev) {
			"use strict";

			ev?.preventDefault();
			ev?.stopPropagation();
            disableNewProgress();
            document.getElementById("openai_generator_button").disabled = true;
            document.getElementById("openai_generator_button").innerHTML = "Please Wait";
			document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
            var value = document.getElementById('description').value;
            if(value){
            $.ajax({
                type: "post",
                url: "/dashboard/user/thenextleg/generate",
                contentType: 'application/json',
                data: JSON.stringify({
                    "msg": value
                }),
                success: function (data) {
                    setTimeout(function () {
                        if(data.status === 400){
                            toastr.error(data.errors);
                            enableNewProgress();
                        }else if(data.messageId){
                            toastr.options = {
                            "timeOut": 20000,
                            "fadeOutDuration": 20000
                            };
                            toastr.info('Please wait for the response. It may take 4-5 minutes.');
                            var result = checkProgress(data.messageId);
                        }else {
                            toastr.error("messageId is missing or null.");
                        }
                    }, 750);
                },
                error: function (data) {
                    if ( data.responseJSON.errors ) {
						$.each(data.responseJSON.errors, function(index, value) {
							toastr.error(value);
						});
					} else if ( data.responseJSON.message ) {
						toastr.error(data.responseJSON.message);
					}
                    document.getElementById("openai_generator_button").disabled = false;
                    document.getElementById("openai_generator_button").innerHTML = "Genarate";
					document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
					document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                    enableNewProgress();
                }
                
            });
            }
            return false;
        }
    </script>
@endsection
