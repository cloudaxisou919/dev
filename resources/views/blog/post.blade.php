@extends('blog.app')

@section('content')

@include('blog.hero')


<section class="page-content">
    <div class="container mb-20">

        @if(isset($post->feature_image) && !empty($post->feature_image) )
            <div class="feature-image mb-10 mx-auto">
                <img class="w-full rounded-3xl" src="/{{$post->feature_image}}" alt="{{$post->title}}">
            </div>
        @endif
        <div class="content lg:w-9/12 w-full mx-auto">
            {!! $post->content !!}
        </div>
        
        @include('blog.part.tag-share')
        <hr class="mt-10 mb-10">
        @include('blog.part.author')
        <hr class="mt-10 mb-10">
        @include('blog.part.prev-next')
        @include('blog.part.related')
    </div>
</section>

@endsection
