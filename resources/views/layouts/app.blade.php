<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="copyright" content="MACode ID, https://macodeid.com/">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>OSA Central Hub - @yield('title', 'USTP Balubal')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">

    <!-- MACode-inspired CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/owl-carousel/css/owl.carousel.css') }}">
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom color scheme -->
    <style>
        :root {
            --primary:rgb(3, 1, 45);
            --accent:rgb(242, 255, 0);
            --secondary:rgb(0, 255, 85);
            --warning:rgb(238, 24, 24);
            --info: #05B4E1;
            --danger:rgb(123, 11, 7);
            --success:rgb(0, 132, 255);
            --dark:rgb(48, 49, 45);
            --light: #F5F9F6;
            --grey: #6E807A;
        }

        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .btn-primary:hover {
            background-color: #07be94 !important;
            border-color: #07be94 !important;
        }
        body { font-family: 'Source Sans Pro', sans-serif; color: var(--dark); }
        /* Utility: smaller, bold text available to all views */
        .small-bold { font-size: .95rem; font-weight: 700; }
        /* Ensure navbar is fixed and always visible on all pages */
        header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 1050 !important;
        }

        .navbar {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            width: 100% !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
            border: none !important;
            border-bottom: none !important;
        }
        
        header {
            border-bottom: none !important;
            box-shadow: none !important;
        }

        /* Global dashboard header - visible on all pages (smaller size) */
        .dashboard-header-global {
            position: fixed !important;
            top: 56px !important; /* Below the navbar */
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 1040 !important;
            background-color: #ffffff !important;
            border-bottom: none !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
            min-height: 50px !important;
        }

        /* Ensure main content area accounts for both fixed headers (navbar + dashboard header) */
        body {
            padding-top: 130px !important; /* navbar (~56px) + dashboard header (~74px) */
        }

        /* Additional spacing for main content */
        #main-content {
            padding-top: 0.5rem !important;
            margin-top: 0 !important;
        }

        /* Ensure Quick Actions and sidebars are visible */
        aside, .sidebar, .col-md-3, .col-md-9 {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /* Remove top margin from Quick Actions cards */
        aside .card {
            margin-top: 0 !important;
        }

        @media (max-width: 991.98px) {
            /* Adjust dashboard header on mobile */
            .dashboard-header-global {
                top: 56px !important;
                min-height: 65px !important;
            }
            body {
                padding-top: 135px !important; /* More space on mobile */
            }
        }

        /* Dashboard header styling - smaller size to prevent overlap */
        .dashboard-header-global .dashboard-header {
            margin-bottom: 0 !important;
            padding: 0.5rem 0 !important;
        }
        .dashboard-header-global .dashboard-header h1 {
            font-size: 1.5rem !important;
            font-weight: 600;
            margin-bottom: 0.25rem !important;
            line-height: 1.2;
        }
        .dashboard-header-global .dashboard-header p {
            font-size: 0.875rem !important;
            color: var(--grey);
            margin-bottom: 0 !important;
            line-height: 1.3;
        }

        /* Keep original dashboard header styles for pages that still use it */
        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: .5rem;
        }
        .dashboard-header p {
            font-size: 1rem;
            color: var(--grey);
        }

        /* Ensure sidebar and main content are below the fixed headers */
        .sidebar, .admin-sidebar-custom, .main, .container-fluid, .col-md-10, #adminMain {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        /* Dropdown styling (all screen sizes) */
        .dropdown-menu {
            margin-top: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.15);
            min-width: 200px;
            }
        .dropdown-item {
            padding: 0.75rem 1.5rem;
                display: flex;
                align-items: center;
            gap: 0.5rem;
        }
        .dropdown-item i {
            font-size: 1.1rem;
            }
        .dropdown-header {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
                display: flex;
                align-items: center;
            gap: 0.5rem;
            }
        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: #f8f9fa;
        }
        #logout-btn-mobile:hover,
        #logout-btn-mobile:focus {
            background-color: #f8f9fa !important;
            color: #212529 !important;
        }
    </style>
