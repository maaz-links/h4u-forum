@extends('adminlte::page')

@section('title', isset($europe_country) ? 'Edit Country' : 'Add New Country')

@section('content_header')
    <h1>{{ isset($europe_country) ? 'Edit Country' : 'Add New Country' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($europe_country) ? route('europe-countries.update', $europe_country->id) : route('europe-countries.store') }}" method="POST">
                @csrf
                @if(isset($europe_country))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Country Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $europe_country->name ?? '') }}" 
                           placeholder="Enter country name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(!isset($europe_country))
                <div class="form-group">
                    <label for="name">Province Name
                        <small class="text-muted">
                            (Each Country must have at least one province)
                        </small>
                    </label>
                    <input type="text" class="form-control @error('province_name') is-invalid @enderror" 
                           id="province_name" name="province_name" 
                           placeholder="Enter province name" required>
                    {{-- @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror --}}
                </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    {{ isset($europe_country) ? 'Update' : 'Save' }}
                </button>
                @if(isset($europe_country))
                @if(!$europe_country->is_default)
                    <a href="{{ route('europe-countries.setdefault',$europe_country->id) }}" class="btn btn-success">Make Default</a>
                @endif
                @endif
                <a href="{{ route('europe-countries.index') }}" class="btn btn-secondary">Back to List</a>
                
            </form>
            @if(isset($europe_country))
            @if(!$europe_country->is_default)
            <small class="mt-2 form-text text-muted">
                When user registers a new profile, Default Country is used and one of its provinces are selected.
            </small>
            <small class="form-text text-muted">
                Default Country must have at least one province.
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