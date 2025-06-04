@extends('adminlte::page')

@section('title', 'Chat List')

@section('content_header')
    <h1>Chat List</h1>
@stop

@section('content')
<form method="GET" action="{{ route('chats.index') }}" class="mb-3">
    <div class="input-group">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Search by Participant Names"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>
    <table id="chatsTable" class="table table-striped">
        <thead>
            <tr>
                <th>S/L</th>
                <th>Participants</th>
               
                <th>Created At</th>
                <th>Status</th>
                {{-- <th>Updated At</th> --}}
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chats as $chat)
                <tr>
                    <td>{{ $loop->index + $chats->firstItem() }}</td>
                    <td>
                        {{ $chat['user1']['name'] }} & {{ $chat['user2']['name'] }}
                    </td>
                   
                   
                    <td>{{ \Carbon\Carbon::parse($chat['created_at'])->format('M d, Y H:i') }}</td>
                    {{-- <td>{{ \Carbon\Carbon::parse($chat['updated_at'])->format('M d, Y H:i') }}</td> --}}
                    <td>
                        @if ($chat['unlocked'])
                            <span class="badge bg-success">Unlocked</span>
                        @else
                            <span class="badge bg-secondary">Locked</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('open.conversation', $chat['id']) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-comments"></i> Open Chat
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $chats->links() }}
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    {{-- <script>
        $(document).ready(function () {
            $('#chatsTable').DataTable({
                // responsive: true,
                // order: [[0, 'desc']], // Sort by ID descending by default
                // pageLength: 25,
                // lengthMenu: [10, 25, 50, 100]
            });
        });
    </script> --}}
@stop