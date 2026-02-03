@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Flip Physics Configuration
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">{{ $flipbook->title }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button onclick="saveSettings()" class="btn btn-sm btn-success">
                        <i class="ki-duotone ki-check fs-3"></i>Save & Continue
                    </button>
                    <a href="{{ route('customer.catalog.index') }}" class="btn btn-sm btn-light">Back to Catalog</a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="row g-5">
                    <!-- Preview -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Live Preview</h3>
                            </div>
                            <div class="card-body" style="min-height: 600px; background: #f5f5f5;">
                                <div class="text-center py-20">
                                    <i class="ki-duotone ki-file fs-5x text-muted mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <p class="text-muted">Preview will be available after publishing</p>
                                    <p class="text-muted fs-7">Configure the flip animation settings on the right</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Panel -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Animation Settings</h3>
                            </div>
                            <div class="card-body">
                                <form id="physicsForm">
                                    <!-- Flip Speed -->
                                    <div class="mb-10">
                                        <label class="form-label">Flip Speed</label>
                                        <input type="range" id="flip_speed" name="flip_speed" class="form-range"
                                            min="0.1" max="5" step="0.1"
                                            value="{{ old('flip_speed', $flipbook->flip_physics['flip_speed'] ?? 1) }}">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted fs-7">Slow (0.1s)</span>
                                            <span class="text-primary fw-bold"
                                                id="speedValue">{{ $flipbook->flip_physics['flip_speed'] ?? 1 }}s</span>
                                            <span class="text-muted fs-7">Fast (5s)</span>
                                        </div>
                                    </div>

                                    <!-- Flip Style -->
                                    <div class="mb-10">
                                        <label class="form-label">Flip Style</label>
                                        <select name="flip_style" class="form-select">
                                            <option value="smooth"
                                                {{ ($flipbook->flip_physics['flip_style'] ?? 'smooth') === 'smooth' ? 'selected' : '' }}>
                                                Smooth - Gentle easing</option>
                                            <option value="realistic"
                                                {{ ($flipbook->flip_physics['flip_style'] ?? '') === 'realistic' ? 'selected' : '' }}>
                                                Realistic - Natural page curl</option>
                                            <option value="fast"
                                                {{ ($flipbook->flip_physics['flip_style'] ?? '') === 'fast' ? 'selected' : '' }}>
                                                Fast - Instant flip</option>
                                        </select>
                                    </div>

                                    <!-- Page Curl Effect -->
                                    <div class="form-check form-switch mb-10">
                                        <input class="form-check-input" type="checkbox" name="page_curl" id="page_curl"
                                            {{ $flipbook->flip_physics['page_curl'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="page_curl">
                                            <strong>Page Curl Effect</strong>
                                            <span class="text-muted d-block fs-7">3D page curl during flip</span>
                                        </label>
                                    </div>

                                    <!-- Shadow Effect -->
                                    <div class="form-check form-switch mb-10">
                                        <input class="form-check-input" type="checkbox" name="shadow_effect"
                                            id="shadow_effect"
                                            {{ $flipbook->flip_physics['shadow_effect'] ?? true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shadow_effect">
                                            <strong>Shadow Effect</strong>
                                            <span class="text-muted d-block fs-7">Dynamic shadows on pages</span>
                                        </label>
                                    </div>

                                    <!-- Sound Enabled -->
                                    <div class="form-check form-switch mb-10">
                                        <input class="form-check-input" type="checkbox" name="sound_enabled"
                                            id="sound_enabled"
                                            {{ $flipbook->flip_physics['sound_enabled'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sound_enabled">
                                            <strong>Page Turn Sound</strong>
                                            <span class="text-muted d-block fs-7">Audio feedback on flip</span>
                                        </label>
                                    </div>

                                    <div class="separator my-7"></div>

                                    <!-- Auto Flip -->
                                    <div class="form-check form-switch mb-5">
                                        <input class="form-check-input" type="checkbox" name="auto_flip" id="auto_flip"
                                            {{ $flipbook->flip_physics['auto_flip'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_flip">
                                            <strong>Auto Flip Pages</strong>
                                            <span class="text-muted d-block fs-7">Automatically advance pages</span>
                                        </label>
                                    </div>

                                    <!-- Auto Flip Delay -->
                                    <div class="mb-10" id="autoFlipDelayContainer"
                                        style="display: {{ $flipbook->flip_physics['auto_flip'] ?? false ? 'block' : 'none' }};">
                                        <label class="form-label">Auto Flip Delay (seconds)</label>
                                        <input type="number" name="auto_flip_delay" class="form-control" min="1"
                                            max="60" value="{{ $flipbook->flip_physics['auto_flip_delay'] ?? 5 }}">
                                    </div>

                                    <div class="separator my-7"></div>

                                    <!-- Drag Sensitivity -->
                                    <div class="mb-10">
                                        <label class="form-label">Drag Sensitivity</label>
                                        <input type="range" name="drag_sensitivity" class="form-range" min="0.1"
                                            max="2" step="0.1"
                                            value="{{ $flipbook->flip_physics['drag_sensitivity'] ?? 1 }}">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted fs-7">Low (0.1)</span>
                                            <span class="text-muted fs-7">High (2)</span>
                                        </div>
                                    </div>

                                    <div class="alert alert-info d-flex align-items-center p-5">
                                        <i class="ki-duotone ki-information-5 fs-2x text-info me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <span class="fs-7">Changes will be applied after saving and publishing your
                                            flipbook</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const flipbookId = {{ $flipbook->id }};

            // Update speed value display
            $('#flip_speed').on('input', function() {
                $('#speedValue').text($(this).val() + 's');
            });

            // Toggle auto flip delay visibility
            $('#auto_flip').on('change', function() {
                $('#autoFlipDelayContainer').toggle($(this).is(':checked'));
            });

            function saveSettings() {
                const formData = {
                    flip_physics: {
                        flip_speed: parseFloat($('#flip_speed').val()),
                        flip_style: $('[name="flip_style"]').val(),
                        page_curl: $('#page_curl').is(':checked'),
                        shadow_effect: $('#shadow_effect').is(':checked'),
                        sound_enabled: $('#sound_enabled').is(':checked'),
                        auto_flip: $('#auto_flip').is(':checked'),
                        auto_flip_delay: parseInt($('[name="auto_flip_delay"]').val()),
                        drag_sensitivity: parseFloat($('[name="drag_sensitivity"]').val())
                    }
                };

                $.ajax({
                    url: `/customer/template/${flipbookId}/config`,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Saved!',
                            text: 'Flip physics configuration saved successfully',
                            icon: 'success',
                            confirmButtonText: 'Continue'
                        }).then(() => {
                            window.location.href = '{{ route('customer.catalog.index') }}';
                        });
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseJSON);
                        Swal.fire('Error!', 'Failed to save settings', 'error');
                    }
                });
            }
        </script>
    @endpush
@endsection
