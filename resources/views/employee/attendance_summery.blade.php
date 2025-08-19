@extends('admin.layouts.app')
@section('title','Attendance Summery')
@push('css') @endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Attendance Summery</h4>
                <div class="page-title-right">
                    @if(! hasCheckIn(Auth::user()->employee_id))
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-manual-attendance"
                           data-direction="in">
                            <i class="fa fa-check-circle"></i> Check-In
                        </a>
                    @endif

                    @if(hasCheckOut(Auth::user()->employee_id))
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-manual-attendance"
                           data-direction="out">
                            <i class="fa fa-check-circle"></i> Check-Out
                        </a>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0 text-nowrap">
                            <thead>
                            <tr>
                                <th class="text-center">Sl.no</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($attendance_summery as $attendance)
                                <tr>
                                    <td class="text-center">{{ $loop->index + 1 }}</td>
                                    <td>{{ dateFormat($attendance->attendance_date,'d, M, y') ?? '' }}</td>
                                    <td>{{ ucfirst($attendance->type) ?? 'N/A' }}</td>
                                    <td>{{ $attendance->check_in ? dateFormat($attendance->check_in,'h:i A') : '' }}</td>
                                    <td>{{ $attendance->check_out ? dateFormat($attendance->check_out,'h:i A') : '' }}</td>
                                    <td class="text-center">
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Show Location"
                                           href="{{ route('admin.attendance-location', encrypt_decrypt($attendance->id,'encrypt')) }}"
                                           class="btn btn-sm btn-soft-success"><i class="fa fa-map"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <x-no-data-found></x-no-data-found>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="d-flex justify-content-center">
                    {{--                    {!! $branches->links('pagination::bootstrap-4') !!}--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/admin/js/custom/manual_attendance.js') }}"></script>
@endpush
