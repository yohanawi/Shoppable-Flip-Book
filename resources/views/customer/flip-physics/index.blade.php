@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Flip Physics Configuration - {{ $flipbook->title }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.catalog.index') }}"
                                class="text-muted text-hover-primary">Catalog</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Flip Physics</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('customer.catalog.show', $flipbook) }}" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-arrow-left fs-4"></i> Back to Flipbook
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="row g-5">
                    <!-- Left Panel - Configuration -->
                    <div class="col-lg-8">
                        <!-- Presets Card -->
                        <div class="card mb-5">
                            <div class="card-header">
                                <h3 class="card-title">Presets</h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-4" id="presetsContainer">
                                    @foreach ($presets as $preset)
                                        <div class="col-md-6">
                                            <div class="preset-card card border {{ $currentPhysics['preset_id'] == $preset->id ? 'border-primary' : '' }}"
                                                data-preset-id="{{ $preset->id }}" style="cursor: pointer;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="mb-0">{{ $preset->name }}</h5>
                                                        @if ($preset->is_default)
                                                            <span class="badge badge-light-success">Default</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-muted small mb-0">{{ $preset->description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Custom Parameters Card -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Custom Parameters</h3>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-light-warning" id="resetBtn">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset to Default
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="physicsForm">
                                    <!-- Duration -->
                                    <div class="mb-6">
                                        <label class="form-label fw-bold">Duration (ms)</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" class="form-range flex-grow-1" id="duration"
                                                name="duration" min="100" max="3000" step="50"
                                                value="{{ $currentPhysics['parameters']['duration'] ?? 800 }}">
                                            <span class="badge badge-light-primary" id="durationValue">
                                                {{ $currentPhysics['parameters']['duration'] ?? 800 }}
                                            </span>
                                        </div>
                                        <div class="form-text">Controls how long the flip animation takes</div>
                                    </div>

                                    <!-- Acceleration -->
                                    <div class="mb-6">
                                        <label class="form-label fw-bold">Acceleration</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" class="form-range flex-grow-1" id="acceleration"
                                                name="acceleration" min="0.1" max="5.0" step="0.1"
                                                value="{{ $currentPhysics['parameters']['acceleration'] ?? 1.5 }}">
                                            <span class="badge badge-light-primary" id="accelerationValue">
                                                {{ $currentPhysics['parameters']['acceleration'] ?? 1.5 }}
                                            </span>
                                        </div>
                                        <div class="form-text">Speed at which the page flip accelerates</div>
                                    </div>

                                    <!-- Hardness -->
                                    <div class="mb-6">
                                        <label class="form-label fw-bold">Hardness</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" class="form-range flex-grow-1" id="hardness"
                                                name="hardness" min="0" max="50" step="1"
                                                value="{{ $currentPhysics['parameters']['hardness'] ?? 10 }}">
                                            <span class="badge badge-light-primary" id="hardnessValue">
                                                {{ $currentPhysics['parameters']['hardness'] ?? 10 }}
                                            </span>
                                        </div>
                                        <div class="form-text">Stiffness of the pages (higher = stiffer)</div>
                                    </div>

                                    <!-- Elevation -->
                                    <div class="mb-6">
                                        <label class="form-label fw-bold">Elevation</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" class="form-range flex-grow-1" id="elevation"
                                                name="elevation" min="0" max="1000" step="10"
                                                value="{{ $currentPhysics['parameters']['elevation'] ?? 300 }}">
                                            <span class="badge badge-light-primary" id="elevationValue">
                                                {{ $currentPhysics['parameters']['elevation'] ?? 300 }}
                                            </span>
                                        </div>
                                        <div class="form-text">Height of the page curl during flip</div>
                                    </div>

                                    <!-- Corners -->
                                    <div class="mb-6">
                                        <label class="form-label fw-bold">Active Corners</label>
                                        <select class="form-select" id="corners" name="corners">
                                            <option value="forward"
                                                {{ ($currentPhysics['parameters']['corners'] ?? 'forward') == 'forward' ? 'selected' : '' }}>
                                                Forward Only (Right/Bottom)
                                            </option>
                                            <option value="backward"
                                                {{ ($currentPhysics['parameters']['corners'] ?? 'forward') == 'backward' ? 'selected' : '' }}>
                                                Backward Only (Left/Top)
                                            </option>
                                            <option value="all"
                                                {{ ($currentPhysics['parameters']['corners'] ?? 'forward') == 'all' ? 'selected' : '' }}>
                                                All Corners
                                            </option>
                                        </select>
                                        <div class="form-text">Which corners can be dragged to flip pages</div>
                                    </div>

                                    <!-- Start Flip Angle -->
                                    <div class="mb-6">
                                        <label class="form-label fw-bold">Start Flip Angle (degrees)</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="range" class="form-range flex-grow-1" id="startFlipAngle"
                                                name="startFlipAngle" min="-90" max="90" step="5"
                                                value="{{ $currentPhysics['parameters']['startFlipAngle'] ?? 0 }}">
                                            <span class="badge badge-light-primary" id="startFlipAngleValue">
                                                {{ $currentPhysics['parameters']['startFlipAngle'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div class="form-text">Initial angle when flip starts</div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light" id="previewBtn">
                                            <i class="bi bi-play-circle"></i> Preview
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Save Configuration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Preview -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h3 class="card-title">Live Preview</h3>
                            </div>
                            <div class="card-body text-center">
                                <div id="flipPreview" class="mb-4"
                                    style="position: relative; width: 100%; height: 400px; background: #f5f5f5; border-radius: 8px; overflow: hidden;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-muted">
                                            <i class="bi bi-book fs-3x mb-3"></i>
                                            <p>Preview will appear here</p>
                                            <button type="button" class="btn btn-sm btn-light-primary" id="testFlipBtn">
                                                Test Flip Animation
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Settings Display -->
                                <div class="bg-light rounded p-4">
                                    <h5 class="mb-3">Current Settings</h5>
                                    <div class="text-start">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Duration:</span>
                                            <span class="fw-bold" id="previewDuration">800ms</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Acceleration:</span>
                                            <span class="fw-bold" id="previewAcceleration">1.5</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Hardness:</span>
                                            <span class="fw-bold" id="previewHardness">10</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Elevation:</span>
                                            <span class="fw-bold" id="previewElevation">300</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Corners:</span>
                                            <span class="fw-bold" id="previewCorners">Forward</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Start Angle:</span>
                                            <span class="fw-bold" id="previewStartAngle">0°</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const flipbookId = {{ $flipbook->id }};
        const csrfToken = '{{ csrf_token() }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Range input updates
            const rangeInputs = ['duration', 'acceleration', 'hardness', 'elevation', 'startFlipAngle'];
            rangeInputs.forEach(inputName => {
                const input = document.getElementById(inputName);
                const valueDisplay = document.getElementById(`${inputName}Value`);

                input.addEventListener('input', function() {
                    valueDisplay.textContent = this.value;
                    updatePreviewDisplay();
                });
            });

            // Corners select update
            document.getElementById('corners').addEventListener('change', updatePreviewDisplay);

            // Update preview display
            function updatePreviewDisplay() {
                document.getElementById('previewDuration').textContent = document.getElementById('duration').value +
                    'ms';
                document.getElementById('previewAcceleration').textContent = document.getElementById('acceleration')
                    .value;
                document.getElementById('previewHardness').textContent = document.getElementById('hardness').value;
                document.getElementById('previewElevation').textContent = document.getElementById('elevation')
                .value;
                document.getElementById('previewCorners').textContent = document.getElementById('corners').options[
                    document.getElementById('corners').selectedIndex].text;
                document.getElementById('previewStartAngle').textContent = document.getElementById('startFlipAngle')
                    .value + '°';
            }

            // Preset selection
            document.querySelectorAll('.preset-card').forEach(card => {
                card.addEventListener('click', function() {
                    const presetId = this.getAttribute('data-preset-id');

                    // Visual feedback
                    document.querySelectorAll('.preset-card').forEach(c => {
                        c.classList.remove('border-primary');
                    });
                    this.classList.add('border-primary');

                    // Apply preset
                    fetch(`/customer/physics/${flipbookId}/apply-preset`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                preset_id: presetId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update form with preset values
                                const params = data.physics.parameters;
                                document.getElementById('duration').value = params.duration;
                                document.getElementById('acceleration').value = params
                                    .acceleration;
                                document.getElementById('hardness').value = params.hardness;
                                document.getElementById('elevation').value = params.elevation;
                                document.getElementById('corners').value = params.corners;
                                document.getElementById('startFlipAngle').value = params
                                    .startFlipAngle;

                                // Update displays
                                rangeInputs.forEach(inputName => {
                                    const input = document.getElementById(inputName);
                                    const valueDisplay = document.getElementById(
                                        `${inputName}Value`);
                                    valueDisplay.textContent = input.value;
                                });
                                updatePreviewDisplay();

                                Swal.fire('Success!', data.message, 'success');
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Failed to apply preset.', 'error');
                        });
                });
            });

            // Save configuration
            document.getElementById('physicsForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const parameters = {
                    duration: parseInt(document.getElementById('duration').value),
                    acceleration: parseFloat(document.getElementById('acceleration').value),
                    hardness: parseInt(document.getElementById('hardness').value),
                    elevation: parseInt(document.getElementById('elevation').value),
                    corners: document.getElementById('corners').value,
                    startFlipAngle: parseInt(document.getElementById('startFlipAngle').value)
                };

                fetch(`/customer/physics/${flipbookId}/save`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            parameters
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', data.message, 'success');
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Failed to save configuration.', 'error');
                    });
            });

            // Reset to default
            document.getElementById('resetBtn').addEventListener('click', function() {
                Swal.fire({
                    title: 'Reset to Default?',
                    text: "This will reset all physics parameters to default values.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reset it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/customer/physics/${flipbookId}/reset`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Reset!', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Failed to reset.', 'error');
                            });
                    }
                });
            });

            // Test flip animation
            document.getElementById('testFlipBtn').addEventListener('click', function() {
                const flipPreview = document.getElementById('flipPreview');
                flipPreview.innerHTML = `
                <div class="flip-animation-demo" style="position: relative; width: 100%; height: 100%;">
                    <div class="page-flip" style="position: absolute; width: 200px; height: 280px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); left: 50%; top: 50%; transform: translate(-50%, -50%); transition: transform ${document.getElementById('duration').value}ms;">
                        <div style="padding: 20px; font-size: 14px; text-align: center;">
                            <p class="mb-2">Sample Page</p>
                            <small class="text-muted">Flip animation preview</small>
                        </div>
                    </div>
                </div>
            `;

                setTimeout(() => {
                    const page = flipPreview.querySelector('.page-flip');
                    page.style.transform = `translate(-50%, -50%) rotateY(180deg)`;

                    setTimeout(() => {
                        page.style.transform = `translate(-50%, -50%) rotateY(0deg)`;
                    }, parseInt(document.getElementById('duration').value));
                }, 100);
            });

            // Initial display update
            updatePreviewDisplay();
        });
    </script>
    <style>
        .preset-card {
            transition: all 0.3s ease;
        }

        .preset-card:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .preset-card.border-primary {
            background-color: #f8f9fa;
        }
    </style>
@endpush
