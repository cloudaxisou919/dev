@extends('panel.layout.app')
@section('title', 'My Affiliates')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 items-center flex justify-between">
                <div class="col">
                    <a href="/dashboard" class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('Advertis Edit') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <h2>{{ __('Advertis Edit') }}</h2>
            <div class="card">
                <div id="table-default-2" class="card-table table-responsive">
                    <form class="p-4 w-1/2 m-auto" method="POST" action="{{ route('dashboard.admin.advertis.update', $advertis) }}">
                        @method('PUT')
                        <div class="flex flex-col gap-4">
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Key</label>
                                <input type="text" name="key" value="{{ $advertis->key }}" disabled
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Title</label>
                                <input type="text" name="title" value="{{ old('title', $advertis->title) }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-4">
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Mobile Tracking Code</label>
                                <textarea name="tracking_code[mobile]" class="w-full bg-gray-400 h-40 p-2 focus:border-blue-300 rounded border-none">{{ old('tracking_code.mobile', data_get($advertis, 'tracking_code.mobile')) }}</textarea>
                            </div>
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Tablet Tracking Code</label>
                                <textarea name="tracking_code[tablet]" class="w-full bg-gray-400 h-40 p-2 focus:border-blue-300 rounded border-none">{{ old('tracking_code.tablet', data_get($advertis, 'tracking_code.tablet')) }}</textarea>
                            </div>
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Desktop Tracking Code</label>
                                <textarea name="tracking_code[desktop]" class="w-full bg-gray-400 h-40 p-2 focus:border-blue-300 rounded border-none">{{ old('tracking_code.desktop', data_get($advertis, 'tracking_code.desktop')) }}</textarea>
                            </div>
                        </div>

                        <div class="flex mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="advertis-status"
                                    @checked($advertis->status == true)>
                                <label class="form-check-label" for="advertis-status">Advertis Status</label>
                            </div>
                        </div>

                        <div class="flex">
                            <button class="p-2 rounded border-none w-full bg-blue-600 text-white text-center font-semibold">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
