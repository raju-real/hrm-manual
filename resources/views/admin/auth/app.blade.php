<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}">
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    @stack('css')
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p>Sign in to continue to {{ siteSettings()['company_name'] ?? 'Ecommerce' }}.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/admin/images/profile-img.png') }}" alt=""
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <a href="{{ route('home') }}" class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}"
                                                alt="" class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>

                                <a href="{{ route('home') }}" class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}"
                                                alt="" class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <x-alert-message></x-alert-message>
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const verificationCodeInput = document.getElementById('verification-code');

        const formatInput = (value, separator = ' - ', maxLength = 11) => {
            let cleanValue = value.replace(/[^0-9]/g, ''); // Only allow numeric values
            return cleanValue.split('').join(separator).slice(0, maxLength); // Format and truncate
        };

        if (verificationCodeInput) {
            verificationCodeInput.addEventListener('input', function() {
                let value = this.value;
                let formattedValue = formatInput(value, ' - ', 21); // Format input as 2 - 5 - 8 - 7
                this.value = formattedValue;
                let cleanValueLength = value.replace(/[^0-9]/g, '').length;
                if (cleanValueLength === 2 || cleanValueLength === 5) {
                    let nextInput = this.nextElementSibling;
                    if (nextInput && nextInput.tagName === 'INPUT') {
                        nextInput.focus();
                    }
                }
            });
        }
    });
</script>

</html>
