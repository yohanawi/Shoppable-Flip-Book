<x-default-layout>

    @section('title')
        Account Settings
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customer.settings.index') }}
    @endsection

    <div id="kt_app_content_container">

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4 mb-5 mb-sm-0">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <h4 class="fw-semibold">Success!</h4>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button"
                    class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                    data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-success">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
        @endif

        {{-- Profile Card with Avatar --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <div class="symbol-label fs-2 fw-semibold text-inverse-primary bg-light-primary">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border-4 border-body h-20px w-20px">
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                        {{ $user->name }}
                                    </a>
                                    <a href="#">
                                        <i class="ki-duotone ki-verify fs-1 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </div>

                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <a href="mailto:{{ $user->email }}"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-sms fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ $user->email }}
                                    </a>
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                        <i class="ki-duotone ki-calendar fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Joined {{ $user->created_at->format('M Y') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="d-flex flex-wrap">
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-shield-tick fs-3 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="fs-2 fw-bold" data-kt-countup="true"
                                                data-kt-countup-value="4500">0</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Active Sessions</div>
                                    </div>

                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-lock fs-3 text-warning me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-value="60">
                                                0</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Last Login</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="#">
                            Overview
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Settings Cards --}}
        <div class="row g-6 g-xl-9">
            {{-- Profile Information --}}
            <div class="col-lg-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Profile Information</span>
                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Update your account details</span>
                        </h3>
                    </div>

                    <div class="card-body pt-5">
                        <form action="{{ route('customer.settings.update-profile') }}" method="POST"
                            id="kt_profile_form">
                            @csrf
                            @method('PUT')

                            <div class="mb-7 fv-row">
                                <label class="fs-6 fw-semibold form-label required">
                                    <span>Full Name</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Your full name as it appears on official documents"></i>
                                </label>
                                <input type="text" name="name"
                                    class="form-control form-control-solid @error('name') is-invalid @enderror"
                                    placeholder="Enter your full name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-10 fv-row">
                                <label class="fs-6 fw-semibold form-label required">
                                    <span>Email Address</span>
                                </label>
                                <input type="email" name="email"
                                    class="form-control form-control-solid @error('email') is-invalid @enderror"
                                    placeholder="Enter your email address" value="{{ old('email', $user->email) }}"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">We'll never share your email with anyone else.</div>
                            </div>

                            <div class="separator separator-dashed my-6"></div>

                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-3">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="col-lg-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Change Password</span>
                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Update your password
                                regularly</span>
                        </h3>
                    </div>

                    <div class="card-body pt-5">
                        <form action="{{ route('customer.settings.update-password') }}" method="POST"
                            id="kt_password_form">
                            @csrf
                            @method('PUT')

                            <div class="mb-7 fv-row" data-kt-password-meter="true">
                                <label class="fs-6 fw-semibold form-label required">
                                    <span>Current Password</span>
                                </label>
                                <div class="position-relative mb-3">
                                    <input type="password" name="current_password"
                                        class="form-control form-control-solid @error('current_password') is-invalid @enderror"
                                        placeholder="Enter current password" autocomplete="off" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-7 fv-row" data-kt-password-meter="true">
                                <label class="fs-6 fw-semibold form-label required">
                                    <span>New Password</span>
                                </label>
                                <div class="position-relative mb-3">
                                    <input type="password" name="password"
                                        class="form-control form-control-solid @error('password') is-invalid @enderror"
                                        placeholder="Enter new password" autocomplete="off" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="text-muted">
                                    Use 8 or more characters with a mix of letters, numbers & symbols.
                                </div>
                            </div>

                            <div class="mb-10 fv-row">
                                <label class="fs-6 fw-semibold form-label required">
                                    <span>Confirm New Password</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                    class="form-control form-control-solid" placeholder="Confirm new password"
                                    autocomplete="off" required>
                            </div>

                            <div class="separator separator-dashed my-6"></div>

                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-3">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-lock fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Security Settings --}}
        <div class="card mt-6 mt-xl-9">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Security Recommendations</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Keep your account secure</span>
                </h3>
            </div>

            <div class="card-body py-4">
                <div class="row g-6">
                    <div class="col-md-4">
                        <div class="d-flex flex-stack">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-50px me-4">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-shield-tick fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Strong
                                        Password</a>
                                    <span class="text-muted fw-semibold d-block fs-7">Use complex
                                        passwords</span>
                                </div>
                            </div>
                            <span class="badge badge-light-success fs-8 fw-bold">Active</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex flex-stack">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-50px me-4">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-fingerprint-scanning fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Two-Factor
                                        Auth</a>
                                    <span class="text-muted fw-semibold d-block fs-7">Enable 2FA
                                        security</span>
                                </div>
                            </div>
                            <span class="badge badge-light-warning fs-8 fw-bold">Inactive</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex flex-stack">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-50px me-4">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-notification-on fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Login
                                        Alerts</a>
                                    <span class="text-muted fw-semibold d-block fs-7">Get notified of
                                        logins</span>
                                </div>
                            </div>
                            <span class="badge badge-light-success fs-8 fw-bold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Form submission handling
            document.getElementById('kt_profile_form').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="indicator-label">Please wait...</span>';
            });

            document.getElementById('kt_password_form').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="indicator-label">Please wait...</span>';
            });
        </script>
    @endpush

</x-default-layout>
