@if ($openai->type == 'image')
    <template id="image_result">
        <div class="col-6 col-md-4 col-xl-2 mb-8">
            <div class="image-result group">
                <div class="relative aspect-square rounded-lg mb-2 overflow-hidden group-hover:shadow-lg transition-all">
                    <img src="" class="w-full h-full aspect-square object-cover object-center">
                    <span class="badge bg-white text-red"></span>
                    <div class="flex items-center justify-center gap-2 w-full h-full absolute top-0 left-0 opacity-0 transition-opacity group-hover:!opacity-100">
                        <a href="" class="btn items-center justify-center w-9 h-9 p-0 download" download>
                            <svg width="8" height="11" viewBox="0 0 8 11" fill="var(--lqd-heading-color)" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.57422 0.5V8.75781L6.67969 6.67969L7.5 7.5L4 11L0.5 7.5L1.32031 6.67969L3.42578 8.75781V0.5H4.57422Z"/>
                            </svg>
                        </a>
                        <a data-fslightbox="gallery" href="" class="btn lb items-center justify-center w-9 h-9 p-0 gallery">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                            </svg>
                        </a>
                        <a href="" onclick="return confirm('Are you sure?')" class="btn items-center justify-center w-9 h-9 p-0 delete">
                            <svg width="10" height="9" viewBox="0 0 10 9" fill="var(--lqd-heading-color)" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.08789 1.49609L5.80664 4.75L9.08789 8.00391L8.26758 8.82422L4.98633 5.57031L1.73242 8.82422L0.912109 8.00391L4.16602 4.75L0.912109 1.49609L1.73242 0.675781L4.98633 3.92969L8.26758 0.675781L9.08789 1.49609Z"/>
                            </svg>
                        </a>

                    </div>
                </div>
                <p class="w-full overflow-ellipsis whitespace-nowrap text-heading mb-1 overflow-hidden" title=""></p>
                <p class="mb-0 text-muted"></p>
            </div>
        </div>
    </template>
    <div class="col-12">
        <div class="w-full">
            <h2 class="mb-3">{{__('Result')}}</h2>
            <div class="image-results row">
            </div>
        </div>
    </div>
