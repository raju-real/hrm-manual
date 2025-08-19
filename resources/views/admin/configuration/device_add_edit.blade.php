@extends('admin.layouts.app')
@section('title','Device Add/Edit')
@push('css') @endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Device Add/Edit</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-primary">
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
                        @isset($device)
                            @method('PUT')
                        @endisset

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Name {!! starSign() !!}</label>
                                    <input type="text" name="name" value="{{ old('name') ?? $device->name ?? '' }}"
                                           class="form-control {{ hasError('name') }}"
                                           placeholder="Name">
                                    @error('name')
                                    {!! displayError($message) !!}
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Serial No. {!! starSign() !!}</label>
                                    <input type="text" name="serial_no" value="{{ old('serial_no') ?? $device->serial_no ?? '' }}"
                                           class="form-control {{ hasError('serial_no') }}"
                                           placeholder="Serial No.">
                                    @error('serial_no')
                                    {!! displayError($message) !!}
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Branch {!! starSign() !!}</label>
                                    <select name="branch" class="form-control select2 {{ hasError('branches') }} ">
                                        <option value="">Select Branch</option>
                                        @foreach(allBranches() as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch') == $branch->id || isset($device) && $device->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch')
                                    {!! displayError($message) !!}
                                    @enderror
                                </div>
                            </div>

                             <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status {!! starSign() !!}</label>
                                    <select name="status"
                                            class="form-select select2-search-disable {{ hasError('status') }}">
                                        @foreach(getStatus() as $status)
                                            <option
                                                value="{{ $status->value }}" {{ (old('status') === $status->value || (isset($staff) && $staff->status === $status->value && empty(old('status')))) ? 'selected' : '' }}>{{ $status->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    {!! displayError($message) !!}
                                    @enderror
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

@push('js') @endpush
