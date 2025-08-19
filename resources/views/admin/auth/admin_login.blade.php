@extends('admin.auth.app')
@section('title', 'Admin Login')

@section('content')
    <form class="form-horizontal" action="{{ route('admin-login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email {!! starSign() !!}</label>
            <input type="text" name="email" value="{{ old('email') }}" class="form-control {{ hasError('email') }}"
                placeholder="Email" autocomplete="on" autofocus>
            @error('email')
                {!! displayError($message) !!}
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password {!! starSign() !!}</label>
            <input name="password" type="password" class="form-control {{ hasError('password') }}" placeholder="Password"
                autocomplete="off">
            @error('password')
                {!! displayError($message) !!}
            @enderror
        </div>

        <div class="form-check">
            <input name="remember" {{ old('remember') ? 'checked' : '' }} class="form-check-input" type="checkbox"
                id="remember-check">
            <label class="form-check-label" for="remember-check">
                Remember me
            </label>
        </div>

        <div class="mt-3 d-grid">
            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In
            </button>
        </div>
    </form>
@endsection
