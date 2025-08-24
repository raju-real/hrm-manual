@extends('admin.layouts.app')
@section('title', 'Edit Attendance')
@push('css')
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Attendance</h4>
                <div class="page-title-right">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-arrow-circle-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ $route }}" method="POST" id="prevent-form">
                        @csrf
                        @isset($attendance)
                            @method('PUT')
                        @endisset
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Employee/Staff</label>
                                    <input type="text" value="{{ $attendance->employee->name ?? '' }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Check in</label>
                                    <input type="text" value="{{ $attendance->check_in ?? '' }}" class="form-control"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Check Out</label>
                                    <input name="check_out" type="text" value="{{ old('check_out') }}"
                                        class="form-control datetimepicker" placeholder="Check out time">
                                </div>
                            </div>


                        </div>
                        <div>
                            <x-submit-button></x-submit-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
