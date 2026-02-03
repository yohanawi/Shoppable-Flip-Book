<x-default-layout>

    @section('title')
        Dashboard
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    @role('Customer')
        <div id="kt_app_content_container" class="app-container container-xxl">

            {{-- Stats Cards Row --}}
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                {{-- Total FlipBooks Card --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card card-flush h-md-100">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $totalFlipbooks }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        All Time
                                    </span>
                                </div>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Total FlipBooks</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pe-0">
                            <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">
                                <i class="ki-duotone ki-book fs-3 text-primary me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                Your entire collection
                            </span>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('customer.catalog.index') }}" class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-eye fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    View Catalog
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Published FlipBooks Card --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card card-flush h-md-100">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $publishedFlipbooks }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-check-circle fs-5 text-success ms-n1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Live
                                    </span>
                                </div>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Published FlipBooks</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pe-0">
                            <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">
                                <i class="ki-duotone ki-rocket fs-3 text-success me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Live & Available to Viewers
                            </span>
                            <div class="progress h-6px w-100 mt-2">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $totalFlipbooks > 0 ? ($publishedFlipbooks / $totalFlipbooks) * 100 : 0 }}%"
                                    aria-valuenow="{{ $publishedFlipbooks }}" aria-valuemin="0"
                                    aria-valuemax="{{ $totalFlipbooks }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Draft FlipBooks Card --}}
                <div class="col-md-6 col-xl-4">
                    <div class="card card-flush h-md-100">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $draftFlipbooks }}</span>
                                    <span class="badge badge-light-warning fs-base">
                                        <i class="ki-duotone ki-timer fs-5 text-warning ms-n1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Draft
                                    </span>
                                </div>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Draft FlipBooks</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-end pe-0">
                            <span class="fs-6 fw-bolder text-gray-800 d-block mb-2">
                                <i class="ki-duotone ki-pencil fs-3 text-warning me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Work in Progress
                            </span>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('customer.catalog.create') }}" class="btn btn-sm btn-light-primary">
                                    <i class="ki-duotone ki-plus-square fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Create New
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Banner --}}
            <div class="card bg-light-primary mb-5 mb-xl-10">
                <div class="card-body d-flex flex-stack flex-wrap p-5">
                    <div class="d-flex align-items-center pe-2">
                        <div class="symbol symbol-60px me-4">
                            <span class="symbol-label bg-white">
                                <i class="ki-duotone ki-abstract-41 fs-2x text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <div class="me-5">
                            <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-4">
                                Ready to create something amazing?
                            </a>
                            <div class="text-gray-600 fw-semibold fs-6 mt-1">
                                Start building your next FlipBook project now
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('customer.catalog.create') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-rocket fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Get Started
                    </a>
                </div>
            </div>

            {{-- Recent FlipBooks Table --}}
            <div class="card card-flush">
                {{-- Card Header --}}
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-book-open fs-1 position-absolute ms-4 text-gray-500">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <input type="text" class="form-control form-control-solid w-250px ps-14"
                                placeholder="Search FlipBooks..." />
                        </div>
                    </div>
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <div class="w-100 mw-150px">
                            <select class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Status" data-hide-search="true">
                                <option value="">All Status</option>
                                <option value="live">Live</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <a href="{{ route('customer.catalog.index') }}" class="btn btn-light-primary">
                            <i class="ki-duotone ki-eye fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            View All
                        </a>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body pt-0">
                    @if ($recentFlipbooks->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_flipbooks_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-250px">FlipBook</th>
                                        <th class="min-w-100px">Template</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px">Visibility</th>
                                        <th class="min-w-100px">Created</th>
                                        <th class="text-end min-w-100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($recentFlipbooks as $flipbook)
                                        <tr>
                                            {{-- FlipBook Info --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-50px me-3">
                                                        <span class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-book fs-2x text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <a href="{{ route('customer.catalog.show', $flipbook) }}"
                                                            class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">
                                                            {{ $flipbook->title }}
                                                        </a>
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                            {{ Str::limit($flipbook->description, 60) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Template --}}
                                            <td>
                                                <div class="badge badge-light-info fw-bold">
                                                    <i class="ki-duotone ki-chart-simple-2 fs-6 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                    {{ str_replace('_', ' ', ucwords($flipbook->template_type ?? 'slicer')) }}
                                                </div>
                                            </td>

                                            {{-- Status --}}
                                            <td>
                                                @if ($flipbook->status === 'live')
                                                    <div class="badge badge-light-success">
                                                        <i class="ki-duotone ki-check-circle fs-6 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Live
                                                    </div>
                                                @elseif($flipbook->status === 'draft')
                                                    <div class="badge badge-light-warning">
                                                        <i class="ki-duotone ki-timer fs-6 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Draft
                                                    </div>
                                                @else
                                                    <div class="badge badge-light-secondary">
                                                        <i class="ki-duotone ki-archive fs-6 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Archived
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Visibility --}}
                                            <td>
                                                <div class="badge badge-light-primary">
                                                    @if (($flipbook->visibility ?? 'private') === 'public')
                                                        <i class="ki-duotone ki-world fs-6 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Public
                                                    @else
                                                        <i class="ki-duotone ki-lock fs-6 me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Private
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Created Date --}}
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold mb-1">
                                                        {{ $flipbook->created_at->format('M d, Y') }}
                                                    </span>
                                                    <span class="text-muted fw-semibold fs-7">
                                                        {{ $flipbook->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Actions
                                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                                </a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('customer.catalog.show', $flipbook) }}"
                                                            class="menu-link px-3">
                                                            View
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('customer.catalog.edit', $flipbook) }}"
                                                            class="menu-link px-3">
                                                            Edit
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">
                                                            Duplicate
                                                        </a>
                                                    </div>
                                                    <div class="separator my-2"></div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link text-danger px-3">
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-20">
                            <div class="mb-10">
                                <i class="ki-duotone ki-book-open fs-5x text-primary opacity-50">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </div>
                            <h1 class="fw-bold text-gray-800 mb-5">No FlipBooks Yet</h1>
                            <p class="text-gray-600 fs-4 fw-semibold mb-10">
                                Start creating amazing interactive FlipBooks for your audience.<br>
                                It only takes a few minutes to get started!
                            </p>
                            <a href="{{ route('customer.catalog.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus-square fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Create Your First FlipBook
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endrole

    @role('Administrator')
        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->
            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                @include('partials/widgets/cards/_widget-20')

                @include('partials/widgets/cards/_widget-7')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                @include('partials/widgets/cards/_widget-17')

                @include('partials/widgets/lists/_widget-26')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xxl-6">
                @include('partials/widgets/engage/_widget-10')
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row-->
        <div class="row gx-5 gx-xl-10">
            <!--begin::Col-->
            <div class="col-xxl-6 mb-5 mb-xl-10">
                @include('partials/widgets/charts/_widget-8')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-6 mb-5 mb-xl-10">
                @include('partials/widgets/tables/_widget-16')
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->
            <div class="col-xxl-6">
                @include('partials/widgets/cards/_widget-18')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-6">
                @include('partials/widgets/charts/_widget-36')
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row-->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!--begin::Col-->
            <div class="col-xl-4">
                @include('partials/widgets/charts/_widget-35')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-8">
                @include('partials/widgets/tables/_widget-14')
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <!--begin::Row-->
        <div class="row gx-5 gx-xl-10">
            <!--begin::Col-->
            <div class="col-xl-4">
                @include('partials/widgets/charts/_widget-31')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-8">
                @include('partials/widgets/charts/_widget-24')
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    @endrole
</x-default-layout>
