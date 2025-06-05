@extends('adminlte::page')

@section('title', 'Message Moderation Rules')

@section('content_header')
    <h1>Message Moderation Rules</h1>
@stop

@section('content')
<div class="col-md-6 my-3">
    <a href="{{ route('moderation-rules.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Rule
    </a>
</div>
@if (session('success'))
<div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
    <form method="GET" action="{{ route('moderation-rules.index') }}" class="mb-3">
        <div class="input-group">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Search"
            >
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    <div class="card">
        {{-- <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('moderation-rules.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Rule
                    </a>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('moderation-rules.index') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search rules..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%">S/L</th>
                            <th style="width: 10%">Type</th>
                            <th style="width: 15%">Regex Name</th>
                            <th>Keyword/Pattern</th>
                            <th style="width: 10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>{{$loop->index + $rules->firstItem() }}</td>
                                <td>{{ ucfirst($rule->type) }}</td>
                                <td>{{ $rule->name ?? 'N/A' }}</td>
                                <td>
                                    @if($rule->type === 'regex')
                                        <code>{{ $rule->pattern }}</code>
                                    @else
                                        {{ $rule->pattern }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('moderation-rules.edit', $rule->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('moderation-rules.destroy', $rule->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this rule?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No rules found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $rules->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop