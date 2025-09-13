@extends('layouts.main')
@section('title', '- Clients - Details')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{ route('clients') }}" class="back-icon">
                            <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1>Client Details</h1>
                    </div>
                    <div class="content-box">
                        <form action="" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Client Name</label>
                                        <input type="text" placeholder="Name" value="{{ $client->name ?? '' }}"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Phone number</label>
                                        <input type="number" placeholder="Enter Phone Number"
                                            value="{{ $client->phone ?? '' }}" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Email</label>
                                        <input type="email" placeholder="Enter Email Address"
                                            value="{{ $client->email ?? '' }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                </div>
                            </div>
                        </form>
                        @can('add-client-product-button-clients')    
                        <div class="add-products">
                            <a href="{{ route('add.product', $client->clientid) }}">
                                <button> + Add Product </button>
                            </a>
                        </div>
                        @endcan
                        <div class="row">
                            @if (count($products) > 0)
                            @foreach ($products as $product)
                            <div class="col-md-6">
                                <a
                                    href="{{route('view.product.details',['slug' => $product->slug,'type' =>$product->product_type])}}">
                                    <div class="product-content-box">
                                        <div class="clints-main-box">
                                            <div class="products-img">
                                                <img class="" src="{{ asset('/assets/images/box-icons.png') }}"
                                                    alt="back-icon" />
                                            </div>
                                            <div class="clint-main-box">
                                                <div class="clients-box-content">
                                                    <h3>{{$product->product_code}} ({{$product->product_type == 1 ?
                                                        'Size chart' : 'Tech pack'}})</h3>
                                                    <p><span>{{date('d-M-Y H:i A',
                                                            strtotime($product->created_at))}}</span></p>
                                                </div>
                                                {{-- <img class="" src="{{ asset('/assets/images/cross.png') }}"
                                                    alt="back-icon" /> --}}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection