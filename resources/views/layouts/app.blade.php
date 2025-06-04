<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Capstone') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --navbar-height: 60px;
            --primary-color: #22bbea;
            --secondary-color: #ff9933;
            --success-color: #10b981;
            --bg-color: rgb(250, 249, 251);
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--bg-color);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            background-color: #ffffff;
            box-shadow: var(--card-shadow);
        }

        .sidebar-heading {
            font-size: 0.65rem;
            letter-spacing: 0.1rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(0, 0, 0, 0.7);
            transition: all 0.2s;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .sidebar .nav-link .icon {
            font-size: 1.1rem;
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #22bbea;
            font-weight: bold;
        }
        
        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: var(--primary-color);
            transform: translateX(3px);
        }

        .sidebar hr {
            margin: 1rem 0;
            opacity: 0.15;
        }

        .logout-btn {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #dc3545;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                padding: 0.75rem;
            }

            .sidebar-header {
                padding: 1rem;
            }

            .sidebar .nav-link {
                padding: 0.6rem 0.75rem;
            }

            .sidebar-category {
                margin: 1.25rem 0 0.5rem 0.5rem;
            }
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 1.25rem;
        }

        .nav-link {
            color: var(--secondary-color);
            transition: all 0.2s ease-in-out;
            border-radius: 0.35rem;
            margin: 0.2rem 0;
        }

        .nav-link.active {
            color: #ffffff;
            background-color: #22bbea;
            font-weight: bold;
        }
        
        .nav-link:hover {
            color: #ffffff;
            background-color: var(--primary-color);
            transform: translateX(3px);
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Table Styles */
        .table th {
            font-weight: 600;
            color: var(--secondary-color);
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        /* Button Styles */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #e68900;
            border-color: #e68900;
        }
    </style>

    @stack('styles')
</head>
<body>
    @auth
        @if(request()->is('login') || request()->is('register'))
            @yield('content')
        @else
            <!-- Sidebar -->
            @if(Auth::user()->role == 'student')
                @include('Component.student-sidebar')
                <!-- Header -->
                @include('Component.student-header')
            @elseif(Auth::user()->role == 'cook')
                @include('Component.cook-sidebar')
                <!-- Header -->
                @include('Component.cook-header')
            @elseif(Auth::user()->role == 'kitchen')
                @include('Component.kitchen-sidebar')
                <!-- Header -->
                @include('Component.kitchen-header')
            @else
                @include('Component.admin-sidebar')
                <!-- Header -->
                @include('Component.admin-header')
            @endif

            <!-- Main Content -->
            <main class="main-content" style="margin-left:250px; padding-top:60px; min-height:calc(100vh - 70px); margin-top:20px; background:#f8f9fc;">
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')
            </main>
        @endif
    @else
        <div class="auth-wrapper d-flex align-items-center justify-content-center p-4">
            <div class="auth-card p-4" style="width: 100%; max-width: 420px;">
                @yield('content')
            </div>
        </div>
    @endauth

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Scripts -->
    <script>
        // Add active class to current nav item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Mobile sidebar toggle
            const mobileSidebar = document.getElementById('mobileSidebar');
            if (mobileSidebar) {
                const bsOffcanvas = new bootstrap.Offcanvas(mobileSidebar);
                
                document.querySelector('[data-bs-target="#mobileSidebar"]').addEventListener('click', function(e) {
                    e.preventDefault();
                    bsOffcanvas.show();
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
