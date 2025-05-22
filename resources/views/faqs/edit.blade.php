@extends('adminlte::page')

@section('title', 'Edit FAQ')

@section('content_header')
<h1>Edit FAQ</h1>
@stop

@section('content')
<div class="container my-5 card px-4 py-4">

    <form action="{{ route('faq.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $faq->id }}">

        <div class="mb-3">
            <label>Question</label>
            <input type="text" name="question" value="{{ $faq->question }}" placeholder="Enter question here"
                class="form-control" required>
            @error('question')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label>Answer</label>
            <textarea name="answer" rows="5" placeholder="Enter answer here"
                class="form-control" required>{{ $faq->answer }}</textarea>
            @error('answer')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a class="btn btn-secondary" href="{{ route('faqs.index') }}">Back to List</a>
    </form>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script>
    console.log("FAQ Edit Page Loaded");
</script>
<script src="https://code.jquery.com/jquery-3.7.1.slim.js"
    integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
@stop
