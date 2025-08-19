@extends('admin.layouts.app')
@section('title', 'Verify Mobile')
@push('css')
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" id="mobile" value="{{ authAdmin()->mobile ?? '' }}" placeholder="Mobile"
                                    class="form-control" readonly>
                                <small class="text-danger font-weight-500 mobile-error"></small> <!-- Error display for mobile -->
                            </div>
                            <button id="send-code-btn" class="btn btn-md btn-primary submit-button" type="submit">Send Verification
                                Code</button>
                        </div>
                    </div>

                    <div class="row" id="verification-section" style="display: none;">
                        <div class="col-md-4">
                            <div class="mb-3 mt-2">
                                <label for="verification-code">Enter Verification Code</label>
                                <input type="text" id="verification-code" class="form-control" placeholder="Enter Code" autocomplete="off" autofocus>
                                <span class="verification-code-error font-weight-500 text-danger"></span>
                            </div>
                            <button id="verify-code-btn" class="btn btn-md btn-primary">Verify Code</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/admin/js/custom/mobile_verification.js') }}"></script>
@endpush
