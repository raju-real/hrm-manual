@extends('admin.layouts.app')
@section('title','Attendance Summary')
@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Attendance Summary</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.attendance-logs') }}" class="btn btn-sm btn-outline-primary">
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
                                <td>{{ \Carbon\Carbon::parse($attendanceDetails->check_in ?? $attendanceDetails->check_out)->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Check-in Time</th>
                                <td>:</td>
                                <td>{{ \Carbon\Carbon::parse($attendanceDetails->check_in)->format('h:i A') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Check-out Time</th>
                                <td>:</td>
                                <td>{{ \Carbon\Carbon::parse($attendanceDetails->check_out)->format('h:i A') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Working Hours</th>
                                <td>:</td>
                                <td>
                                    {{ $attendanceDetails->working_hour ?? 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    {{-- Conditionally show the map container only if coordinates exist --}}
                    @if ($attendanceDetails->check_in_lat || $attendanceDetails->check_out_lat)
                        <div id="map"></div>
                    @else
                        <p>No location data available for this attendance record.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Use a self-executing anonymous function to prevent conflicts
        (function() {
            // Get boolean values directly from the PHP object
            const hasCheckIn = {{ $attendanceDetails->has_check_in }};
            const hasCheckOut = {{ $attendanceDetails->has_check_out }};

            // Initialize map only if there is at least one set of coordinates
            if (hasCheckIn || hasCheckOut) {
                // Initialize map with a default view. It will be adjusted later.
                const map = L.map('map').setView([0, 0], 2);

                // Add the OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Create custom icons for check-in (green) and check-out (red)
                const checkInIcon = L.icon({
                    iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                });

                const checkOutIcon = L.icon({
                    iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    shadowSize: [41, 41]
                });

                // Create a feature group to manage markers
                const markersGroup = new L.featureGroup();

                // Add the check-in marker if coordinates exist
                if (hasCheckIn) {
                    // Use json_encode to ensure the variables are properly rendered, preventing syntax errors if null
                    const checkInLat = {{ $attendanceDetails->check_in_lat }};
                    const checkInLon = {{ $attendanceDetails->check_in_lon }};
                    const content = `
                            <strong>Check-in Location</strong>
                            <p style="color:red;font-weight: bold;">Accuracy: &plusmn; 50m</p>
                        `;
                    const checkInMarker = L.marker([checkInLat, checkInLon], { icon: checkInIcon })
                        .bindPopup(content)
                        .openPopup();
                    markersGroup.addLayer(checkInMarker);
                }

                // Add the check-out marker if coordinates exist
                if (hasCheckOut) {
                    // Use json_encode to ensure the variables are properly rendered
                    const checkOutLat = {{ $attendanceDetails->check_out_lat }};
                    const checkOutLon = {{ $attendanceDetails->check_out_lon }};
                    const content = `
                            <strong>Check-out Location</strong>
                            <p style="color:red;font-weight: bold;">Accuracy: &plusmn; 50m</p>
                        `;
                    const checkOutMarker = L.marker([checkOutLat, checkOutLon], { icon: checkOutIcon })
                        .bindPopup(content)
                        .openPopup();
                    markersGroup.addLayer(checkOutMarker);
                }
                
                // Add the group of markers to the map
                markersGroup.addTo(map);

                // Fit the map to the bounds of the markers that were added
                map.fitBounds(markersGroup.getBounds(), {
                    padding: [50, 50] // Add some padding
                });
            }
        })();
    </script>
@endpush
