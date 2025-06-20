@extends('adminlte::page')

@section('title', isset($europe_province) ? 'Edit Province' : 'Add New Province')

@section('content_header')
    <h1>{{ isset($europe_province) ? 'Edit Province' : 'Add New Province' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($europe_province) ? route('europe-provinces.update', $europe_province->id) : route('europe-provinces.store') }}" method="POST">
                @csrf
                @if(isset($europe_province))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="country_id">Country</label>
                    <select class="form-control @error('country_id') is-invalid @enderror"
                    {{ isset($europe_province) && $europe_province->id ? 'disabled' : '' }}
                           id="country_id" name="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" 
                                {{ old('country_id', $europe_province->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Province Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" 
                           value="{{ old('name', $europe_province->name ?? '') }}" 
                           placeholder="Enter province name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($europe_province) ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('europe-provinces.index') }}" class="btn btn-secondary">Back to List</a>
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