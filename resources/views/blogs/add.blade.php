@extends('layouts.main')
@section('title', '- Add Blog')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="head-box">
                            <a href="{{ route('blogs') }}" class="back-icon">
                                <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                            </a>
                            <h1>Add Blog</h1>
                        </div>
                        <div class="content-box">
                            <form action="{{ route('store.blog') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Type</label>
                                            <select name="type" id="" class="select-boxes">
                                                <option value="">Select</option>
                                                <option value="blog" {{ old('type') == 'blog' ? 'selected' : '' }}>Blog
                                                </option>
                                                <option value="article" {{ old('type') == 'article' ? 'selected' : '' }}>
                                                    Article
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Blog Title</label>
                                            <input type="text" name="title" placeholder="Title"
                                                value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Blog Image</label>
                                            <input type="file" name="blog_image" placeholder="Upload Blog Image"
                                                value="{{ old('blog_image') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Meta Title</label>
                                            <input type="text" name="meta_title" placeholder="Enter Meta Title"
                                                value="{{ old('meta_title') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Meta Description</label>
                                            <input type="text" name="meta_description"
                                                placeholder="Enter Meta Description" value="{{ old('meta_description') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Social Media Type</label>
                                            <select name="social_media_type" id="" class="select-boxes">
                                                <option value="">Select</option>
                                                <option value="linkedin"
                                                    {{ old('social_media_type') == 'linkedin' ? 'selected' : '' }}>LinkedIn
                                                </option>
                                                <option value="x"
                                                    {{ old('social_media_type') == 'x' ? 'selected' : '' }}>X
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Social Links</label>
                                            <input type="text" name="social_links" placeholder="Enter Social Links"
                                                value="{{ old('social_links') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-5">
                                        <textarea id="summernote" name="content"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="add-member-btn">
                                            <button type="submit">Create</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#summernote').summernote({
                    placeholder: 'Please write blog description here...',
                    tabsize: 2,
                    height: 300
                });
            });
        </script>
    @endpush
@endsection
