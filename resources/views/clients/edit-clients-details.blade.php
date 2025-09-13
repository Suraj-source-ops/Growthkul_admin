@extends('layouts.main')
@section('title', '- Edit - Clients Details')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{route('clients')}}">
                            <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1> Edit Client Details</h1>
                    </div>
                    <div class="content-box">
                        <form action="{{route('update.client.details', $client->id)}}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Client Name</label>
                                        <input type="text" placeholder="Name" name="name"
                                            value="{{ $client->name ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Phone number</label>
                                        <input type="number" placeholder="Enter description" name="phone"
                                            value="{{ $client->phone ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Email</label>
                                        <input type="email" placeholder="Enter description" name="email"
                                            value="{{ $client->email ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if (count($products) > 0)
                                @foreach ($products as $product)
                                <div class="col-md-6">
                                    <div class="product-content-box">
                                        <div class="clints-main-box">
                                            <div class="products-img">
                                                <img class="" src="{{ asset('/assets/images/box-icons.png') }}"
                                                    alt="back-icon" />
                                            </div>
                                            <div class="clint-main-box">
                                                <a
                                                    href="{{route('edit.product.details', ['slug' => $product->slug])}}?type={{$product->product_type}}">
                                                    <div class="clients-box-content">
                                                        <h3>{{$product->product_code}} {{!empty($product->product_type)
                                                            &&
                                                            $product->product_type == 1 ? ' (Size chart)': ' (Tech
                                                            pack)'}}</h3>
                                                        <p><span>{{date('d-M-Y H:i A',
                                                                strtotime($product->created_at))}}</span></p>
                                                    </div>
                                                </a>
                                                {{-- @can('delete-client-product-clients')
                                                <a href="{{route('delete.product', $product->id)}}"
                                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <img class="" src="{{ asset('/assets/images/cross.png') }}"
                                                        alt="back-icon" />
                                                </a>
                                                @endcan --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                            @can('update-client-button-clients')
                            <div class="col-md-12">
                                <div class="add-member-btn">
                                    <button type="submit">Update</button>
                                </div>
                            </div>
                            @endcan
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection