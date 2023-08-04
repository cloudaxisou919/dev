@extends('blog.app')

@section('content')

@include('blog.hero')

<section class="page-content">
    <div class="container mb-20">
		<div class="grid gap-14 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mb-16">
			@foreach ($posts as $post)
				@include('blog.part.card')
			@endforeach
		</div>
		{{$posts->links('pagination::bootstrap-5-alt')}}
    </div>
</section>

@endsection
