@extends('panel.layout.app')
@section('title', __('Add or Edit Post'))
@section('additional_css')
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="page-header" xmlns="http://www.w3.org/1999/html">
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
						<a href="{{route('dashboard.blog.list')}}" class="page-pretitle flex items-center">
							/ {{__('Blog Posts')}}
						</a>
					</div>
                    <h2 class="page-title mb-2">
                        {{__('Add or Edit Post')}}
                    </h2>
                </div>
				<div class="col col-12 col-lg-6">
					<div class="flex space-x-1 lg:justify-end">
						@if($blog!=null)
						<a href="{{ LaravelLocalization::localizeUrl( url('/blog', $blog->slug) ) }}" target="_blank" class="btn btn-default">
							{{__('Preview')}}
						</a>
						@endif
						<button type="submit" form="post_form" id="post_button" class="btn btn-primary">
							{{__('Save')}}
						</button>
					</div>
				</div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
			<form id="post_form" onsubmit="return blogSave({{$blog!=null ? $blog->id : null}});" action="" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-7 mx-auto">
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('Post Title')}}
								<x-info-tooltip text="{{__('Add a post title.')}}" />
							</label>
							<input type="text" class="form-control" id="title" name="title" value="{{$blog!=null ? $blog->title : null}}">
						</div>
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('Content')}}
								<x-info-tooltip text="{{__('A short description of what this chat template can help with.')}}" />
							</label>
							<textarea class="form-control" id="content" name="content">{{$blog!=null ? $blog->content : null}}</textarea>
						</div>
					</div>

					<div class="col-md-4 mx-auto">

						<div class="mb-[20px]">
							<div class="vstack gap-1">
								<label class="form-label">{{__('Post Image')}}</label>
								<img class="preview border rounded-lg mb-[20px] @if($blog==null || ($blog!=null && !$blog->feature_image)){{'hidden'}}@endif" alt="{{$blog!=null ? $blog->title : __('preview')}}" src="/{{$blog!=null ? $blog->feature_image : null}}">
								<input type="file" class="form-control" id="feature_image" name="feature_image" value="/{{$blog!=null ? $blog->feature_image : null}}" accept=".jpg, .jpeg, .png, .webp">
							</div>
						</div>

						<div class="mb-[20px]">
							<label class="form-label">{{__('Post Status')}}</label>
							<select id="status" name="status" class="form-control">
								<option value="0" {{$blog!=null && $blog->status == 0 ? 'selected' : ''}} >{{__('Draft')}}</option>
								<option value="1" {{$blog!=null && $blog->status == 1 ? 'selected' : ''}} >{{__('Publish')}}</option>
							</select>
						</div>

						<div class="form-control border-none p-0 mb-[20px] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em] [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius]">
							<label class="form-label">
								{{__('Category')}}
								<x-info-tooltip text="{{__('Categories of the post. Useful for filtering in the blog posts.')}}" />
							</label>
							<select class="form-control select2" name="category" id="category" multiple>
								@if($blog!=null && $blog->category)
									@foreach (explode(',', $blog->category) as $cat)
										<option value="{{$cat}}" selected>{{$cat}}</option>
									@endforeach
								@endif
							</select>
						</div>

						<div class="form-control border-none p-0 mb-[20px] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em] [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius]">
							<label class="form-label">
								{{__('Tag')}}
								<x-info-tooltip text="{{__('Categories of the post. Useful for filtering in the blog posts.')}}" />
							</label>
							<select class="form-control select2" name="tag" id="tag" multiple>
								@if($blog!=null && $blog->tag)
									@foreach (explode(',', $blog->tag) as $tag)
										<option value="{{$tag}}" selected>{{$tag}}</option>
									@endforeach
								@endif
							</select>
						</div>

						<h3 class="mt-[40px] mb-[20px]">{{__('SEO')}}</h3>
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('SEO Title')}}
								<x-info-tooltip text="{{__('If you will leave empty: using the post title for the SEO')}}" />
							</label>
							<input type="text" class="form-control" id="seo_title" name="seo_title" value="{{$blog!=null ? $blog->seo_title : null}}">
						</div>
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('Slug')}}
								<x-info-tooltip text="{{__('Add Slug for SEO. Example: my-post')}}" />
							</label>
							<input type="text" class="form-control" id="slug" name="slug" value="{{$blog!=null ? $blog->slug : null}}">
						</div>
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('SEO Description')}}
								<x-info-tooltip text="{{__('A short description of what this chat template can help with for SEO')}}" />
							</label>
							<textarea class="form-control" id="seo_description" name="seo_description">{{$blog!=null ? $blog->seo_description : null}}</textarea>
						</div>
					</div>
				</div>
			</form>
        </div>
    </div>

@endsection

@section('script')
	<script src="/assets/js/panel/blog.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script src="/assets/libs/tinymce/tinymce.min.js"></script>
	<script>
		$(document).ready(function() {
			$('.select2').select2({
				tags: true
			});
		});
		tinymce.init({
			selector: '#content',
			height: '610',
			plugins: 'quickbars advlist link image lists',
			//toolbar:'advlist link image lists'
			toolbar:'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | lists | indent outdent | image',
  			quickbars_insert_toolbar: false
		});
	</script>

@endsection
