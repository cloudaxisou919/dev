@foreach($chat->messages as $message)
@if($message->input != null)
    <div class="lqd-chat-user-bubble flex flex-row-reverse content-end mb-2 lg:ms-auto gap-[8px]">
        <span class="text-dark">
            <span class="avatar w-[24px] h-[24px] shrink-0" style="background-image: url(/{{Auth::user()->avatar}})"></span>
        </span>
        <div class="max-w-[calc(100%-64px)] border-none rounded-[2em] mb-[7px] bg-[#F3E2FD] text-[#090A0A] dark:bg-[rgba(var(--tblr-primary-rgb),0.3)] dark:text-white">
            <div class="chat-content py-[0.75rem] px-[1.5rem]">
                {{$message->input}}
            </div>
        </div>
    </div>
@endif

<div class="lqd-chat-ai-bubble flex content-start mb-2 gap-[8px]">
	<span class="text-dark">
		<span class="avatar w-[24px] h-[24px] shrink-0" style="background-image: url('/{{$chat->category->image ?? 'assets/img/auth/default-avatar.png'}}')"></span>
	</span>
	<div class="chat-content-container max-w-[calc(100%-64px)] border-none rounded-[2em] mb-[7px] relative bg-[#E5E7EB] text-[#090A0A] dark:bg-[rgba(255,255,255,0.02)] dark:text-white group">
		<pre class="chat-content py-[0.75rem] px-[1.5rem] bg-transparent text-inherit font-[inherit] text-[1em] indent-0 m-0 w-full whitespace-pre-wrap">{!!  $message->output !!}</pre>
		<button class="lqd-clipboard-copy inline-flex items-center justify-center w-10 h-10 p-0 border-none absolute bottom-0 -end-5 bg-white rounded-full text-black !shadow-lg pointer-events-auto opacity-0 invisible transition-all hover:-translate-y-[2px] hover:scale-110 group-hover:!opacity-100 group-hover:!visible" title="{{__('Copy to clipboard')}}" data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'>
			<span class="sr-only">{{__('Copy to clipboard')}}</span>
			<svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 96 960 960" fill="currentColor" width="20"> <path d="M180 975q-24 0-42-18t-18-42V312h60v603h474v60H180Zm120-120q-24 0-42-18t-18-42V235q0-24 18-42t42-18h440q24 0 42 18t18 42v560q0 24-18 42t-42 18H300Zm0-60h440V235H300v560Zm0 0V235v560Z"/> </svg>
		</button>
	</div>
</div>
@endforeach
@if(count($chat->messages) == 0)
<div class="flex content-end mb-2">
	<div class="border w-full-none rounded-[2em] bg-[#F3E2FD] text-[#090A0A] dark:bg-[rgba(255,255,255,0.02)] dark:text-white">
		<div class="chat-content py-[0.75rem] px-[1.5rem]">
			{{__('You have no message... Please start typing.')}}
		</div>
	</div>
</div>
@endif<?php
