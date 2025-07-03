@extends('adminlte::page')

@section('title', isset($shownService) ? 'Edit Service' : 'Add New Service')

@section('content_header')
    <h1>{{ isset($shownService) ? 'Edit Service' : 'Add New Service' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($shownService) ? route('shown-services.update', $shownService->id) : route('shown-services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($shownService))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Service Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $shownService->name ?? '') }}" 
                           placeholder="Enter service name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Service Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" 
                              rows="3" 
                              placeholder="Enter service description">{{ old('description', $shownService->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                           id="display_order" name="display_order" 
                           value="{{ old('display_order', $shownService->display_order ?? 0) }}">
                    @error('display_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="form-group">
                    <label for="image">Service Image</label>
                    <input type="file" class="form-control-file @error('image') is-invalid @enderror" 
                           id="image" name="image" {{ !isset($shownService) ? 'required' : '' }}>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($shownService) && $shownService->image_path))
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$shownService->image_path) }}" alt="{{ $shownService->name }}" style="max-width: 200px;">
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($shownService) ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('shown-services.index') }}" class="btn btn-secondary">Back to List</a>
            </form>
        </div>
    </div>
@stop
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
@stop
