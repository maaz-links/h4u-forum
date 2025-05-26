@extends('adminlte::page')

@section('title', 'Change User Password')

@section('content_header')
    <h1>Change User Password of {{$user->name}}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Change User Password</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user-profile.password.update') }}">
                        @csrf

                        {{-- <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input id="current_password" type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   name="current_password" required autocomplete="current-password">
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}

                        <div class="form-group mt-3">
                            <label for="new_password">New Password</label>
                            <input id="new_password" type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   name="new_password" required autocomplete="new-password">
                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <input id="new_password" type="hidden" 
                            class="form-control @error('new_password') is-invalid @enderror" 
                            name="user_id" value={{$user->id}} required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input id="new_password_confirmation" type="password" 
                                   class="form-control" name="new_password_confirmation" required 
                                   autocomplete="new-password">
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-info">
                                Change Password
                            </button>
                            <a href="{{route('user-profile',$user->name)}}" class="btn btn-secondary">
                                Back to Profile
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-header {
        color: white;
    }
    .table th {
        background-color: #f8f9fa;
    }
</style>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
@stop