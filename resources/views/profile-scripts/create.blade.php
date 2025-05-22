@extends('adminlte::page')

@section('title', 'Profile Scripts')

@section('content_header')
    <h1>Profile Scripts</h1>
@stop

@section('content')
    <div class="container my-5">
        <div class='mt-3'>
            <div class="container col-9">
                <div class="card p-3">
                    <h3 class="mb-5">Generate Test Profiles</h3>
                    <form id="profileForm" action="{{ route('profile-scripts.store') }}" method="POST">
                        @csrf
                        <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-5 col-form-label text-end">
                                Script Name
                            </label>
                            <div class="col-sm-4">
                                <input type="text" name="script_name" class="form-control" placeholder="Enter name" required>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-5 col-form-label text-end">
                                Number of Profiles
                            </label>
                            <div class="col-sm-4">
                                <input type="number" min="1" max="100" name="profile_count" class="form-control" value="3" required>
                            </div>
                        </div>
                        
                        <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-5 col-form-label text-end">
                                Age Range
                            </label>
                            <div class="col-sm-2">
                                <input type="number" min="18" max="99" name="min_age" class="form-control" placeholder="Min" value="18" required>
                            </div>
                            <div class="col-sm-2">
                                <input type="number" min="18" max="99" name="max_age" class="form-control" placeholder="Max" value="35" required>
                            </div>
                        </div>
                        
                        {{-- <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-5 col-form-label text-end">
                                Gender
                            </label>
                            <div class="col-sm-4">
                                <select name="gender" class="form-control" required>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                        </div>
                         --}}
                        {{-- <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-5 col-form-label text-end">
                                Province
                            </label>
                            <div class="col-sm-4">
                                <select name="province" class="form-control" required>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        
                        <div class="form-group row mt-4">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-7">
                                {{-- <button type="button"
                                onclick="confirmWithReason('profileForm')" class="btn btn-primary">
                                    Generate Profiles
                                </button> --}}
                                <button class="btn btn-primary">
                                    Generate Profiles
                                </button>
                                <a href={{route('profile-scripts.index')}} class="btn btn-secondary">
                                    Back to List
                                </a>
                            </div>
                        </div>
                        
                    </form>
                </div>
                
                @foreach ($errors->all() as $error)
                    <div class="mt-3 alert alert-danger">
                        <li>{{ $error }}</li>
                    </div>
                @endforeach
                
                @if(session('success'))
                    <div class="mt-3 alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{-- <x-confirm-dialog title="Generate profiles?" message="Enter reason to perform this action" inputPlaceholder=""
    buttonText="Confirm" cancelText="Cancel" /> --}}
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
        
    <script>
        $(document).ready(function() {
            // Form validation
            $('#profileForm').submit(function(e) {
                const minAge = parseInt($('[name="min_age"]').val());
                const maxAge = parseInt($('[name="max_age"]').val());
                
                if (minAge > maxAge) {
                    e.preventDefault();
                    alert('Minimum age cannot be greater than maximum age');
                    // Swal.fire({
                    //     icon: 'error',
                    //     title: 'Invalid Age Range',
                    //     text: 'Minimum age cannot be greater than maximum age'
                    // });
                }
            });
        });
    </script>
@stop