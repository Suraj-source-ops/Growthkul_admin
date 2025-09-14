@extends('layouts.main')
@section('title', '- Edit Blog')
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
                            <h1>Edit Blog</h1>
                        </div>
                        <div class="content-box">
                            <form action="{{ route('update.blog.details', ['id' => $blogDetails->id]) }}" method="POST"
                                autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Type</label>
                                            <select name="type" id="" class="select-boxes">
                                                <option value="">Select</option>
                                                <option value="blog"
                                                    {{ old('type', $blogDetails->type) == 'blog' ? 'selected' : '' }}>Blog
                                                </option>
                                                <option value="article"
                                                    {{ old('type', $blogDetails->type) == 'article' ? 'selected' : '' }}>
                                                    Article
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Blog Title</label>
                                            <input type="text" name="title" placeholder="Title"
                                                value="{{ old('title', $blogDetails->title) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Blog Image</label>
                                            <input type="file" name="blog_image" placeholder="Upload Blog Image"
                                                value="{{ old('blog_image') }}">
                                            @if ($blogDetails->blogImage)
                                                <button type="button" class="btn btn-secondary btn-sm mt-1 mb-0"
                                                    data-toggle="modal" data-target="#imagePreviewModal">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Meta Title</label>
                                            <input type="text" name="meta_title" placeholder="Enter Meta Title"
                                                value="{{ old('meta_title', $blogDetails->meta_title) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Meta Description</label>
                                            <input type="text" name="meta_description"
                                                placeholder="Enter Meta Description" value="{{ old('meta_description', $blogDetails->meta_description) }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Social Media Type</label>
                                            <select name="social_media_type" id="" class="select-boxes">
                                                <option value="">Select</option>
                                                <option value="linkedin"
                                                    {{ old('social_media_type', $blogDetails->social_media_type) == 'linkedin' ? 'selected' : '' }}>LinkedIn
                                                </option>
                                                <option value="x"
                                                    {{ old('social_media_type', $blogDetails->social_media_type) == 'x' ? 'selected' : '' }}>X
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Social Links</label>
                                            <input type="text" name="social_links" placeholder="Enter Social Links"
                                                value="{{ old('social_links', $blogDetails->social_link) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-5">
                                        <textarea id="summernote" name="content"> {{ old('content', $blogDetails->content) }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="add-member-btn">
                                            <button type="submit">Update</button>
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
    <!-- Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Blog Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    @if ($blogDetails->blogImage)
                        <img src="{{ asset($blogDetails->blogImage->file_path) }}" alt="Blog Image" class="img-fluid">
                    @else
                        <p>No image available.</p>
                    @endif
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
