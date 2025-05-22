@extends('adminlte::page')

@section('title', 'FAQs')

@section('content_header')
    <h1>FAQs</h1>
@stop

@section('content')
<div class="container my-5">
    <a href="{{ route('faq.create') }}" class="btn btn-success">Create New FAQ</a>

    @if(session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
    @endif

    <div class='mt-3' style="overflow:auto;max-height:50vh">
        <form method="GET" action="{{ route('faqs.index') }}" class="mb-3">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Search by question or answer"
                >
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>S/L</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faqs as $key => $faq)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $faq->question }}</td>
                        <td>{{ Str::limit($faq->answer, 100) }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('faq.edit', ['id' => $faq->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm delete-btn"
                                data-toggle="modal"
                                data-target="#deleteConfirmModal"
                                data-form-id="delete-form-{{ $faq->id }}"
                                data-name="{{ $faq->question }}">
                                Delete
                            </button>

                            <form id="delete-form-{{ $faq->id }}" action="{{ route('faq.destroy') }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $faq->id }}">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="container">
        <div class="d-flex justify-content-end">
            {{ $faqs->links() }}
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete FAQ: <strong id="deleteItemName"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let deleteFormId = null;

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                deleteFormId = this.getAttribute('data-form-id');
                let itemName = this.getAttribute('data-name');
                document.getElementById('deleteItemName').textContent = `${itemName}`;
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (deleteFormId) {
                document.getElementById(deleteFormId).submit();
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script> console.log("FAQ Module Loaded"); </script>
@stop
