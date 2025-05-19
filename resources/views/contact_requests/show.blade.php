@extends('adminlte::page')

@section('title', 'View Contact Request')

@section('content_header')
    <h1>View Contact Request</h1>
   
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Request Details</h5>
                        <hr>
                        <div class="mb-3">
                            <strong>ID:</strong> {{ $contactRequest->id }}
                        </div>
                        <div class="mb-3">
                            <strong>Name:</strong> {{ $contactRequest->name }}
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong> 
                            {{ $contactRequest->email }}
                        </div>
                        <div class="mb-3">
                            <strong>Submitted At:</strong> 
                            {{ $contactRequest->created_at->format('M d, Y H:i') }}
                        </div>
                        {{-- <div class="mb-3">
                            <strong>Terms Accepted:</strong> 
                            {{ $contactRequest->terms_accepted ? 'Yes' : 'No' }}
                        </div> --}}
                    </div>
                    <div class="col-md-6">
                        <h5>Message</h5>
                        <hr>
                        <div class="border p-3 bg-light rounded">
                            {{ $contactRequest->message }}
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <form action="{{ route('contact-requests.destroy', $contactRequest) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this request?')">
                            Delete Request
                        </button>
                        <a href="{{ route('contact-requests.index') }}" class="btn btn-secondary btn-sm float-right">Back to List</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop