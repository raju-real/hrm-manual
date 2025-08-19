@extends('admin.layouts.app')
@section('title','Attendance Summery')
@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Attendance Summery</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.attendance-summery') }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-arrow-circle-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th>Date</th>
                            <td>:</td>
                            <td>{{ dateFormat($attendance->punch_time,'d M, y') }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const lat = {{ $attendance->latitude ?? 'null' }};
        const lng = {{ $attendance->longitude ?? 'null' }};
        if (lat && lng) {
            const map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(map);
            L.marker([lat, lng]).addTo(map);
        }
    </script>
@endpush
