<x-default-layout>

    @section('title')
        Support Tickets
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.tickets.index') }}
    @endsection

    <div id="kt_app_content_container">

        <div class="card">
            {{-- Header --}}
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold">All Support Tickets</h3>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body pt-0">

                @if ($tickets->count())
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>#</th>
                                    <th>Subject</th>
                                    <th>User</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>#{{ $ticket->id }}</td>

                                        <td>
                                            <div class="fw-bold text-gray-800">
                                                {{ $ticket->subject }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="text-gray-700 fw-semibold">
                                                {{ $ticket->user->name ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge badge-light-info">
                                                {{ ucfirst($ticket->category) }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($ticket->priority === 'high')
                                                <span class="badge badge-light-danger">High</span>
                                            @elseif ($ticket->priority === 'medium')
                                                <span class="badge badge-light-warning">Medium</span>
                                            @else
                                                <span class="badge badge-light-success">Low</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($ticket->status === 'open')
                                                <span class="badge badge-light-primary">Open</span>
                                            @elseif ($ticket->status === 'in_progress')
                                                <span class="badge badge-light-warning">In Progress</span>
                                            @else
                                                <span class="badge badge-light-success">Closed</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="text-muted">
                                                {{ $ticket->created_at->diffForHumans() }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                                                class="btn btn-sm btn-light btn-active-light-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-20">
                        <i class="ki-duotone ki-message-question fs-5x text-muted mb-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>

                        <h3 class="text-muted">No Support Tickets Found</h3>
                        <p class="text-muted fs-5">
                            All tickets will appear here once customers start submitting them.
                        </p>
                    </div>
                @endif

            </div>
        </div>

    </div>

</x-default-layout>
