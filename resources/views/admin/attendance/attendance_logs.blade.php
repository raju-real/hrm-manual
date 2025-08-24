@extends('admin.layouts.app')
@section('title', 'Attendance Summery')
@push('css')
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Attendance Summery</h4>
                {{-- <div class="page-title-right">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-manual-attendance" data-direction="in">
                        <i class="fa fa-plus-circle"></i> Add Attendance
                    </a>
                </div> --}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Accordion for Search -->
            <div class="accordion mb-3" id="accordionSearch">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSearch">
                        <button class="accordion-button {{ request()->query() ? '' : 'collapsed' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseSearch"
                            aria-expanded="{{ request()->query() ? 'true' : 'false' }}" aria-controls="collapseSearch">
                            Search
                        </button>
                    </h2>
                    <div id="collapseSearch" class="accordion-collapse collapse {{ request()->query() ? 'show' : '' }}"
                        aria-labelledby="headingSearch" data-bs-parent="#accordionSearch">
                        <div class="accordion-body">
                            <form method="GET" action="{{ route('admin.attendance-logs') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <select name="branch" class="form-control slim-select">
                                                <option value="" {{ !isset(request()->branch) ? 'selected' : '' }}>
                                                    Branch</option>
                                                @foreach (allBranches() as $branch)
                                                    <option value="{{ $branch->slug }}"
                                                        {{ request('branch') === $branch->slug ? 'selected' : '' }}>
                                                        {{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <select name="user" class="form-control slim-select">
                                                <option value="" {{ !isset(request()->user) ? 'selected' : '' }}>
                                                    Employee/Staff
                                                </option>
                                                @foreach (allEmployees() as $employee)
                                                    <option value="{{ $employee->employee_id }}"
                                                        {{ request('user') === $employee->employee_id ? 'selected' : '' }}>
                                                        {{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group input-clearable">
                                            <input type="text" name="start_date" id="start_date"
                                                class="form-control datepicker" value="{{ request()->start_date ?? '' }}"
                                                placeholder="Start Date" autocomplete="off" readonly>
                                            <span class="clear-btn"
                                                onclick="document.getElementById('start_date').value='';">X</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group input-clearable">
                                            <input type="text" name="end_date" id="end_date"
                                                class="form-control datepicker" value="{{ request()->end_date ?? '' }}"
                                                placeholder="End Date" autocomplete="off" readonly>
                                            <span class="clear-btn"
                                                onclick="document.getElementById('end_date').value='';">X</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select name="status" class="form-select">
                                                <option value="" {{ !isset(request()->status) ? 'selected' : '' }}>
                                                    Status</option>
                                                <option value="Present"
                                                    {{ request('status') === 'Present' ? 'selected' : '' }}>Present
                                                </option>
                                                <option value="Absent"
                                                    {{ request('status') === 'Absent' ? 'selected' : '' }}>Absent</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-0">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
                                    <th>Name</th>
                                    <th class="text-center">First Check In</th>
                                    <th class="text-center">Last Check Out</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Working Hour</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendance_summary as $attendance)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ dateFormat($attendance->attendance_date, 'd M, D, y') }}</td>
                                        <td>{{ $attendance->employee_name ?? '' }}</td>
                                        <td class="text-center">
                                            {{ $attendance->check_in ? timeFormat($attendance->check_in, 'h:i A') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $attendance->check_out ? timeFormat($attendance->check_out, 'h:i A') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $attendance->status === 'present' ? 'success' : 'danger' }}">
                                                {{ ucFirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $attendance->working_hours ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @if ($attendance->status === 'present')
                                                <a type="button" class="btn btn-sm btn-info show-punch-history"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Punch History"
                                                    data-user-id="{{ $attendance->user_id }}"
                                                    data-attendance-date="{{ $attendance->attendance_date }}">
                                                    <i class="fa fa-eye fa-xl"></i>
                                                </a>
                                            @endif
                                            {{-- @if ($attendance->status === 'present' && $attendance->check_out == null)
                                                <a href="{{ route('admin.edit-attendance',encrypt_decrypt($attendance->first_checkin_id,'encrypt')) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip">
                                                    <i class="fa fa-edit fa-xl"></i>
                                                </a>
                                            @endif --}}
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
                    {!! $attendance_summary->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/admin/js/custom/attendance_logs.js') }}"></script>
@endpush
