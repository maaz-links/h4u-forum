@extends('adminlte::page')

@section('title', 'Configurations')

@section('content_header')
    <h1>Configurations</h1>
@stop

@section('content')
<div class="container my-5">
    <a href="{{ route('configs.create') }}" class="btn btn-success">Create New Configuration</a>
    
    @if(session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
    @endif

    <div class='mt-3' style="overflow:auto;max-height:50vh">
    <table class="table">
        <thead>
            <tr>
                <th style='width: 20%'>Key</th>
                <th style='width: 65%'>Value</th>
                <th style='width: 15%'>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($configs as $config)
            <tr>
                <td>{{ $config->key }}</td>
                <td>{{ $config->value }}</td>
                <td>
                    <a href="{{ route('configs.edit', $config) }}" class="btn btn-sm btn-warning">Edit</a>
                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal" data-form-id="delete-form-{{ $config->id }}"
                        data-name="{{ $config->key }}">
                        Delete
                    </button>

                    <!-- Add an ID to each delete form -->
                    <form id="delete-form-{{ $config->id }}" action="{{ route('configs.destroy', $config->id) }}"
                        method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete Configuration: <strong id="deleteItemName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let deleteFormId = null;

        // When delete button is clicked, store the form ID and update the modal text
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                deleteFormId = this.getAttribute('data-form-id');
                let itemName = this.getAttribute('data-name');

                // Set the item name in the modal
                document.getElementById('deleteItemName').textContent = `${itemName}`;
            });
        });

        // When confirm button in the modal is clicked, submit the form
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (deleteFormId) {
                document.getElementById(deleteFormId).submit();
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script
			  src="https://code.jquery.com/jquery-3.7.1.slim.js"
			  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
			  crossorigin="anonymous"></script>
@stop