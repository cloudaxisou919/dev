<div class="col-6 custom-container">
    <div class="row custom-row bg-style" >
        @foreach($thenextlegImagesArrays as $entry)
        @php
        $image;
        $settings_two = App\Models\SettingTwo::first();
        $image_storage = $settings_two->ai_image_storage;
        $is_cdn_path = false;
        if ($image_storage == App\Http\Controllers\AIController::STORAGE_S3) {
            if(isset($entry['images']->aws_path) && $entry['images']->aws_path != null ){
                $path = $entry['images']->aws_path;
                $image = $path;
            }elseif(isset($entry['images']->image_local_path) && $entry['images']->image_local_path != null && file_exists( public_path() . "/" . $entry['images']->image_local_path ) ){
                $path = $entry['images']->image_local_path;
                $image = URL::to('/').'/'.$path;
            }else{
                $image = $entry['images']->image_path;
                $is_cdn_path = true;
            }
        }else{
            if(isset($entry['images']->image_local_path) && $entry['images']->image_local_path != null && file_exists( public_path() . "/" . $entry['images']->image_local_path ) ){
                $path = $entry['images']->image_local_path;
                $image = URL::to('/').'/'.$path;
            }elseif(isset($entry['images']->aws_path) && $entry['images']->aws_path != null ){
                $path = $entry['images']->aws_path;
                $image = $path;
            }else{
                $image = $entry['images']->image_path;
                $is_cdn_path = true;
            }
        }
        @endphp
                <div class="col-6 mb-2 mt-2 ">
                    <div class="image-result group">
                        <div class="relative aspect-square rounded-lg mb-2 overflow-hidden group-hover:shadow-lg transition-all">
                                <img src="{{$image}}"  class="w-full h-full aspect-square object-cover object-center img-fluid" >
                                <div class="flex items-center justify-center gap-2 w-full h-full absolute top-0 left-0 opacity-0 transition-opacity group-hover:!opacity-100">
                                    <div class="flex flex-col">
                                        <div>
                                            <a href="<?php echo $is_cdn_path == true ? $image : route('download.image')."/?url=".$image; ?>" <?php echo route('download.image')."/?url=".$image ?> target="_blank" class="btn items-center justify-center w-9 h-9 p-0 <?php if( $is_cdn_path == true){ ?>download<?php } ?>">
                                                <svg width="8" height="11" viewBox="0 0 8 11" fill="var(--lqd-heading-color)" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.57422 0.5V8.75781L6.67969 6.67969L7.5 7.5L4 11L0.5 7.5L1.32031 6.67969L3.42578 8.75781V0.5H4.57422Z"/>
                                                </svg>
                                            </a>
                                            <a data-fslightbox="gallery" href="{{$image}}" target="_blank" class="btn lb items-center justify-center w-9 h-9 p-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                                </svg>
                                            </a> 
                                            <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.magiclensai.image.delete', ['id' => $entry['images']->id])) }}" onclick="return confirm('Are you sure?')" class="btn items-center justify-center w-9 h-9 p-0">
                                                <svg width="10" height="9" viewBox="0 0 10 9" fill="var(--lqd-heading-color)" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.08789 1.49609L5.80664 4.75L9.08789 8.00391L8.26758 8.82422L4.98633 5.57031L1.73242 8.82422L0.912109 8.00391L4.16602 4.75L0.912109 1.49609L1.73242 0.675781L4.98633 3.92969L8.26758 0.675781L9.08789 1.49609Z"/>
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="btn-group mt-3 mb-3 justify-center" role="group"><a href="{{route('dashboard.user.magiclensai.generator', 'ai_image_generator')}}">
                                                <button class="btn btn-light btn-default variation" data-id= "{{$entry['button_message_id']}}" data-index = "{{$entry['image_index']}}" data-variation="{{$entry['images']->id}}" autofocus="autofocus" id= "openai_generator_button" style="background-color: #330582;color:white" >
                                                   Variation
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        @endforeach
        <?php
       
        foreach ($thenextlegImagesArrays as $key=>$entry) { 
            if($key == 0){
            ?>
              
        <p class="mt-3 text-muted text-center mb-4 mx-auto">
                {{ $entry['images']['created_at']->diffForHumans() }}
            </p>
       <?php  }}  ?>
    </div>
 </div>
 
