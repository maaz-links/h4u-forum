@extends('adminlte::page')

@section('title', 'Edit Review')

@section('content_header')
    <h1>Edit Review</h1>
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
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Review Details</h3>
                </div>
                
                <form id="changeReview" action="{{route('update.review')}}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>Reviewer</label>
                            <small class="text-muted">
                                <a href="{{ route('user-profile', $review->reviewer->name) }}">(View profile)</a>
                            </small>
                            <input type="text" class="form-control" value="{{ $review->reviewer->name }} (ID: {{ $review->reviewer_id }})" readonly>
                           
                        </div>
                        
                        <div class="form-group">
                            <label>Reviewed User</label>
                            <small class="text-muted">
                                <a href="{{ route('user-profile', $review->reviewedUser->name) }}">(View profile)</a>
                            </small>
                            <input type="text" class="form-control" value="{{ $review->reviewedUser->name }} (ID: {{ $review->reviewed_user_id }})" readonly>
                           
                        </div>
                        
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star rating-star" data-value="{{ $i }}" 
                                       style="{{ $i <= $review->rating ? 'color: #ffc107;' : '' }}"></i>
                                @endfor
                                <input type="hidden" name="review_id" value="{{ $review->id }}" required>
                                <input type="hidden" name="rating" id="rating" value="{{ $review->rating }}">
                                <input type="hidden" id="original_rating" value="{{ $review->rating }}">
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="form-group">
                            <label for="rating">Rating</label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star rating-star" data-value="{{ $i }}" 
                                       style="{{ $i <= $review->rating ? 'color: #ffc107;' : '' }}"></i>
                                @endfor
                                <input type="hidden" name="review_id" value="{{ $review->id }}" required>
                                <input type="hidden" name="rating" id="rating" value="{{ $review->rating }}">
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}
                        
                        {{-- <div class="form-group">
                            <label>Created At</label>
                            <input type="text" class="form-control" 
                                   value="{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y h:i A') }}" readonly>
                        </div> --}}
                    </div>
                    
                    <div class="card-footer">
                        {{-- <button id="saveButton" type="button" class="btn btn-primary"
                        onclick="confirmWithReason('changeReview')">
                            <i class="fas fa-save"></i> Save Changes
                        </button> --}}
                        <button id="saveButton" class="btn btn-primary"
                        >
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{route('user-profile.reviews',[$review->reviewedUser->name])}}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Reviews
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- <x-confirm-dialog title="Modify Review?" message="Enter reason to perform this action" inputPlaceholder=""
        buttonText="Confirm" cancelText="Cancel" /> --}}
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .rating-input {
        font-size: 2em;
        cursor: pointer;
    }
    .rating-star {
        color: #e4e5e9;
    }
    /* .rating-star:hover {
        color: #ffc107;
    } */
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    $(document).ready(function() {
        const originalRating = parseInt($('#original_rating').val());
        let currentRating = originalRating;
        
        // Initialize save button state
        updateSaveButton();
        // Star rating system
        // $('.rating-star').hover(
        //     function() {
        //         const value = $(this).data('value');
        //         $('.rating-star').each(function(index) {
        //             if (index < value) {
        //                 $(this).css('color', '#ffc107');
        //             }
        //         });
        //     },
        //     function() {
        //         const currentRating = $('#rating').val();
        //         $('.rating-star').each(function(index) {
        //             if (index >= currentRating) {
        //                 $(this).css('color', '#e4e5e9');
        //             }
        //         });
        //     }
        // );
        
        $('.rating-star').click(function() {
            const value = $(this).data('value');
            $('#rating').val(value);
            currentRating = value;
            $('.rating-star').each(function(index) {
                if (index < value) {
                    $(this).css('color', '#ffc107');
                } else {
                    $(this).css('color', '#e4e5e9');
                }
            });
            updateSaveButton();
        });

        function updateSaveButton() {
            const saveButton = $('#saveButton');
            if (currentRating === originalRating) {
                saveButton.prop('disabled', true);
                saveButton.removeClass('btn-primary').addClass('btn-secondary');
            } else {
                saveButton.prop('disabled', false);
                saveButton.removeClass('btn-secondary').addClass('btn-primary');
            }
        }
    });
</script>
@stop