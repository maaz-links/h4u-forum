@extends('adminlte::page')

@section('title', 'Shops')

@section('content_header')
    <h1>Transactions</h1>
@stop

@section('content')
<div class="container mt-4">
    {{-- <a href="{{ route('shop.create') }}" class="btn btn-success">Create New Shop</a> --}}
    
    @if(session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
    @endif

    <div class="container mt-4">
    <form method="GET" action="{{ route('shops.transactions') }}" class="mb-3">
        <div class="input-group">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Search by user name or shop title or payment ID"
            >
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>


    <div class='mt-3' style="overflow:auto;max-height:50vh">
    <table class="table">
        <thead>
            <tr>
                <th style='width: 20%'>S/L</th>
                <th style='width: 20%'>Payment ID</th>
                <th style='width: 20%'>User Name</th>
                <th style='width: 20%'>Shop Title</th>
                <th style='width: 65%'>Price</th>
                <th style='width: 65%'>Credit</th>
                <th style='width: 65%'>Payment_Method</th>
                <th style='width: 65%'>Purchased_at</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $key => $transaction)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $transaction->payment_id ?? 'N/A' }}</td>
                <td>{{ $transaction->user ? $transaction->user->name : 'N/A' }}</td>
                <td>{{ $transaction->shop->title ?? 'N/A' }}</td>
                <td>{{ $transaction->shop->price ?? 'N/A' }}</td>
                <td>{{ $transaction->shop->credits }}</td>
                <td class="text-center">{{ $transaction->payment_method }}</td>
                <td>{{ $transaction->created_at->format('F j, Y g:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
<div class="container">
    <div class=" d-flex justify-content-end">
  {{ $transactions->links() }}
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
                Are you sure you want to delete transaction: <strong id="deleteItemName"></strong>?
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