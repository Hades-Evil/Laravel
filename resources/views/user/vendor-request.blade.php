@extends('layouts.app')
@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Request Vendor Access</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-3">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="page-content">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Vendor Application</h5>
                            <p class="text-muted">Fill out the form below to request vendor access. Once approved, you'll have admin privileges to manage products and categories.</p>

                            <form method="POST" action="{{ route('user.vendor.request.store') }}">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="business_name" class="form-label">Business Name *</label>
                                        <input type="text" class="form-control" id="business_name" name="business_name" 
                                               value="{{ old('business_name') }}" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="business_type" class="form-label">Business Type *</label>
                                        <select class="form-control" id="business_type" name="business_type" required>
                                            <option value="vendor" {{ old('business_type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="business_description" class="form-label">Business Description *</label>
                                    <textarea class="form-control" id="business_description" name="business_description" 
                                              rows="4" placeholder="Describe your business, products you sell, experience, etc." required>{{ old('business_description') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="{{ old('phone') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Business Address</label>
                                    <textarea class="form-control" id="address" name="address" 
                                              rows="2" placeholder="Your business address">{{ old('address') }}</textarea>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Note:</strong> Once your request is approved, you will receive admin privileges to:
                                    <ul class="mb-0 mt-2">
                                        <li>Add, edit, and manage products</li>
                                        <li>Manage product categories</li>
                                        <li>Access admin dashboard</li>
                                        <li>View sales and analytics</li>
                                    </ul>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Submit Request</button>
                                    <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
