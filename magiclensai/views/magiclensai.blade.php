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
{{-- <script src="/assets/libs/fslightbox/index.js?1674944402" defer></script> --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
   
   <script>
        var id;
        var messageId;
        function disableNewProgress(){
            $(".variation").prop("disabled", true);
            $(".generate-button").prop("disabled", true);
            
        }
        function enableNewProgress(){
            $(".variation").prop("disabled", false);
            $(".generate-button").prop("disabled", false);
            $(".variation").html("Variation");
            $(".generate-button").html("Regenerate");
        }

        jQuery(document).ready(function(){
			
			async function toDataURL(url) {
				const blob = await fetch(url).then(res => res.blob());
				return URL.createObjectURL(blob);
			}

			async function download(image, download_name) {
				const a = document.createElement("a");
				a.href = await toDataURL(image);
				a.download = download_name;
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			}
			
			jQuery(document).on("click",".download", function(e){
				e.preventDefault();
				// var image = $(this).data("download");
				var image = $(this).attr("href");
				var download_name = $(this).attr("download");
				download(image, download_name);
			});			
		});

        $(document).ready(function() {

            $(document).on("click", ".variation", function(e) {
                e.preventDefault();
                disableNewProgress();

                dataIdValue = $(this).data("id");
                dataIndexValue = $(this).data("index");
                id = $(this).data("variation");
                $.ajax({
                    type: "post",
                    url: "/dashboard/user/magiclensai/button",
                    contentType: "application/json",
                    data: JSON.stringify({
                        button: "V"+ dataIndexValue,
                        buttonMessageId: dataIdValue
                    }),
                    success: function(data) {
                        toastr.info('Please wait for the response. It may take 4-5 minutes.');
                        setTimeout(function() {
                        if (data.messageId && data.loadBalanceId) {
                            var result = checkProgress(data.messageId,id,data.loadBalanceId);
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
        function checkProgress(messageId, id = null,loadBalanceId){
            disableNewProgress();
            document.getElementById("openai_generator_button").disabled = true;
            document.getElementById("openai_generator_button").innerHTML = "Please Wait";
			document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
            var url = "/dashboard/user/magiclensai/generate/" + messageId;
            $.ajax({
                type: "get",
                url:  url,
                dataType:"json",
                data:{id:id,loadBalanceId:loadBalanceId},
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
                            checkProgress(messageId,id,loadBalanceId);
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
                url: "/dashboard/user/magiclensai/generate",
                contentType: 'application/json',
                data: JSON.stringify({
                    "msg": value
                }),
                success: function (data) {
                    setTimeout(function () {
                        if(data.status === 400){
                            toastr.error(data.errors);
                            enableNewProgress();
                        }else if(data.messageId && data.loadBalanceId){
                            toastr.options = {
                            "timeOut": 20000,
                            "fadeOutDuration": 20000
                            };
                            toastr.info('Please wait for the response. It may take 4-5 minutes.');
                            var result = checkProgress(data.messageId,id,data.loadBalanceId);
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
