@extends('layouts.app')
@section('content')
      <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="row">
        <div class="col-lg-3">
            @include('user.account-nav')
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__dashboard">
            <p>Hello <strong>{{ Auth::user()->name }}</strong></p>
            <p>From your account dashboard you can view your <a class="unerline-link" href="account_orders.html">recent
                orders</a>, manage your <a class="unerline-link" href="account_edit_address.html">shipping
                addresses</a>, and <a class="unerline-link" href="account_edit.html">edit your password and account
                details.</a></p>

            @if(Auth::user()->isAdmin())
              <div class="alert alert-info mt-4">
                <h5>Admin Access</h5>
                <p>You have administrator privileges. <a href="{{ route('admin.index') }}" class="btn btn-primary btn-sm">Go to Admin Dashboard</a></p>
              </div>
            @else
              <!-- Vendor Request Section -->
              <div class="vendor-request-section mt-4">
                @if($vendorRequest)
                  @if($vendorRequest->isPending())
                    <div class="alert alert-warning">
                      <h5>Vendor Request Pending</h5>
                      <p>Your request to become a vendor is currently being reviewed by our admin team.</p>
                      <p><strong>Business Name:</strong> {{ $vendorRequest->business_name }}</p>
                      <p><strong>Business Type:</strong> {{ ucfirst($vendorRequest->business_type) }}</p>
                      <p><strong>Submitted:</strong> {{ $vendorRequest->created_at->format('M d, Y') }}</p>
                    </div>
                  @elseif($vendorRequest->isApproved())
                    <div class="alert alert-success">
                      <h5>Congratulations! You are now a Vendor</h5>
                      <p>Your vendor request has been approved. You now have admin access to manage products and categories.</p>
                      <a href="{{ route('admin.index') }}" class="btn btn-success">Access Admin Dashboard</a>
                    </div>
                  @elseif($vendorRequest->isRejected())
                    <div class="alert alert-danger">
                      <h5>Vendor Request Rejected</h5>
                      <p>Unfortunately, your vendor request was not approved.</p>
                      @if($vendorRequest->admin_notes)
                        <p><strong>Admin Notes:</strong> {{ $vendorRequest->admin_notes }}</p>
                      @endif
                      <a href="{{ route('user.vendor.request') }}" class="btn btn-primary btn-sm mt-2">Submit New Request</a>
                    </div>
                  @endif
                @else
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Become a Vendor</h5>
                      <p class="card-text">Want to sell your products on our platform? Request to become a vendor and get admin access to manage your business.</p>
                      <a href="{{ route('user.vendor.request') }}" class="btn btn-primary">Request Vendor Access</a>
                    </div>
                  </div>
                @endif
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection