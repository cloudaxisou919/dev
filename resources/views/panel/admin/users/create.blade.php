@extends('panel.layout.app')
@section('title', 'My Account')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 items-center justify-content-between">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('User Management') }}
                    </h2>
                </div>
                <div class="col-6">
                    <div class="flex justify-end">
                        <a href="{{ route('dashboard.user.create') }}"
                            class="uppercase hover:decoration-transparent rounded-2xl bg-blue-500 text-[10px] font-semibold text-white border-none p-2
                        w-1/4
                        ">
                            {{ __('Create New User') }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-3 -mt-1 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="card">
                <div class="flex flex-col">
                    <h1 class="text-sm m-3">
                        Create New User
                    </h1>
                    @if ($errors->any())
                        <div class="flex border-red-500 border w-1/3 m-2 p-1">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-500 font-semibold">
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <hr>
                    <form action="{{ route('dashboard.user.store') }}" method="POST" class="m-3 w-2/3" enctype="multipart/form-data">
                        <div class="flex gap-4">
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="name" class="m-1">First Name</label>
                                <input type="text" name="firstname"
                                    value="{{ old('firstname') }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="name" class="m-1">Last Name</label>
                                <input type="text" name="lastname"
                                    value="{{ old('lastname') }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="name" class="m-1">E-Mail</label>
                                <input type="email" name="email"
                                    value="{{ old('email') }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="name" class="m-1">Phone</label>
                                <input type="text" name="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-full gap-2 flex flex-col">
                                <label for="name" class="m-1">Avatar</label>
                                <input type="file" name="avatar"
                                    accept="image/*"
                                    required
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="name" class="m-1">Password</label>
                                <input type="password" name="password"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="name" class="m-1">Re-Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-full flex flex-col">
                                <label for="name" class="m-1">Country</label>
                                <select name="country"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                                    <option selected disabled>Select Country</option>
                                    <option value="turkey">Turkey</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="type" class="m-1">Type</label>
                                <select name="type"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                                    <option value="admin">admin</option>
                                    <option value="user">user</option>
                                </select>
                            </div>
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="status" class="m-1">Status</label>
                                <select name="status"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                                    <option value="1">Active</option>
                                    <option value="0">Passive</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="remaining_words" class="m-1">Remaining Words</label>
                                <input type="text" name="remaining_words"
                                    value="{{ old('remaining_words') }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                            <div class="w-1/2 gap-2 flex flex-col">
                                <label for="remaining_images" class="m-1">Remaining Images</label>
                                <input type="text" name="remaining_images"
                                    value="{{ old('remaining_images') }}"
                                    class="w-full pl-3 h-10 bg-gray-400 focus:border-blue-300 rounded border-none">
                            </div>
                        </div>
                        <div class="flex mt-3">
                            <button class="bg-blue-800 text-white rounded-xl border-none p-2 w-full ">
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
