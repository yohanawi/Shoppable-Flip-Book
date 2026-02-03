<x-default-layout>
    @section('title')
        Ticket #{{ $ticket->id }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.tickets.show', $ticket) }}
    @endsection

    <div id="kt_app_content_container">

        {{-- Ticket Header Card --}}
        <div class="card mb-5 mb-xl-8">
            <div class="card-body">
                {{-- Header Section --}}
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <span class="symbol-label bg-light-primary text-primary fs-2x fw-bold">
                                {{ substr($ticket->user->name, 0, 2) }}
                            </span>
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
                                        {{ $ticket->subject }}
                                    </a>
                                </div>

                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        {{ $ticket->user->name }}
                                    </a>
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-sms fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ $ticket->user->email ?? 'N/A' }}
                                    </a>
                                    <a href="#"
                                        class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                        <i class="ki-duotone ki-tag fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Ticket #{{ $ticket->id }}
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex my-4">
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light me-2">
                                    <i class="ki-duotone ki-arrow-left fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Back
                                </a>
                                @if ($ticket->status !== 'closed')
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_close_ticket">
                                        <i class="ki-duotone ki-cross-circle fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Close Ticket
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Status Badges --}}
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="d-flex flex-wrap gap-2">
                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-category fs-3 text-info me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="fs-2 fw-bold">{{ ucfirst($ticket->category) }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-500">Category</div>
                                    </div>

                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-information-5 fs-3 text-warning me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <div class="fs-2 fw-bold">{{ ucfirst($ticket->priority) }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-500">Priority</div>
                                    </div>

                                    <div
                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $statusIcon =
                                                    [
                                                        'open' => 'ki-timer',
                                                        'in_progress' => 'ki-abstract-26',
                                                        'closed' => 'ki-check-circle',
                                                    ][$ticket->status] ?? 'ki-timer';

                                                $statusColor =
                                                    [
                                                        'open' => 'primary',
                                                        'in_progress' => 'success',
                                                        'closed' => 'danger',
                                                    ][$ticket->status] ?? 'primary';
                                            @endphp
                                            <i
                                                class="ki-duotone {{ $statusIcon }} fs-3 text-{{ $statusColor }} me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="fs-2 fw-bold">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-500">Status</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 g-xl-8">
            {{-- Conversation Column --}}
            <div class="col-xl-8">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Conversation</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ $ticket->messages->count() }}
                                messages</span>
                        </h3>
                    </div>

                    <div class="card-body pt-4">
                        <div class="timeline-label">
                            @forelse ($ticket->messages as $message)
                                <div class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>

                                    <div class="timeline-badge">
                                        <i
                                            class="ki-duotone ki-abstract-8 text-{{ $message->is_admin ? 'success' : 'primary' }} fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>

                                    <div class="timeline-content d-flex">
                                        <div class="pe-3 mb-5">
                                            @if ($message->is_admin)
                                                <div class="mb-2">
                                                    <span
                                                        class="badge badge-light-success fw-bold fs-8 px-2 py-1">ADMIN</span>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center mt-1 fs-6">
                                                <div class="text-muted me-2 fs-7">by</div>
                                                <a href="#" class="text-primary fw-bold me-1">
                                                    {{ $message->is_admin ? 'Admin Support' : $message->user->name }}
                                                </a>
                                            </div>

                                            <div class="overflow-auto pb-5">
                                                <div
                                                    class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5 mt-3">
                                                    <div class="flex-grow-1 me-2">
                                                        <div class="fw-normal text-gray-800 fs-6 mb-2">
                                                            {{ $message->message }}
                                                        </div>
                                                        <span
                                                            class="text-muted fs-7">{{ $message->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <i class="ki-duotone ki-message-text-2 fs-5x text-gray-400 mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <p class="text-gray-500 fs-4 fw-semibold mb-0">No messages yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ticket Details Sidebar --}}
            <div class="col-xl-4">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">Ticket Details</span>
                        </h3>
                    </div>

                    <div class="card-body pt-5">
                        <div class="mb-7">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-500 fs-6 fw-semibold me-2">Created:</span>
                                <span
                                    class="text-gray-800 fw-bold">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-500 fs-6 fw-semibold me-2">Updated:</span>
                                <span class="text-gray-800 fw-bold">{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="separator separator-dashed mb-7"></div>

                        {{-- Quick Actions --}}
                        <div class="mb-5">
                            <h5 class="mb-4">Quick Actions</h5>

                            <button class="btn btn-light-primary w-100 mb-2" data-bs-toggle="collapse"
                                data-bs-target="#kt_reply_form">
                                <i class="ki-duotone ki-message-edit fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Reply to Customer
                            </button>

                            @if ($ticket->status !== 'closed')
                                <button class="btn btn-light-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_close_ticket">
                                    <i class="ki-duotone ki-cross-circle fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Close Ticket
                                </button>
                            @else
                                <div
                                    class="alert alert-dismissible bg-light-danger d-flex flex-column flex-sm-row p-5">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1">Ticket Closed</h5>
                                        <span>This ticket is closed</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Reply Section (Collapsible) --}}
        @if ($ticket->status !== 'closed')
            <div class="collapse mt-5" id="kt_reply_form">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-duotone ki-message-edit fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Send Admin Reply
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                            @csrf

                            <div class="mb-7">
                                <label class="form-label fw-bold fs-6 text-gray-700">Message</label>
                                <textarea name="message" class="form-control form-control-solid" rows="6"
                                    placeholder="Write your admin reply here..." required></textarea>
                                <div class="form-text">Your response will be sent to the customer</div>
                            </div>

                            <div class="row g-5 mb-7">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold fs-6 text-gray-700">Update Status</label>
                                    <select name="status" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                                        <option value="in_progress"
                                            {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>
                                            In Progress
                                        </option>
                                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>
                                            Open
                                        </option>
                                        <option value="closed">
                                            Close Ticket
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-light me-3" data-bs-toggle="collapse"
                                    data-bs-target="#kt_reply_form">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-send fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Close Ticket Modal --}}
    <div class="modal fade" id="kt_modal_close_ticket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>

                <div class="modal-body scroll-y pt-0 pb-15">
                    <div class="text-center mb-13">
                        <h1 class="mb-3">Close Ticket</h1>
                        <div class="text-muted fw-semibold fs-5">
                            Are you sure you want to close this ticket?
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                        @csrf
                        <input type="hidden" name="status" value="closed">

                        <div class="mb-7">
                            <label class="form-label fw-bold">Closing Message (Optional)</label>
                            <textarea name="message" class="form-control form-control-solid" rows="4"
                                placeholder="Add a final message before closing..."></textarea>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="ki-duotone ki-check-circle fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Close Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
