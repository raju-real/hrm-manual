<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Attendance Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" base_url="{!! url('/') !!}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f7fa;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Fixed Header */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            height: 65px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        header a.logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        header a.logo img {
            max-height: 45px;
        }

        header .header-right a {
            font-size: 20px;
            color: #333;
            margin-left: 15px;
            transition: transform 0.2s, color 0.2s;
        }

        header .header-right a:hover {
            transform: scale(1.2);
            color: #1cc88a;
        }

        /* Main content */
        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding-top: 100px;
            padding-bottom: 60px;
        }

        .attendance-container {
            text-align: center;
            background: #fff;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 90%;
        }

        .attendance-title {
            font-size: 26px;
            font-weight: 600;
            color: #333;
        }

        .attendance-quote {
            font-style: italic;
            color: #666;
            margin: 8px 0 15px 0;
            font-size: 14px;
        }

        .user-info {
            color: #333;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .designation {
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .date-display {
            font-size: 18px;
            margin-bottom: 25px;
            color: #555;
        }

        .btn-attendance {
            width: 160px;
            height: 60px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            margin: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-attendance img {
            max-height: 24px;
            margin-right: 8px;
        }

        .btn-attendance:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        footer {
            width: 100%;
            text-align: center;
            padding: 15px 0;
            background: #1cc88a;
            color: #fff;
            font-weight: 500;
            font-size: 16px;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1100;
        }

        @media (max-width: 480px) {
            .btn-attendance {
                width: 100%;
                height: 55px;
                font-size: 16px;
            }

            .attendance-title {
                font-size: 22px;
            }

            header .header-right a {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>

    <header>
        <a href="#" class="logo">
            <img src="{{ asset(siteSettings()['logo']) ?? (devLogo() ?? '') }}" alt="Company Logo">
        </a>
        <div class="header-right">
            <a href="{{ route('admin.check-in-out') }}" title="Reload"><i class="fas fa-sync-alt"></i></a>
            <a href="{{ route('admin.logout') }}" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <div class="main-content">
        <div class="attendance-container">
            <h2 class="attendance-title">Attendance Management</h2>
            <div class="attendance-quote">"Punctuality is the soul of business."</div>
            <div class="user-info">{{ Auth::user()->name ?? '' }}</div>
            <div class="designation">{{ Auth::user()->designation->name ?? '' }}</div>
            <div class="date-display" id="currentDate">{{ date('d M D, Y') }}</div>

            <!-- Centered Toast -->
            <div class="toast-container">
                <div id="attendanceToast" class="toast text-white bg-primary border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="toast-body position-relative text-center">
                        <span id="toastMessage">Message goes here</span>
                        <button type="button"
                            class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-2"
                            data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <!-- Check-In / Check-Out Buttons -->
            <div class="d-flex flex-column align-items-center">
                <div class="alert-container mb-2">
                    {{-- @if (hasCheckIn() && hasCheckOut()) --}}
                    {{-- <div class="alert alert-success text-center py-1" style="min-height:35px;">
                            Today's attendance completed successfully!
                        </div> --}}
                    {{-- @endif --}}
                </div>
                <button class="btn btn-success btn-attendance btn-manual-attendance mb-3" data-direction="in"
                    id="checkInBtn" {{ hasOpenAttendance() === 'in_only' ? 'disabled' : '' }}>
                    <img src="https://img.icons8.com/ios-filled/50/ffffff/login-rounded-right.png" />
                    Check-In
                </button>


                {{-- @if (!hasCheckOut()) --}}
                <button class="btn btn-danger btn-attendance btn-manual-attendance" data-direction="out"
                    id="checkOutBtn" {{ hasOpenAttendance() === 'completed' || hasOpenAttendance() === 'none' ? 'disabled' : '' }}>
                    <img src="https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png" />
                    Check-Out
                </button>
                {{-- @endif --}}
            </div>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y', strtotime(now())) }} {{ siteSettings()['company_name'] ?? '' }}
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="{{ asset('assets/admin/js/custom/manual_attendance.js') }}"></script>

</body>

</html>
