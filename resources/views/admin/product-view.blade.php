@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Product Details</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.products')}}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Product Details</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="row">
                <!-- Product Image -->
                <div class="col-lg-6">
                    <div class="product-image-container">
                        <h5 class="mb-3">Product Image</h5>
                        <div class="image-preview">
                            <img src="{{asset('uploads/products')}}/{{$product->image}}" 
                                 alt="{{$product->name}}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-width: 100%; height: auto;">
                        </div>
                        @if($product->images)
                            <div class="gallery-images mt-3">
                                <h6>Gallery Images</h6>
                                <div class="row">
                                    @foreach(explode(',',$product->images) as $gimg)
                                        @if($gimg)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{asset('uploads/products')}}/{{$gimg}}" 
                                                 alt="Gallery Image" 
                                                 class="img-fluid rounded shadow-sm">
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Details -->
                <div class="col-lg-6">
                    <div class="product-details">
                        <h5 class="mb-3">Product Information</h5>
                        
                        <div class="info-group mb-3">
                            <label class="fw-bold">Product Name:</label>
                            <p class="mb-1">{{$product->name}}</p>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold">Slug:</label>
                            <p class="mb-1 text-muted">{{$product->slug}}</p>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold">SKU:</label>
                            <p class="mb-1">{{$product->SKU}}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="fw-bold">Regular Price:</label>
                                    <p class="mb-1 text-primary fs-5">${{$product->regular_price}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="fw-bold">Sale Price:</label>
                                    <p class="mb-1 text-success fs-5">
                                        @if($product->sale_price)
                                            ${{$product->sale_price}}
                                        @else
                                            <span class="text-muted">No sale price</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="fw-bold">Category:</label>
                                    <p class="mb-1">{{$product->category->name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="fw-bold">Brand:</label>
                                    <p class="mb-1">{{$product->brand ? $product->brand->name : 'No Brand'}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="info-group">
                                    <label class="fw-bold">Stock Status:</label>
                                    <p class="mb-1">
                                        <span class="badge bg-{{$product->stock_status == 'instock' ? 'success' : 'danger'}}">
                                            {{ucfirst($product->stock_status)}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-group">
                                    <label class="fw-bold">Quantity:</label>
                                    <p class="mb-1">{{$product->quantity}}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-group">
                                    <label class="fw-bold">Featured:</label>
                                    <p class="mb-1">
                                        <span class="badge bg-{{$product->featured ? 'success' : 'secondary'}}">
                                            {{$product->featured ? 'Yes' : 'No'}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold">Short Description:</label>
                            <div class="short-description-content">
                                {!! $product->short_description !!}
                            </div>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold">Full Description:</label>
                            <div class="description-content p-3 bg-light rounded">
                                {!! $product->description !!}
                            </div>
                        </div>

                        <!-- Admin Only Information -->
                        <div class="admin-only-info mt-4 p-3 bg-warning bg-opacity-10 rounded">
                            <h6 class="text-warning mb-3">
                                <i class="icon-lock"></i> Admin Only Information
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group mb-2">
                                        <label class="fw-bold">Created:</label>
                                        <p class="mb-1 small">{{$product->created_at ? \Carbon\Carbon::parse($product->created_at)->format('M d, Y h:i A') : 'N/A'}}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group mb-2">
                                        <label class="fw-bold">Last Updated:</label>
                                        <p class="mb-1 small">{{$product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('M d, Y h:i A') : 'N/A'}}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-group mb-2">
                                <label class="fw-bold">Product ID:</label>
                                <p class="mb-1 small">#{{$product->id}}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons mt-4">
                            <a href="{{route('admin.product.edit', ['id'=> $product->id])}}" 
                               class="btn btn-primary me-2">
                                <i class="icon-edit-3"></i> Edit Product
                            </a>
                            <a href="{{route('admin.products')}}" 
                               class="btn btn-secondary">
                                <i class="icon-arrow-left"></i> Back to Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-image-container img {
    border: 1px solid #e0e0e0;
    transition: transform 0.3s ease;
}

.product-image-container img:hover {
    transform: scale(1.05);
}

.info-group {
    margin-bottom: 1.5rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.info-group label {
    color: #495057;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: block;
}

.info-group p {
    color: #212529;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 0;
    font-weight: 500;
}

.description-content {
    max-height: 250px;
    overflow-y: auto;
    background: #ffffff;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    line-height: 1.8;
    font-size: 15px;
}

.short-description-content {
    background: #ffffff;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    line-height: 1.6;
    font-size: 15px;
    color: #495057;
}

.admin-only-info {
    border-left: 4px solid #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #fef9e7 100%);
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
}

.admin-only-info h6 {
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 1rem;
}

.admin-only-info .info-group {
    background: rgba(255, 255, 255, 0.7);
    border-left: 2px solid #ffc107;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.admin-only-info .info-group label {
    font-size: 12px;
    color: #856404;
    font-weight: 700;
}

.admin-only-info .info-group p {
    font-size: 14px;
    color: #856404;
    font-weight: 600;
}

.gallery-images img {
    max-height: 100px;
    object-fit: cover;
    border-radius: 6px;
    border: 2px solid #dee2e6;
    transition: border-color 0.3s ease;
}

.gallery-images img:hover {
    border-color: #007bff;
}

.badge {
    font-size: 12px;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.text-primary {
    font-weight: 700 !important;
}

.text-success {
    font-weight: 700 !important;
}

.btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.wg-box {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
}

h5 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 1.5rem;
    font-size: 18px;
}

h6 {
    color: #2c3e50;
    font-weight: 600;
    font-size: 14px;
}

.breadcrumbs .text-tiny {
    font-size: 13px;
    color: #6c757d;
}

.breadcrumbs a:hover .text-tiny {
    color: #007bff;
}

.product-details {
    padding: 1rem;
}

.image-preview {
    text-align: center;
    margin-bottom: 1rem;
}

.row.mb-3 .info-group {
    margin-bottom: 0.75rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .info-group {
        margin-bottom: 1rem;
        padding: 0.5rem;
    }
    
    .description-content {
        max-height: 200px;
        padding: 1rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection
