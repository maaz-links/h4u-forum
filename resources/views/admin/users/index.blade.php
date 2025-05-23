@extends('adminlte::page')

@section('title', 'Admin Users')

@section('content_header')
    <h1>Admin Users</h1>
@stop

@section('content')
<div class="container">
    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- <h1>Admin Users</h1> --}}
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            Create New Admin
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            @foreach($admin->roles as $role)
                                <span class="badge bg-primary">{{ Str::of($role->name)->replace('_', ' ')->title() }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach($admin->roles as $role)
                                @foreach($role->permissions as $permission)
                                    <span class="badge bg-secondary">{{ $permission->name }}</span>
                                @endforeach
                            @endforeach
                        </td>
                        <td>
                            @if (\Auth::id() === $admin->id)
                            You
                            @else
                                <a href="{{ route('admin.users.edit', $admin->id) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop