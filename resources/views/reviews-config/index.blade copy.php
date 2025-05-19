@extends('adminlte::page')

@section('title', 'Reviews Configuration')

@section('content_header')
    <h1>Reviews Configurations</h1>
@stop

@section('content')
    <div class="container my-5">

        @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class='mt-3'>
            <div class="container col-9">
                <div class="card p-3">
                    <h3 class="mb-5">Change Review and Ratings Configuration</h3>

                    <form id="delayForm" action="{{ route('reviews-config.store') }}" method="POST">
                        @csrf
                        <div class="form-group row align-items-center mb-3">
                            <label title="Number of days after which a male user can review a female user"
                                class="col-sm-5 col-form-label text-end">
                                Review delay for male users
                            </label>
                            <div class="col-sm-2">
                                <input type="number" min="0" name="review_delay" class="form-control"
                                    value="{{ config('h4u.reviews.review_delay') }}" required>
                            </div>
                            <div class="col-sm-2">
                                <span class="form-text">day(s)</span>
                            </div>
                            <div class="col-sm-3 text-start">
                                {{-- <button type="button" class="btn btn-success" onclick="confirmWithReason('delayForm')">
                                    Save
                                </button> --}}
                                <button class="btn btn-success">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                    <form id="minreviewsForm" action="{{ route('reviews-config.store') }}" method="POST">
                        @csrf
                        <div class="form-group row align-items-center mb-3">
                            <label title="The minimum number of reviews required before a user's rating is visible publicly"
                                class="col-sm-5 col-form-label text-end">
                                Minimum Reviews for Public Rating
                            </label>
                            <div class="col-sm-2">
                                <input type="number" min="0" name="minimum_reviews_for_visibility"
                                    class="form-control" value="{{ config('h4u.reviews.minimum_reviews_for_visibility') }}"
                                    required>
                            </div>
                            <div class="col-sm-2">
                                <span class="form-text">review(s)</span>
                            </div>
                            <div class="col-sm-3 text-start">
                                {{-- <button type="button" class="btn btn-success" onclick="confirmWithReason('minreviewsForm')">
                                    Save
                                </button> --}}
                                <button class="btn btn-success">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                    <form id="minratingForm" action="{{ route('reviews-config.store') }}" method="POST">
                        @csrf
                        <div class="form-group row align-items-center mb-3">
                            <label title="The minimum number of stars a user can give to other user"
                                class="col-sm-5 col-form-label text-end">
                                Minimum Rating
                            </label>
                            <div class="col-sm-2">
                                <input type="number" min="0" max="5" name="minimum_rating"
                                    class="form-control" value="{{ config('h4u.reviews.minimum_rating') }}"
                                    required>
                            </div>
                            <div class="col-sm-2">
                                <span class="form-text">star(s)</span>
                            </div>
                            <div class="col-sm-3 text-start">
                                {{-- <button type="button" class="btn btn-success" onclick="confirmWithReason('minratingForm')">
                                    Save
                                </button> --}}
                                <button class="btn btn-success">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @foreach ($errors->all() as $error)
                    <div class="mt-3 alert alert-danger">
                        <li>{{ $error }}</li>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- <x-confirm-dialog formId="minratingForm" title="Update Credit Costs" message="Enter reason to perform this action"
        inputPlaceholder="" buttonText="Confirm" cancelText="Cancel" /> --}}
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

@stop
