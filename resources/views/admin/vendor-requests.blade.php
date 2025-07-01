@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Vendor Requests</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Vendor Requests</div>
                </li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name" tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @if($vendorRequests->count() > 0)
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Business Name</th>
                                    <th>Business Type</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendorRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>
                                        <div class="user">
                                            <div class="info">
                                                <div class="name">
                                                    <a href="#" class="body-title-2">{{ $request->user->name }}</a>
                                                </div>
                                                <div class="text-tiny mt-3">{{ $request->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $request->business_name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($request->business_type) }}</span>
                                    </td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="list-icon-function">
                                            @if($request->isPending())
                                                <button type="button" class="btn btn-sm btn-primary" onclick="showRequestDetails('{{ $request->id }}', '{{ $request->user->name }}', '{{ $request->business_name }}', '{{ $request->business_type }}', '{{ addslashes($request->business_description) }}', '{{ $request->phone }}', '{{ addslashes($request->address) }}')" data-bs-toggle="modal" data-bs-target="#requestModal">
                                                    <i class="icon-eye"></i> View
                                                </button>
                                            @else
                                                <span class="text-muted">{{ ucfirst($request->status) }}</span>
                                                @if($request->admin_notes)
                                                    <br><small>{{ $request->admin_notes }}</small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <h4>No vendor requests found</h4>
                            <p class="text-muted">When users request vendor access, they will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
            @if($vendorRequests->hasPages())
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $vendorRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Vendor Request Details Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Vendor Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>User:</strong> <span id="modal-user"></span></p>
                        <p><strong>Business Name:</strong> <span id="modal-business-name"></span></p>
                        <p><strong>Business Type:</strong> <span id="modal-business-type"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
                        <p><strong>Address:</strong> <span id="modal-address"></span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p><strong>Business Description:</strong></p>
                        <p id="modal-description" class="border p-3 rounded"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-12 mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes:</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add notes for approval/rejection..."></textarea>
                    </div>
                </div>
                <button type="button" class="btn btn-success" onclick="processRequest('approve')">
                    <i class="icon-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="processRequest('reject')">
                    <i class="icon-x"></i> Reject
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentRequestId = null;

function showRequestDetails(id, user, businessName, businessType, description, phone, address) {
    currentRequestId = id;
    
    document.getElementById('modal-user').textContent = user;
    document.getElementById('modal-business-name').textContent = businessName;
    document.getElementById('modal-business-type').textContent = businessType.charAt(0).toUpperCase() + businessType.slice(1);
    document.getElementById('modal-description').textContent = description;
    document.getElementById('modal-phone').textContent = phone || 'Not provided';
    document.getElementById('modal-address').textContent = address || 'Not provided';
    
    // Clear previous admin notes
    document.getElementById('adminNotes').value = '';
}

function processRequest(action) {
    if (!currentRequestId) {
        alert('No request selected');
        return;
    }
    
    const adminNotes = document.getElementById('adminNotes').value;
    
    if (action === 'reject' && !adminNotes.trim()) {
        alert('Please provide admin notes for rejection.');
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/vendor-requests/${currentRequestId}/${action}`;
    
    console.log('Form action:', form.action); // Debug log
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add admin notes
    if (adminNotes.trim()) {
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'admin_notes';
        notesInput.value = adminNotes;
        form.appendChild(notesInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
