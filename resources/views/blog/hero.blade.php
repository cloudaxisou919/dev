<section class="flex items-center justify-center min-h-[200px] text-center text-black relative pt-52 pb-28 max-md:pb-16 max-md:pt-48 overflow-hidden" id="banner">

	<div class="container relative">
		<div class="max-lg:w-2/3 max-md:w-full flex flex-col items-center w-1/2 mx-auto">
			<div class="banner-title-wrap relative">
				@if(isset($post->category))
					@php $cat = explode(',', $post->category); @endphp
					<a class="text-black font-normal" href="{{ url('/blog/category', $cat[0]) }}">
						<span class="px-4 py-1 bg-gradient-to-r from-purple-100 via-purple-200 to-slate-200 rounded-md">
							{{$cat[0]}}
						</span>
					</a>
				@elseif(isset($hero['subtitle']))
					<span class="px-4 py-1 bg-gradient-to-r from-purple-100 via-purple-200 to-slate-200 rounded-md">{{__($hero['subtitle'])}}</span>
				@endif
				<h1
					class="
					text-[55px]
				    font-golos -tracking-wide font-semibold text-black mb-8 mt-4
					opacity-0 transition-all ease-out translate-y-7
					group-[.page-loaded]/body:opacity-100 group-[.page-loaded]/body:translate-y-0">
                    @if(isset($post->title))
					    {{$post->title}}
                    @elseif(isset($hero['title']))
						@if( $hero['type'] == 'author' )
							{{ucfirst(App\Models\User::where('id', $hero['title'])->first()->name)}}
                        @else
							{{$hero['title']}}
						@endif
                    @else
                        {{__('Blog Posts')}}
                    @endif
				</h1>
				@if(isset($hero['description']))
					<p class="text-[20px] font-medium text-[#0E3F58]">
						{{__($hero['description'])}}
					</p>
				@endif
				@if(isset($post->seo_description))
					<p class="text-[20px] font-medium text-[#0E3F58]">
						{{$post->seo_description}}
					</p>
				@endif
			</div>
		</div>
	</div>
</section>