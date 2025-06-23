@extends('adminlte::page')

@section('title', isset($form_nationality) ? 'Edit Nationality' : 'Add New Nationality')

@section('content_header')
    <h1>{{ isset($form_nationality) ? 'Edit Nationality' : 'Add New Nationality' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($form_nationality) ? route('form-nationalities.update', $form_nationality->id) : route('form-nationalities.store') }}" method="POST">
                @csrf
                @if(isset($form_nationality))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Nationality Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $form_nationality->name ?? '') }}" 
                           placeholder="Enter nationality name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($form_nationality) ? 'Update' : 'Save' }}
                </button>
                @if(isset($form_nationality))
                @if(!$form_nationality->isDefault())
                    <a href="{{ route('form-nationalities.setdefault',$form_nationality->id) }}" class="btn btn-success">Make Default</a>
                @endif
                @endif
                <a href="{{ route('form-nationalities.index') }}" class="btn btn-secondary">Back to List</a>
            </form>
            @if(isset($form_nationality))
            @if(!$form_nationality->isDefault())
            <small class="mt-2 form-text text-muted">
                When user registers a new profile, Default Nationality is used.
            </small>
            @endif
            @endif
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