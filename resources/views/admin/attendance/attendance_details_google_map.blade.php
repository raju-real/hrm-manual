@extends('admin.layouts.app')
@section('title','Attendance Summary')
@push('css')
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
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD36x7UyJoXuWlfIY-aF6Q9MD4SBZkLkiM&libraries=geometry&callback=initMap">
    </script>
    <script>
        let map;

        function initMap() {
            // Using a ternary operator to ensure the correct boolean value is passed to JavaScript
            const hasCheckIn = {{ $attendanceDetails->has_check_in ? 'true' : 'false' }};
            const hasCheckOut = {{ $attendanceDetails->has_check_out ? 'true' : 'false' }};

            if (hasCheckIn || hasCheckOut) {
                // Determine the initial center and zoom level
                let centerPoint;
                let zoomLevel = 15;

                if (hasCheckIn) {
                    const checkInLat = parseFloat({{ $attendanceDetails->check_in_lat }});
                    const checkInLon = parseFloat({{ $attendanceDetails->check_in_lon }});
                    centerPoint = new google.maps.LatLng(checkInLat, checkInLon);
                } else if (hasCheckOut) {
                    const checkOutLat = parseFloat({{ $attendanceDetails->check_out_lat }});
                    const checkOutLon = parseFloat({{ $attendanceDetails->check_out_lon }});
                    centerPoint = new google.maps.LatLng(checkOutLat, checkOutLon);
                }

                // If both locations exist, center the map between them and adjust the zoom
                if (hasCheckIn && hasCheckOut) {
                    const checkInLat = parseFloat({{ $attendanceDetails->check_in_lat }});
                    const checkInLon = parseFloat({{ $attendanceDetails->check_in_lon }});
                    const checkOutLat = parseFloat({{ $attendanceDetails->check_out_lat }});
                    const checkOutLon = parseFloat({{ $attendanceDetails->check_out_lon }});
                    
                    const checkIn = new google.maps.LatLng(checkInLat, checkInLon);
                    const checkOut = new google.maps.LatLng(checkOutLat, checkOutLon);

                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(checkIn);
                    bounds.extend(checkOut);

                    centerPoint = bounds.getCenter();
                    
                    // The zoom level is determined by the distance between the two points
                    const distance = google.maps.geometry.spherical.computeDistanceBetween(checkIn, checkOut);
                    
                    // Simple logic to set a reasonable zoom level based on distance
                    if (distance > 100000) { // > 100 km
                        zoomLevel = 6;
                    } else if (distance > 10000) { // 10-100 km
                        zoomLevel = 9;
                    } else if (distance > 1000) { // 1-10 km
                        zoomLevel = 12;
                    } else if (distance > 100) { // 100m - 1km
                        zoomLevel = 14;
                    } else { // < 100m
                        zoomLevel = 17;
                    }
                }

                // Initialize the Google map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: centerPoint,
                    zoom: zoomLevel,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                // Add check-in location and accuracy circle
                if (hasCheckIn) {
                    const checkInLat = parseFloat({{ $attendanceDetails->check_in_lat }});
                    const checkInLon = parseFloat({{ $attendanceDetails->check_in_lon }});
                    // Using a ternary operator for the null-coalescing check
                    const checkInAccuracy = parseFloat({{ $attendanceDetails->check_in_accuracy ?? 50 }});

                    const checkInMarker = new google.maps.Marker({
                        position: { lat: checkInLat, lng: checkInLon },
                        map: map,
                        title: 'Check-in Location',
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 8,
                            strokeColor: '#fff',
                            strokeWeight: 2,
                            fillColor: '#4CAF50',
                            fillOpacity: 1
                        }
                    });

                    new google.maps.Circle({
                        strokeColor: '#4CAF50',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#4CAF50',
                        fillOpacity: 0.35,
                        map: map,
                        center: { lat: checkInLat, lng: checkInLon },
                        radius: checkInAccuracy
                    });

                    // Add an info window for the marker
                    const checkInInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <strong>Check-in Location</strong>
                            <p>Accuracy: &plusmn;${Math.round(checkInAccuracy)}m</p>
                        `
                    });
                    checkInMarker.addListener('click', () => {
                        checkInInfoWindow.open(map, checkInMarker);
                    });
                }

                // Add check-out location and accuracy circle
                if (hasCheckOut) {
                    const checkOutLat = parseFloat({{ $attendanceDetails->check_out_lat }});
                    const checkOutLon = parseFloat({{ $attendanceDetails->check_out_lon }});
                    const checkOutAccuracy = parseFloat({{ $attendanceDetails->check_out_accuracy ?? 50 }});

                    const checkOutMarker = new google.maps.Marker({
                        position: { lat: checkOutLat, lng: checkOutLon },
                        map: map,
                        title: 'Check-out Location',
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 8,
                            strokeColor: '#fff',
                            strokeWeight: 2,
                            fillColor: '#F44336',
                            fillOpacity: 1
                        }
                    });

                    new google.maps.Circle({
                        strokeColor: '#F44336',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#F44336',
                        fillOpacity: 0.35,
                        map: map,
                        center: { lat: checkOutLat, lng: checkOutLon },
                        radius: checkOutAccuracy
                    });

                    // Add an info window for the marker
                    const checkOutInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <strong>Check-out Location</strong>
                            <p>Accuracy: &plusmn;${Math.round(checkOutAccuracy)}m</p>
                        `
                    });
                    checkOutMarker.addListener('click', () => {
                        checkOutInfoWindow.open(map, checkOutMarker);
                    });
                }
            }
        }
    </script>
@endpush
