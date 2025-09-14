@extends('layouts.main')
@section('title', '- Add Project')
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
                            <h1>Add Project</h1>
                        </div>
                        <div class="content-box">
                            <form action="{{ route('store.project') }}" method="POST" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Name</label>
                                            <input type="text" name="name" placeholder="Name"
                                                value="{{ old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Title</label>
                                            <input type="text" name="title" placeholder="Title"
                                                value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Image</label>
                                            <input type="file" name="project_image" placeholder="Upload Project Image"
                                                value="{{ old('project_image') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project URL</label>
                                            <input type="text" name="project_url" placeholder="Enter Project URL"
                                                value="{{ old('project_url') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Project Description</label>
                                            <textarea name="project_description" placeholder="Enter Project Description">{{ old('project_description') }}</textarea>
                                        </div>
                                    </div>
                                    @can('add-project-button-projects')
                                        <div class="col-md-12">
                                            <div class="add-member-btn">
                                                <button type="submit">Create</button>
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
@endsection
