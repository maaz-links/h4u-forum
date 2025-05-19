@extends('adminlte::page')

@section('title', 'User Reviews')

@section('content_header')
    <h1>Reviews for <a href="{{ route('user-profile', $name) }}">{{$name}} </a></h1>
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
        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header bg-primary">
                    <h3 class="card-title">All Reviews</h3>
                    <div class="card-tools">
                        <span class="badge bg-white text-primary">{{ $reviews->count() }} total reviews</span>
                    </div>
                </div> --}}
                
                <div class="card-body p-0">
                    {{-- <div class="table-responsive"> --}}
                        <table class="table">
                            <thead>
                                <tr>
                                    {{-- <th>ID</th> --}}
                                    <th>Reviewer</th>
                                    <th>Rating</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                <tr>
                                    {{-- <td>{{ $review->id }}</td> --}}
                                    <td>
                                    @if($review['reviewer'])
                                    
                                        <a href="{{ route('user-profile', $review['reviewer']['name']) }}">

                                            {{ $review['reviewer']['name'] }}
                                        </a>
                                    
                                    @else
                                        [Deleted]
                                    @endif
                                    </td>
                                    <td>
                                        <div class="rating-display" data-rating="{{ $review->rating }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                            {{-- <span class="ml-2">{{ $review->rating }}.0</span> --}}
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <form id="{{ "deletereviewForm-{$review['id']}" }}"
                                            action="{{ route('delete.review') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="review_id" value="{{ $review['id'] }}" required>
                                            <a href="{{ route('edit.review', $review['id']) }}"
                                            class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            {{-- <button type="button"
                                                onclick="confirmWithReason('{{ 'deletereviewForm-' . $review['id'] }}')"
                                                class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button> --}}
                                            <button 
                                            class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        </form>
                                        {{-- <button class="btn btn-sm btn-danger delete-review" data-review-id="{{ $review->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button> --}}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No reviews found for this user</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    {{-- </div> --}}
                </div>
                @if(count($reviews) > 0)
                <div class="card-footer clearfix">
                    <div class="float-right">
                        Showing {{ count($reviews) }} reviews
                    </div>
                </div>
                @endif
                {{-- @if($reviews->hasPages())
                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $reviews->links() }}
                    </div>
                </div>
                @endif --}}
            </div>
        </div>
    </div>
</div>
{{-- <x-confirm-dialog title="Delete this review?" message="Enter reason to perform this action" inputPlaceholder=""
        buttonText="Confirm" cancelText="Cancel" /> --}}
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .rating-display {
        display: inline-flex;
        align-items: center;
    }
    .rating-display i {
        font-size: 1.2em;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.03);
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    $(document).ready(function() {
        // Delete review confirmation
        $('.delete-review').click(function() {
            const reviewId = $(this).data('review-id');
            
            Swal.fire({
                title: 'Delete Review?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/reviews/${reviewId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'The review has been deleted.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was a problem deleting the review.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@stop