</head>
<body>
    
    @php use Illuminate\Support\Facades\Auth; @endphp

    <!-- Back to top button (hidden by default, shown via JS) -->
    <a href="#top" class="back-to-top" aria-label="Back to top"></a>

    <div id="top"></div>



    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif

    <header>

        <!-- Main navigation -->
    <nav class="navbar navbar-expand-lg navbar-light" aria-label="Main navigation" style="z-index:1050; box-shadow: none !important; border: none !important;">
            <div class="container-fluid px-4">
                    <a class="navbar-brand" href="{{ url('/') }}" aria-label="OSA Central Hub Home">
                        <span class="text-primary">OSA</span>Central Hub
                    </a>
                    <!-- Dropdown Menu (Always Visible) -->
                    <div class="dropdown ml-auto">
                    <button 
                            class="btn btn-primary dropdown-toggle" 
                        type="button" 
                            id="mobileNavDropdown"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                        aria-expanded="false"
                            aria-label="Menu">
                            <i class="bi bi-list"></i> Menu
                    </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mobileNavDropdown" id="mobileNavDropdownMenu">
                            @guest
                                <a class="dropdown-item" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a>
                            @else
                                    @php
                                        $profileRoute = route('student.profile');
                                        if (\Illuminate\Support\Facades\Auth::user()->role == 4) {
                                            $profileRoute = route('admin.profile');
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role == 2) {
                                            $profileRoute = route('staff.profile');
                                        } elseif (\Illuminate\Support\Facades\Auth::user()->role == 3) {
                                            $profileRoute = route('assistant.profile');
                                        }
                                    @endphp
                                <div class="dropdown-header">
                                    <i class="bi bi-person-circle"></i> Hi, {{ \Illuminate\Support\Facades\Auth::user()->first_name }}
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ $profileRoute }}">
                                    <i class="bi bi-person"></i> Profile
                                </a>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changeImageModal" onclick="event.preventDefault();">
                                    <i class="bi bi-image"></i> Change Image
                                </a>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form-mobile" class="d-inline m-0">
                                            @csrf
                                    <button type="submit" class="dropdown-item" id="logout-btn-mobile" style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1.5rem; color: #212529; cursor: pointer;">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                        </form>
                            @endguest
                        </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard Header - Visible on all pages for authenticated users -->
    @auth
        @php
            $user = auth()->user();
            $designation = $user->designation ?? optional($user->staffProfile)->designation ?? null;
            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            $computedTitle = $designation ? ($designation . ' — ' . $fullName) : $fullName;
            
            // Determine role label
            $roleLabel = 'My Dashboard';
            switch ((int) ($user->role ?? 0)) {
                case 1:
                    $roleLabel = 'My Student Dashboard';
                    break;
                case 2:
                    $roleLabel = $designation ? ($designation . ' Dashboard') : 'My Staff Dashboard';
                    break;
                case 3:
                    $roleLabel = 'My Assistant Dashboard';
                    break;
                case 4:
                    $roleLabel = 'Admin Dashboard';
                    break;
            }
        @endphp
        <div class="dashboard-header-global">
            <div class="container-fluid px-4">
                <div class="dashboard-header text-center">
                    <h1>Welcome, {{ $user->first_name }}!</h1>
                    <p class="small-bold">{{ $computedTitle }}</p>
                </div>
            </div>
        </div>
    @endauth

    <main id="main-content" class="py-4">
        @yield('content')
    </main>

    <!-- Change Image Modal -->
    @auth
    <div class="modal fade" id="changeImageModal" tabindex="-1" role="dialog" aria-labelledby="changeImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeImageModalLabel">Change Profile Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="changeImageForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="profileImage">Select Image</label>
                            <input type="file" class="form-control-file" id="profileImage" name="image" accept="image/*" required>
                            <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WEBP (Max: 10MB)</small>
                        </div>
                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                        <div id="imageUploadError" class="alert alert-danger mt-3" style="display: none;"></div>
                        <div id="imageUploadSuccess" class="alert alert-success mt-3" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="uploadImageBtn">
                            <span class="spinner-border spinner-border-sm" id="uploadSpinner" style="display: none;" role="status" aria-hidden="true"></span>
                            Upload Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('profileImage');
            const previewImg = document.getElementById('previewImg');
            const imagePreview = document.getElementById('imagePreview');
            const changeImageForm = document.getElementById('changeImageForm');
            const uploadBtn = document.getElementById('uploadImageBtn');
            const uploadSpinner = document.getElementById('uploadSpinner');
            const errorDiv = document.getElementById('imageUploadError');
            const successDiv = document.getElementById('imageUploadSuccess');

            // Preview image when selected
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                        errorDiv.style.display = 'none';
                        successDiv.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });

            // Handle form submission
            changeImageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                uploadBtn.disabled = true;
                uploadSpinner.style.display = 'inline-block';
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';

                fetch('{{ route("profile.update-image") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    uploadBtn.disabled = false;
                    uploadSpinner.style.display = 'none';
                    
                    if (data.success) {
                        successDiv.textContent = data.message;
                        successDiv.style.display = 'block';
                        
                        // Reload page after 1.5 seconds to show new image
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        errorDiv.textContent = data.message || 'Failed to upload image';
                        errorDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    uploadBtn.disabled = false;
                    uploadSpinner.style.display = 'none';
                    errorDiv.textContent = 'An error occurred: ' + error.message;
                    errorDiv.style.display = 'block';
                });
            });

            // Reset form when modal is closed
            $('#changeImageModal').on('hidden.bs.modal', function() {
                changeImageForm.reset();
                imagePreview.style.display = 'none';
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';
                uploadBtn.disabled = false;
                uploadSpinner.style.display = 'none';
            });
        });
    </script>
    @endauth

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/owl-carousel/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize WOW animations
            new WOW().init();   

            // Ensure main content is focusable for skip links (if added later)
            document.getElementById('main-content').setAttribute('tabindex', '-1');

            // Mobile dropdown menu handler
            var mobileDropdownBtn = document.getElementById('mobileNavDropdown');
            var mobileDropdownMenu = document.getElementById('mobileNavDropdownMenu');
            
            if (mobileDropdownBtn && mobileDropdownMenu) {
                // Use jQuery for Bootstrap dropdown if available, otherwise manual toggle
                if (typeof jQuery !== 'undefined' && jQuery.fn.dropdown) {
                    jQuery(mobileDropdownBtn).dropdown();
                } else {
                    // Manual toggle handler
                    mobileDropdownBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        var isExpanded = this.getAttribute('aria-expanded') === 'true';
                        
                        // Toggle dropdown - close if open, open if closed
                        if (isExpanded) {
                            // Close dropdown
                            mobileDropdownMenu.classList.remove('show');
                            this.setAttribute('aria-expanded', 'false');
                            this.classList.remove('show');
                        } else {
                            // Open dropdown
                            mobileDropdownMenu.classList.add('show');
                            this.setAttribute('aria-expanded', 'true');
                            this.classList.add('show');
                        }
                    });
                }
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (mobileDropdownBtn && mobileDropdownMenu) {
                        var isClickInside = mobileDropdownBtn.contains(e.target) || mobileDropdownMenu.contains(e.target);
                        
                        if (!isClickInside) {
                            mobileDropdownMenu.classList.remove('show');
                            mobileDropdownBtn.setAttribute('aria-expanded', 'false');
                            mobileDropdownBtn.classList.remove('show');
                        }
                    }
                });
                
                // Close dropdown when clicking on dropdown items (except logout form)
                if (mobileDropdownMenu) {
                    mobileDropdownMenu.addEventListener('click', function(e) {
                        // Don't close if clicking on logout button (form submission)
                        if (e.target.id !== 'logout-btn-mobile' && e.target.closest('#logout-form-mobile') === null) {
                            // Close after a short delay to allow navigation
                            setTimeout(function() {
                                mobileDropdownMenu.classList.remove('show');
                                mobileDropdownBtn.setAttribute('aria-expanded', 'false');
                                mobileDropdownBtn.classList.remove('show');
                            }, 100);
                        }
                    });
                }
            }

            // Bootstrap dropdowns will work automatically with data attributes
            // No need for explicit initialization

            // Auto-dismiss flash alerts after 3 seconds (Bootstrap 4 compatible)
            setTimeout(function() {
                document.querySelectorAll('.alert.alert-dismissible').forEach(function(alert) {
                    var closeBtn = alert.querySelector('[data-dismiss="alert"]');
                    if (closeBtn) {
                        closeBtn.click();
                    } else {
                        // Fallback: fade out and remove
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 500);
                    }
                });
            }, 3000);

            // Handle logout form submission to prevent CSRF errors (desktop)
            const logoutForm = document.getElementById('logout-form');
            const logoutBtn = document.getElementById('logout-btn');
            
            // Handle logout form submission for mobile
            const logoutFormMobile = document.getElementById('logout-form-mobile');
            const logoutBtnMobile = document.getElementById('logout-btn-mobile');
            
            function handleLogout(form, btn) {
                if (!form || !btn) return;
                
                let isSubmitting = false;
                
                form.addEventListener('submit', function(e) {
                    // Prevent double submission
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Always refresh CSRF token from meta tag before submission
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfInput = form.querySelector('input[name="_token"]');
                    
                    if (csrfMeta && csrfInput) {
                        const freshToken = csrfMeta.getAttribute('content');
                        if (freshToken) {
                            csrfInput.value = freshToken;
                        }
                    }
                    
                    isSubmitting = true;
                    btn.disabled = true;
                    if (btn.textContent !== undefined) {
                        btn.textContent = 'Logging out...';
                    }
                });
            }
            
            // Handle desktop logout
            handleLogout(logoutForm, logoutBtn);
            
            // Handle mobile logout
            handleLogout(logoutFormMobile, logoutBtnMobile);
                
                // Also update CSRF token on page visibility change (in case session refreshed)
                document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const forms = [logoutForm, logoutFormMobile].filter(Boolean);
                    forms.forEach(function(form) {
                        const csrfInput = form.querySelector('input[name="_token"]');
                        if (csrfMeta && csrfInput) {
                            const freshToken = csrfMeta.getAttribute('content');
                            if (freshToken) {
                                csrfInput.value = freshToken;
                            }
                        }
                    });
                    }
                });
            
            // Global CSRF token refresh for all forms before submission
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.tagName === 'FORM') {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfInput = form.querySelector('input[name="_token"]');
                    
                    if (csrfMeta && csrfInput) {
                        const freshToken = csrfMeta.getAttribute('content');
                        if (freshToken) {
                            csrfInput.value = freshToken;
                        }
                    }
                }
            }, true); // Use capture phase to refresh before form submission
            
            // Refresh CSRF token periodically (every 30 minutes) to prevent expiration
            setInterval(function() {
                // Refresh the page's CSRF token by making a lightweight request
                fetch(window.location.href, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                }).then(function(response) {
                    // CSRF token is automatically updated in the meta tag via Laravel
                    // The meta tag should already be up to date
                }).catch(function(error) {
                    console.warn('CSRF token refresh check failed:', error);
                });
            }, 30 * 60 * 1000); // Every 30 minutes
        });
    </script>

    @stack('scripts')
</body>
</html>