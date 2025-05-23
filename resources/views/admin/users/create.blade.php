@extends('adminlte::page')

@section('title', 'Create New Admin')

@section('content_header')
    <h1>Create New Admin</h1>
@stop

@section('content')
<div class="container">
    {{-- <h1>Create New Admin</h1> --}}
    
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                           value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                           value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" {{ !isset($user) ? 'required' : '' }}>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                           {{ !isset($user) ? 'required' : '' }}>
                </div>
            </div>
        </div>
        
        {{-- <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
         --}}
        {{-- <div class="form-group">
            <label>Permissions</label>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="permission_{{ $permission->id }}" 
                                   name="permissions[]" 
                                   value="{{ $permission->id }}">
                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div> --}}
        <div class="form-group">
            <label>Role</label>
            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="role_{{ $role->id }}" 
                                   name="roles[]" 
                                   value="{{ $role->id }}"
                                   {{-- {{ (isset($user) && $user->roles->first()->permissions->contains($permission->id)) || in_array($permission->id, old('permissions', [])) ? 'checked' : '' }} --}}
                                   >
                            <label title="{{ $role->permissions()->pluck('name')->implode(', ') }}" class="form-check-label" for="role_{{ $role->id }}">
                                {{ Str::of($role->name)->replace('_', ' ')->title() }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Admin</button>
    </form>
</div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop
