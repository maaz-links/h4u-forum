@extends('adminlte::page')

@section('title', 'Moderation Reports')

@section('content_header')
    <h1>Moderation Trend Reports</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="get" class="form-inline">
            <label class="mr-2">Time Range:</label>
            <select name="time_range" class="form-control mr-2" onchange="this.form.submit()">
                @foreach($timeRanges as $value => $label)
                    <option value="{{ $value }}" {{ $timeRange == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </div>
</div>

<div class="row">
    <!-- Top Violations -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">Top Violations</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Rule</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($violationStats as $stat)
                            <tr>
                                <td>{{ $stat->rule }}</td>
                                <td>{{ $stat->count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No violations found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Frequent Offenders -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">Frequent Offenders</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Alerts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userStats as $stat)
                            <tr>
                                <td>
                                    {{-- <a href="{{ route('admin.users.show', $stat->user_id) }}"> --}}
                                        {{ $stat->user_name }}
                                    {{-- </a> --}}
                                </td>
                                <td>{{ $stat->alert_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No user alerts found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- <div class="row mt-4">
    <!-- Status Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">Status Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Time Trend -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">Violations Over Time</h3>
            </div>
            <div class="card-body">
                <canvas id="timeTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div> --}}
@stop

@section('css')
<style>
    .badge {
        font-size: 1em;
        padding: 0.5em 0.75em;
    }
</style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- <script>
$(document).ready(function() {
    // Status Distribution Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusStats->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($statusStats->pluck('count')) !!},
                backgroundColor: [
                    '#ffc107', // PENDING - yellow
                    '#28a745', // APPROVED - green
                    '#dc3545', // REJECTED - red
                    '#6c757d'  // ARCHIVED - gray
                ]
            }]
        }
    });

    // Time Trend Chart (simplified example)
    // In a real implementation, you would fetch time-series data from your controller
    new Chart(document.getElementById('timeTrendChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Violations',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#007bff',
                fill: false
            }]
        }
    });
});
</script> --}}
@stop