@elseif($openai->type == 'voiceover')
<div class="table-responsive">
    <table class="table card-table">
        <thead>
        <tr>
            <th>{{__('File')}}</th>
            <th>{{__('Language')}}</th>
            <th>{{__('Voice')}}</th>
            <th>{{__('Date')}}</th>
            <th>{{__('Play')}}</th>
            <th>{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($userOpenai as $entry)
            <tr class="text-[13px]">
                <td>{{$entry->title}}</td>
                <td class="text-[11px]">
					<span class="inline-block bg-black/[0.06] px-[6px] py-[3px] rounded-sm dark:bg-white/[0.06]">
						@foreach(array_unique(json_decode($entry->response)->language) as $lang)
							{{country2flag(explode("-", $lang)[1])}}
						@endforeach
						{{$lang}}
					</span>
                </td>
                <td>
                    @foreach(array_unique(json_decode($entry->response)->voices) as $voice)
                        {{getVoiceNames($voice)}}
                    @endforeach
                </td>
                <td>
					<span>{{$entry->created_at->format('M d, Y')}}, <span class="opacity-60">{{$entry->created_at->format('H:m')}}</span></span>
				</td>
                <td class="data-audio" data-audio="/uploads/{{$entry->output}}">
					<div class="audio-preview"></div>
                </td>
                <td>
                    <a href="/uploads/{{$entry->output}}" target="_blank" class="btn relative z-10 w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('View and edit')}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                            <path d="M7 11l5 5l5 -5"></path>
                            <path d="M12 4l0 12"></path>
                        </svg>
                    </a>
                    <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.user.openai.documents.image.delete', $entry->slug)) }}" onclick="return confirm('Are you sure?')" class="btn relative z-10 p-0 border w-[36px] h-[36px] hover:bg-red-600 hover:text-white" title="{{__('Delete')}}">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"/>
                        </svg>
                    </a>
                </td>

            </tr>
        @endforeach
        @if(count($userOpenai) == 0)
            <tr>
                <td colspan="6">{{__('No entries created yet.')}}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@else
<div class="table-responsive">
    <table
        class="table table-vcenter card-table">
        <thead>
        <tr>
            <th>{{__('Type')}}</th>
            <th>{{__('Result')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($userOpenai as $entry)
            <tr>
                <td class="text-muted">
                    <span class="avatar w-[43px] h-[43px] [&_svg]:w-[20px] [&_svg]:h-[20px]" style="background: {{$entry->generator->color}}">
						@if ( $entry->generator->image !== 'none' )
						{!! html_entity_decode($entry->generator->image) !!}
						@endif
					</span>
                </td>
                @if($openai->type == 'text')
                    <td>
                        {!! $entry->output !!}
                    </td>
                @elseif($openai->type == 'code')
                    <td>
                        <div class="min-h-full border-solid border-t border-r-0 border-b-0 border-l-0 border-[var(--tblr-border-color)] pt-[30px] mt-[15px]">
                            <pre id="code-pre" class="line-numbers min-h-full [direction:ltr]"><code id="code-output">{{$entry->output}}</code></pre>
                        </div>
                    </td>
                @else
                    <td>
                        {{$entry->output}}
                    </td>
                @endif
            </tr>
        @endforeach
        @if(count($userOpenai) == 0)
            <tr>
                <td colspan="2">{{__('No entries created yet.')}}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@endif
@if($openai->slug=='ai_image_generator')
    <script>
            function lazyLoadImages(offset) {
                fetch(`{{ route('dashboard.user.openai.lazyloadimage')}}?offset=${offset}&post_type={{$openai->slug}}`)
                .then(response => response.json())
                .then(data => {
                    const images = data.images;
                    const hasMore = data.hasMore;
                    const imageContainer = document.querySelector('.image-results');
                    // Append images to the DOM or perform any other action
                    images.forEach(image => {
                        const imageResultTemplate = document.querySelector( '#image_result' ).content.cloneNode( true );
                        imageResultTemplate.querySelector('.image-result img').setAttribute('src', image.output);
                        imageResultTemplate.querySelector('.image-result span').innerHTML = image.response == "SD" ? "SD" : "DE";
                        imageResultTemplate.querySelector('.image-result span').setAttribute('class', image.response == "SD" ? "badge bg-blue text-white" : "badge bg-white text-red") 
                        imageResultTemplate.querySelector('.image-result a.download').setAttribute('href', image.output);
                        imageResultTemplate.querySelector('.image-result a.gallery').setAttribute('href', image.output);
                        const currenturl = window.location.href;
                        const server = currenturl.split('/')[0];
                        const delete_url = `${server}/dashboard/user/openai/documents/delete/image/${image.slug}`;
                        imageResultTemplate.querySelector('.image-result a.delete').setAttribute('href', delete_url);
                        imageResultTemplate.querySelector('.image-result a.download').setAttribute('href', image.output);
                        imageResultTemplate.querySelector('.image-result p.text-heading').setAttribute('title', image.input);
                        imageResultTemplate.querySelector('.image-result p.text-heading').innerHTML = image.input;
                        imageResultTemplate.querySelector('.image-result p.text-muted').innerHTML = '';
                        imageContainer.append(imageResultTemplate);
                    });

                    // Update the offset for the next lazy loading request
                    offset += images.length;
                    // Check if there are more images to load
                    if (hasMore) {
                        // Call the lazyLoadImages function recursively to load the next set of images
                        lazyLoadImages(offset);
                    }
                });
            }
            // document.addEventListener("DOMContentLoaded", function() {
            //     lazyLoadImages(0);
            // });
            lazyLoadImages(0);
    </script>
@endif
