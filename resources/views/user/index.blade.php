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
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection