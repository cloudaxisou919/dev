<div class="flex space-x-6 items-center">
    <div>
        <img class="rounded-full" width="80" height="80" src="@if( !App\Models\User::where('id', $post->user_id)->first()->github_token && !App\Models\User::where('id', $post->user_id)->first()->google_token && !App\Models\User::where('id', $post->user_id)->first()->facebook_token )/@endif{{App\Models\User::where('id', $post->user_id)->first()->avatar;}}">
    </div>
    <div class="flex flex-col">
        <a class="text-black font-semibold" href="{{ url('/blog/author', $post->user_id) }}">{{App\Models\User::where('id', $post->user_id)->first()->name;}}</a>
    </div>
</div>