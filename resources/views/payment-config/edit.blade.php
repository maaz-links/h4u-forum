@extends('adminlte::page')

@section('title', 'Payment Configuration')

@section('content_header')
    <h1>Payment Configuration</h1>
@stop

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('payment-config.update') }}">
        @csrf
        @method('PUT')

        <div class="card mb-6">
            <div class="card-header">PayPal Settings</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="services.paypal.client_id">Paypal Client ID</label>
                    <input type="text" class="form-control" id="services.paypal.client_id" name="services_paypal_client_id" 
                           value="{{ config('services.paypal.client_id') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="services.paypal.secret">PayPal Secret Key</label>
                    <input type="text" class="form-control" id="services.paypal.secret" name="services_paypal_secret" 
                           value="{{ config('services.paypal.secret') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="services.paypal.sandbox">Paypal Sandbox Mode</label>
                    <div class="px-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="services_paypal_sandbox" id="sandbox_enabled"
                                   value="1" {{ config('services.paypal.sandbox') ? 'checked' : '' }}>
                            <label class="form-check-label" for="sandbox_enabled">True</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="services_paypal_sandbox" id="sandbox_disabled"
                                   value="0" {{ !config('services.paypal.sandbox') ? 'checked' : '' }}>
                            <label class="form-check-label" for="sandbox_disabled">False</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-header">Stripe Settings</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="services.stripe.key">Stripe Public Key</label>
                    <input type="text" class="form-control" id="services.stripe.key" name="services_stripe_key" 
                           value="{{ config('services.stripe.key') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="services.stripe.secret">Stripe Secret Key</label>
                    <input type="text" class="form-control" id="services.stripe.secret" name="services_stripe_secret" 
                           value="{{ config('services.stripe.secret') }}" required>
                </div>
                
            </div>
        </div>
        
        
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge {
            font-size: 0.85em;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop
