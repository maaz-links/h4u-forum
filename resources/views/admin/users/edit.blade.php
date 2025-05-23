@extends('adminlte::page')

@section('title', 'Edit Admin Roles')

@section('content_header')
    <h1>Edit Admin Roles</h1>
@stop

@section('content')
<div class="container">
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="name">Admin Name</label>
            <input type="text" class="form-control" id="name" value="{{ $user->name }}" disabled>
        </div>

        <div class="form-group mb-3">
            <label for="email">Admin Email</label>
            <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
        </div>

        <div class="form-group mb-4">
            <label>Roles</label>
            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   id="role_{{ $role->id }}"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                            <label title="{{ $role->permissions()->pluck('name')->implode(', ') }}" class="form-check-label" for="role_{{ $role->id }}">
                                {{ Str::of($role->name)->replace('_', ' ')->title() }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Roles</button>
    </form>
</div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop
