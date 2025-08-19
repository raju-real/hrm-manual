@extends('admin.auth.app')

@section('content')
    <form class="form-horizontal" action="{{ route('admin.2fa.verify.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Enter 2FA Code: {!! starSign() !!}</label>
            <input type="text" name="two_factor_code" id="verification-code" value="{{ old('two_factor_code') }}"
                class="form-control {{ hasError('two_factor_code') }}" placeholder="2FA Code">
            @error('two_factor_code')
                {!! displayError($message) !!}
            @enderror
        </div>

        <div class="form-check">
            <input name="remember_device" {{ old('remember_device') ? 'checked' : '' }} class="form-check-input"
                type="checkbox" id="remember-check-device">
            <label class="form-check-label" for="remember-check-device">
                Remember this device for 30 days
            </label>
        </div>

        <div class="mt-3 d-grid">
            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In
            </button>
        </div>
    </form>
@endsection
