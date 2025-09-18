@extends('layouts.main')
@section('title', '- Edit Project')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="head-box">
                            <a href="{{ route('projects') }}" class="back-icon">
                                <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                            </a>
                            <h1>Edit Project</h1>
                        </div>
                        <div class="content-box">
                            <form action="{{ route('update.project.details', ['id' => $projectDetails->id]) }}"
                                method="POST" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Name</label>
                                            <input type="text" name="name" placeholder="Name"
                                                value="{{ old('name', $projectDetails->name) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Title</label>
                                            <input type="text" name="title" placeholder="Title"
                                                value="{{ old('title', $projectDetails->title) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Image</label>
                                            <input type="file" name="project_image" placeholder="Upload Project Image"
                                                value="{{ old('project_image') }}">
                                            @if ($projectDetails->projectImage)
                                                <button type="button" class="btn btn-secondary btn-sm mt-1 mb-0"
                                                    data-toggle="modal" data-target="#imagePreviewModal">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project URL</label>
                                            <input type="text" name="project_url" placeholder="Enter Project URL"
                                                value="{{ old('project_url', $projectDetails->project_url) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Description</label>
                                            <textarea name="project_description" placeholder="Enter Project Description">{{ old('project_description', $projectDetails->description) }}</textarea>
                                        </div>
                                    </div>
                                    @can('update-project-button-projects')
                                        <div class="col-md-12">
                                            <div class="add-member-btn">
                                                <button type="submit">Update</button>
                                            </div>
                                        </div>
                                    @endcan
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
                    <h5 class="modal-title" id="imagePreviewModalLabel">Project Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    @if ($projectDetails->projectImage)
                        <img src="{{ asset($projectDetails->projectImage->file_path) }}" alt="Project Image"
                            class="img-fluid">
                    @else
                        <p>No image available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
