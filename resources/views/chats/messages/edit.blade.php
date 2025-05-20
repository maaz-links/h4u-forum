@extends('adminlte::page')

@section('title', 'Edit Chat Message')

@section('content_header')
    <h1>Edit Chat Message</h1>
@stop

@section('content')
@if (session('success'))
<div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="card">
    <div class="card-body">
        <form action="{{route('chat.messages.update',$msg->id)}}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="message">Message Content</label>
                <textarea class="form-control @error('message') is-invalid @enderror" 
                          id="message" name="message" rows="3" required>{{ old('message', $msg->message) }}</textarea>
                @error('message')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            {{-- <div class="form-group mt-3">
                <label>Message Info</label>
                <div class="alert alert-info">
                    <p><strong>Message ID:</strong> {{ $msg->id }}</p>
                    <p><strong>Chat ID:</strong> {{ $msg->chat_id }}</p>
                    <p><strong>Sender ID:</strong> {{ $msg->sender_id }}</p>
                    <p><strong>Created:</strong> {{ \Carbon\Carbon::parse($msg->created_at)->format('M d, Y H:i:s') }}</p>
                    <p><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($msg->updated_at)->format('M d, Y H:i:s') }}</p>
                </div>
            </div> --}}
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Message
                </button>
                
                <a href="{{ route('open.conversation',$msg->chat_id) }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </form>
        <form id="deleteForm" action="{{route('chat.messages.destroy',$msg->id)}}" method="POST">
            @csrf
            @method('DELETE')
        
        <button type="submit" class="btn btn-danger mt-2" onclick="return confirm('Are you sure you want to delete this message?')">
            <i class="fas fa-trash"></i> Delete Message
        </button>
        </form>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script
  src="https://code.jquery.com/jquery-3.7.1.slim.js"
  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
  crossorigin="anonymous"></script>

@stop