@extends('adminlte::page')

@section('title', 'User Details')

@section('content_header')
    <h1>User Details of {{$user->name}}

        @if($user->isBanned())
        <span class="badge bg-danger">
            <i class="fas fa-ban"></i> Banned
        </span>
    @else
        {{-- <span class="badge bg-success">
            <i class="fas fa-check-circle"></i> Active
        </span> --}}
    @endif

    <form method="GET" action="{{ route('admin.login-as-user',$user->name) }}" class="mx-3" style='float: right;'>
        <button
            onclick="return confirm('{{ $user->isDummy() ? '' : 'This user is not Fake. ' }} Are you sure you want to login as {{ $user->name }}?')"
            type="submit"
            class="btn btn-warning">
            <i class="fas fa-key"></i> Login as User
        </button>
</form>
    </h1>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if (session('error'))
            <div class="mt-3 alert alert-danger alert-dismissible fade show">{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Basic Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Is Email Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
                                <p><strong>Phone:</strong> {{ $user->phone }}</p>
                                <p><strong>Latest OTP:</strong> {{ $user->otp ?? 'N/A' }} {{ ($user->otp && $user->otp_expires_at < now()) ? '(Expired)' : '' }} </p>
                                <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                                <p><strong>Is Fake:</strong> {{ $user->isDummy()?'Yes':'No' }}</p>
                                <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($user->dob)->format('M d, Y') }}</p>
                                <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                                <p><strong>Rating:</strong> {{ number_format($user->rating, 2) }} ⭐</p>
                                @if (\Auth::user()->hasPermission('manage_chat'))
                                <p><a href="{{route('user-profile.chat',[$user->name])}}">Check Chat List</a></p>
                                @endif
                                @if (\Auth::user()->hasPermission('change_user_password'))
                                <p><a href="{{route('user-profile.password.edit',[$user->id])}}">Change Password</a></p>
                                @endif
                                @if (\Auth::user()->hasPermission('manage_reviews'))
                                <p><a href="{{route('user-profile.reviews',[$user->name])}}">Check Reviews</a></p>
                                @endif
                                @if (\Auth::user()->hasPermission('user_bans'))
                                <p><a href="{{route('admin.users.ban.show',[$user->name])}}">Manage Bans</a></p>
                                @endif
                                {{-- <p><a href="{{route('user-profile.chat',[$user->name])}}">Check chat list</a></p> --}}
                                {{-- <p><a href="{{route('user-profile.reviews',[$user->name])}}">Check reviews</a></p>
                                <p><a href="{{route('admin.users.ban.show',[$user->name])}}">Manage bans</a> --}}
                                   
                                </p>
                            </div>
                            <div class="col-md-6">
                                
                                {{-- <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p> --}}
                                @if($user->profile_picture_id)
                                    <img src="{{ route('attachments.show' , $user->profile_picture_id) }}" 
                                         alt="Profile Picture" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px;">
                                @else
                                    <p class="text-muted">No profile picture</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->profile)
                <div class="card mt-4">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Profile Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <p><strong>Description:</strong><div>{{ $user->profile->description }}</div> </p>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                {{-- <p><strong>Gender:</strong> {{ $user->profile->gender }}</p> --}}
                                {{-- <p><strong>Description:</strong> {{ $user->profile->description }}</p> --}}

                                <p><strong>Nationality:</strong> {{ $user->profile->nationality }}</p>
                                <p><strong>Height:</strong> {{ $user->profile->height }} cm</p>
                                <p><strong>Top Profile:</strong> {{ $user->profile->top_profile ? 'Yes' : 'No' }}</p>
                                <p><strong>Verified Profile:</strong> {{ $user->profile->verified_profile ? 'Yes' : 'No' }}</p>
                                <p><strong>Eye Color:</strong> {{ ucwords($user->profile->eye_color) }}</p>
                                <p><strong>Shoe Size:</strong> {{ $user->profile->shoe_size }}</p>
                                <p><strong>
                                    @if ($user->role == \App\Models\User::ROLE_HOSTESS)
                                    Free Message limit today:
                                    @else
                                    Credits:
                                    @endif
                                </strong> {{ $user->profile->credits }}</p>
                            </div>
                            
                            <div class="col-md-6">
                                @if ($user->role == \App\Models\User::ROLE_HOSTESS)
                                <p><strong>Weight:</strong> {{ $user->profile->weight }} kg</p>
                                <p><strong>Dress Size:</strong> {{ $user->profile->dress_size }}</p>
                                <p><strong>Is Model:</strong> {{ $user->profile->is_user_model ? 'Yes' : 'No' }}</p>
                                
                                {{-- <p><strong>Verified Female:</strong> {{ $user->profile->verified_female ? 'Yes' : 'No' }}</p> --}}
                                
                                <p><strong>Travel Available:</strong> {{ $user->profile->travel_available ? 'Yes' : 'No' }}</p>
                                <p><strong>Telegram:</strong> {{ $user->profile->telegram ?: 'N/A' }}</p>
                                @endif
                            </div>
                           
                            @if (\Auth::user()->hasPermission('declare_badges'))
                            <div class="flex col-6">
                                <form class="d-inline" method="POST" action="{{ route('user-profile.toggle-verified', $user->id) }}">
                                    @csrf
                                    <button class="btn btn-{{ $user->profile->verified_profile ? 'secondary' : 'info' }}">
                                        {{ $user->profile->verified_profile ? 'Remove Verification' : 'Declare Verified' }}
                                    </button>
                                </form>
                            
                                <form class="d-inline" method="POST" action="{{ route('user-profile.toggle-top', $user->id) }}">
                                    @csrf
                                    <button class="btn btn-{{ $user->profile->top_profile ? 'secondary' : 'warning' }}">
                                        {{ $user->profile->top_profile ? 'Remove Top Profile' : 'Declare Top Profile' }}
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- <div class="card mt-4">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Social Media</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Facebook:</strong> {{ $user->profile->facebook ?: 'N/A' }}</p>
                                <p><strong>Instagram:</strong> {{ $user->profile->instagram ?: 'N/A' }}</p>
                                <p><strong>Telegram:</strong> {{ $user->profile->telegram ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>TikTok:</strong> {{ $user->profile->tiktok ?: 'N/A' }}</p>
                                <p><strong>OnlyFans:</strong> {{ $user->profile->onlyfans ?: 'N/A' }}</p>
                                <p><strong>Personal Website:</strong> {{ $user->profile->personal_website ?: 'N/A' }}</p>
                            </div>
                        </div> --}}
                        {{-- <hr> --}}
                        {{-- <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Available Services:</strong></p>
                                <ul>
                                    @foreach($user->profile->available_services as $service)
                                        <li>{{ $service }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Personal Interests:</strong></p>
                                <ul>
                                    @foreach($user->profile->personal_interests as $interest)
                                        <li>{{ $interest }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <p><strong>Languages:</strong></p>
                                <ul>
                                    @foreach($user->profile->my_languages as $language)
                                        <li>{{ $language }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div> --}}
                    {{-- </div>
                </div> --}}
                @else
                <div class="card mt-4">
                    <div class="card-body">
                        <p class="text-muted">No profile details available for this user.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .card-header {
            color: white;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
@stop