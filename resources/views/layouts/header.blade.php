<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Growthkul @yield('title')</title>
    <link rel="shortcut icon" type="image/png" href="" />  {{-- set fevicon icon --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/common.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.2.13/dist/semantic.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <script>
        var base_url = '{{ url('/') }}';
        var csrf_token = "{{ csrf_token() }}";
    </script>
</head>

<body>
    @php
    $color = '#' . substr(md5(Auth::user()->name), 0, 6);
    @endphp
    <header class="main_header_section">
        <div class="web-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-6">

                    </div>
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="header_right_box">
                            <div class="notification-image">
                                <a href="#">
                                    <img src="{{ asset('assets\images\notification-icons.png') }}"
                                        onclick="toggleNotification()" alt="bell-icons" />
                                </a>
                                <img class="notification-icons"
                                    src="{{ asset('assets\images\notification-icons.png') }}" alt="bell-icons" />
                                <div class="notification-box" id="notification" style="display: none;">
                                    <div class="notification-head">
                                        <h3>Notification</h3>
                                        <div class="cross-btns" onclick="toggleNotification()">
                                            <img src="{{ asset('/assets/images/cross.png') }}" alt="back-icon" />
                                        </div>
                                    </div>
                                    <div class="notification-content-box-main">
                                        <table id="notificationTable" class="table table-sm table-borderless"
                                            style="width: 100%;">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="header_drop_btn dropdown-toggle" type="button" data-toggle="dropdown">
                                    @if (isset(Auth::user()->profile->file_path))
                                    <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                        alt="user-icon" /> {{ Auth::user()->name }}
                                    @else
                                    <div class="circle-name" style="background-color: {{ $color }};">
                                        <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                    </div>
                                    @endif
                                </button>
                                <div class="dropdown-menu header_admin_drop">
                                    <div class="admin_profile_box">
                                        @if (isset(Auth::user()->profile->file_path))
                                        <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                            alt="user-icon" />
                                        @else
                                        <div class="circle-name" style="background-color: {{ $color }};">
                                            <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                        </div>
                                        @endif
                                        <div>
                                            <h3>{{ Auth::user()->name }}</h3>
                                            <a href="#">{{ Auth::user()->email }}</a>
                                        </div>
                                    </div>
                                    <div class="profile_logout_btn">
                                        {{-- <a class="dropdown-item Profile_bor">Profile</a> --}}
                                        <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mob-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="menu-icon">
                            <img id="mob-menu" src="{{ url('/assets/images/menu.png') }}" alt="Menu" />
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-6 text-right">
                        <div class="header_right_box">
                            <div class="dropdown">
                                <button class="header_drop_btn dropdown-toggle" type="button" data-toggle="dropdown">
                                    @if (isset(Auth::user()->profile->file_path))
                                    <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                        alt="user-icon" /> {{ Auth::user()->name }}
                                    @else
                                    <div class="circle-name" style="background-color: {{ $color }};">
                                        <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                    </div>
                                    @endif
                                </button>
                                <div class="dropdown-menu header_admin_drop">
                                    <div class="admin_profile_box">
                                        @if (isset(Auth::user()->profile->file_path))
                                        <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                            alt="user-icon" />
                                        @else
                                        <div class="circle-name" style="background-color: {{ $color }};">
                                            <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                        </div>
                                        @endif
                                        <div>
                                            <h3>{{ Auth::user()->name }}</h3>
                                            <a href="#">{{ Auth::user()->email }}</a>
                                        </div>
                                    </div>
                                    <div class="profile_logout_btn">
                                        {{-- <a class="dropdown-item Profile_bor" href="#">Profile</a> --}}
                                        <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @include('layouts.sidebar')
    @yield('main-content')
    <div id="fullscreenLoader" style="display:none;">
        <div class="loader-backdrop">
            <div class="loader-content">
                <div class="spinner"></div>
                <div id="loaderText">Uploading, Please wait...</div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script
        src="https://cdn.rawgit.com/mdehoog/Semantic-UI/6e6d051d47b598ebab05857545f242caf2b4b48c/dist/semantic.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.2.13/dist/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        toastr.options = {
            closeButton: true,
            preventDuplicates: true,
            timeOut: 3000,
        }
    </script>

    @stack('scripts')
    <script>
        @if (Session::has('message'))
            toastr.options = {
                closeButton: true,
                preventDuplicates: true,
                timeOut: 3000
            };
            var type = "{{ Session::get('alert-type', 'info') }}";
            var message = "{{ Session::get('message') }}";
            toastr[type](message);
        @endif
    </script>
    <script>
        $("#mob-menu").on("click", function() {
            $(".main_sidebar_section").addClass("addsidebar");
        });
    </script>
    <script>
        $('#rangestart').calendar({
            type: 'date',
            endCalendar: $('#rangeend')
        });
        $('#rangeend').calendar({
            type: 'date',
            startCalendar: $('#rangestart')
        });
    </script>
    <script>
        $("#close-menu").on("click", function() {
            $(".main_sidebar_section").removeClass("addsidebar");
        });
    </script>
    <script>
        $(document).ready(function() {
            let currentUrl = window.location.href;
            let urlParts = currentUrl.split("/");
            return false;
        });
    </script>
    {{-- multiple selection dropdown start --}}
    <script>
        $(document).ready(function() {
            $('.label.ui.dropdown').dropdown();
        });
    </script>
    {{-- multiple selection dropdown end --}}

    {{-- file and dropdown selection js for product edit table --}}

    {{-- Upload File in chunk for size chart --}}
    <script>
        let uploadedFilesSizeChart = [];
        let uploadControllerSizeChart = null;
        let currentUploadIdSizeChart = null;

        async function startSizeChartattachmentChunkUpload(input) {
            const productId = input.dataset.productid
            const file = input.files[0];
            if (!file) return;

            const chunkSize = 10 * 1024 * 1024;
            const totalChunks = Math.ceil(file.size / chunkSize);
            const uploadId = "{{ time() }}";
            currentUploadIdSizeChart = uploadId;

            const fileName = file.name;
            const mimeType = file.type;

            // UI Elements
            const progressBar = document.getElementById('upload-progress-bar-sizechart');
            const statusText = document.getElementById('upload-status-text-sizechart');
            const cancelBtn = document.getElementById('cancel-upload-btn-sizechart');
            const progressWrapper = document.getElementById('upload-progress-wrapper-sizechart');

            // Reset UI
            progressWrapper.style.display = 'block';
            progressBar.style.width = '0%';
            cancelBtn.style.display = 'inline-block';
            statusText.textContent = `Uploading "${fileName}"...`;

            let chunkIndex = 0;
            let uploadedBytes = 0;
            uploadControllerSizeChart = new AbortController();

            async function sendChunkSizeChart() {
                if (chunkIndex >= totalChunks) return mergeChunksSizeChart();
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const blob = file.slice(start, end);
                const formData = new FormData();
                formData.append('chunk', blob);
                formData.append('upload_id', uploadId);
                formData.append('chunk_index', chunkIndex);

                try {
                    await fetch('/api/upload-chunk', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        signal: uploadControllerSizeChart.signal
                    });

                    uploadedBytes += blob.size;
                    const progress = Math.round((uploadedBytes / file.size) * 100);
                    progressBar.style.width = `${progress}%`;
                    statusText.textContent = `Uploading "${fileName}"... ${progress}%`;
                    chunkIndex++;
                    sendChunkSizeChart();
                } catch (err) {
                    if (err.name === 'AbortError') {
                        statusText.textContent = 'Upload cancelled.';
                        cancelBtn.style.display = 'none';
                        cleanupCancelledUploadSizeChart(event, uploadId);
                    } else {
                        statusText.textContent = 'Upload failed. Retrying...';
                        setTimeout(sendChunkSizeChart, 1000);
                    }
                }
            }

            async function mergeChunksSizeChart() {
                try {
                    document.getElementById('fullscreenLoader').style.display = 'block';
                    const response = await fetch('/api/merge-chunks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            user_id: "{{ Auth::user()->id }}",
                            upload_id: uploadId,
                            filename: fileName,
                            total_chunks: totalChunks,
                            product_id: productId,
                            stage_id: null,
                            mime_type: mimeType,
                            identifier: 'productfile'
                        })
                    });

                    const result = await response.json();
                    cancelBtn.style.display = 'none';

                    if (result.status && result.url) {
                        toastr.success(result.message)
                        statusText.textContent = 'Upload complete.';
                        progressWrapper.style.display = 'none';
                        document.getElementById('fullscreenLoader').style.display = 'none';
                        uploadedFilesSizeChart.push({
                            file_id: result.docId,
                            file_name: fileName,
                            file_path: result.url
                        });
                        document.getElementById('productfiles-sizechart').value = JSON.stringify(uploadedFilesSizeChart);
                        document.getElementById('size-chart-uploaded-files-wrapper').insertAdjacentHTML('beforeend', `
                            <div class="col-md-4 uploaded-file-sizechart" data-doc-id="${result.docId}">
                                <div class="file-view">
                                    <div class="sample-pdf-img">
                                        <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon"/>
                                        <p style="width: 150px; overflow: hidden; text-overflow: ellipsis;" title="${fileName}">${fileName}</p>
                                    </div>
                                    <div class="file-view-icons" style="cursor:pointer;">
                                        <img class="remove-icon" src="{{ asset('/assets/images/cross.png') }}" alt="remove icon" onclick="deleteProductDocSizeChart(event,${result.docId})"/>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        statusText.textContent = 'Upload failed.';
                        toastr.error('Error merging uploaded chunks.');
                    }
                } catch (err) {
                    statusText.textContent = 'Merge error.';
                    toastr.error('Merge failed.');
                }

                input.value = '';
            }

            sendChunkSizeChart();
        }

        function cancelProductUploadSizeChart(e) {
            e.preventDefault();
            if (uploadControllerSizeChart) {
                uploadControllerSizeChart.abort();
                uploadControllerSizeChart = null;
                document.getElementById('cancel-upload-btn-sizechart').style.display = 'none';
            }
        }

        function cleanupCancelledUploadSizeChart(event, uploadId) {
            event.preventDefault();
            const progressSection = document.getElementById('upload-progress-wrapper-sizechart');
            fetch('/api/cancel-upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        upload_id: uploadId
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        progressSection.style.display = 'none';
                        $('#sizeChartInputFile').val('');
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                })
                .catch(() => {
                    toastr.error('Error cleaning up upload.');
                });
        }
    </script>
    {{-- end here --}}

    {{-- Tech Pack--}}
    <script>
        let uploadedFilesTechPack = [];
        let uploadControllerTechPack = null;
        let currentUploadIdTechPack = null;

        async function startTechPackattachmentChunkUpload(input) {
            const productIdTech = input.dataset.productid
            const fileTech = input.files[0];
            if (!fileTech) return;

            const chunkSizeTech = 10 * 1024 * 1024;
            const totalChunksTech = Math.ceil(fileTech.size / chunkSizeTech);
            const uploadIdTech = "{{ time() }}";
            currentUploadIdTechPack = uploadIdTech;

            const fileNameTech = fileTech.name;
            const mimeTypeTech = fileTech.type;

            // UI Elements
            const progressBarTech = document.getElementById('upload-progress-bar-techpack');
            const statusTextTech = document.getElementById('upload-status-text-techpack');
            const cancelBtnTech = document.getElementById('cancel-upload-btn-techpack');
            const progressWrapperTech = document.getElementById('upload-progress-wrapper-techpack');

            // Reset UI
            progressWrapperTech.style.display = 'block';
            progressBarTech.style.width = '0%';
            cancelBtnTech.style.display = 'inline-block';
            statusTextTech.textContent = `Uploading "${fileNameTech}"...`;

            let chunkIndexTech = 0;
            let uploadedBytesTech = 0;
            uploadControllerTechPack = new AbortController();

            async function sendChunkTechPack() {
                if (chunkIndexTech >= totalChunksTech) return mergeChunksTechPack();
                const startTech = chunkIndexTech * chunkSizeTech;
                const endTech = Math.min(startTech + chunkSizeTech, fileTech.size);
                const blobTech = fileTech.slice(startTech, endTech);
                const formDataTech = new FormData();
                formDataTech.append('chunk', blobTech);
                formDataTech.append('upload_id', uploadIdTech);
                formDataTech.append('chunk_index', chunkIndexTech);

                try {
                    await fetch('/api/upload-chunk', {
                        method: 'POST',
                        body: formDataTech,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        signal: uploadControllerTechPack.signal
                    });

                    uploadedBytesTech += blobTech.size;
                    const progress = Math.round((uploadedBytesTech / fileTech.size) * 100);
                    progressBarTech.style.width = `${progress}%`;
                    statusTextTech.textContent = `Uploading "${fileNameTech}"... ${progress}%`;
                    chunkIndexTech++;
                    sendChunkTechPack();
                } catch (err) {
                    if (err.name === 'AbortError') {
                        statusTextTech.textContent = 'Upload cancelled.';
                        cancelBtnTech.style.display = 'none';
                        cleanupCancelledUploadTechPack(event, uploadIdTech);
                    } else {
                        statusTextTech.textContent = 'Upload failed. Retrying...';
                        setTimeout(sendChunkTechPack, 1000);
                    }
                }
            }

            async function mergeChunksTechPack() {
                try {
                    document.getElementById('fullscreenLoader').style.display = 'block';
                    const response = await fetch('/api/merge-chunks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            user_id: "{{ Auth::user()->id }}",
                            upload_id: uploadIdTech,
                            filename: fileNameTech,
                            total_chunks: totalChunksTech,
                            product_id: productIdTech,
                            stage_id: null,
                            mime_type: mimeTypeTech,
                            identifier: 'productfile'
                        })
                    });

                    const result = await response.json();
                    cancelBtnTech.style.display = 'none';

                    if (result.status && result.url) {
                        toastr.success(result.message)
                        statusTextTech.textContent = 'Upload complete.';
                        progressWrapperTech.style.display = 'none';
                        document.getElementById('fullscreenLoader').style.display = 'none';
                        uploadedFilesTechPack.push({
                            file_id: result.docId,
                            file_name: fileNameTech,
                            file_path: result.url
                        });
                        document.getElementById('productfiles-techpack').value = JSON.stringify(uploadedFilesTechPack);
                        document.getElementById('tech-pack-uploaded-files-wrapper').insertAdjacentHTML('beforeend', `
                            <div class="col-md-4 uploaded-file-techpack" data-doc-id="${result.docId}">
                                <div class="file-view">
                                    <div class="sample-pdf-img">
                                        <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon"/>
                                        <p style="width: 150px; overflow: hidden; text-overflow: ellipsis;" title="${fileNameTech}">${fileNameTech}</p>
                                    </div>
                                    <div class="file-view-icons" style="cursor:pointer;">
                                        <img class="remove-icon" src="{{ asset('/assets/images/cross.png') }}" alt="remove icon" onclick="deleteProductDocTechPack(event,${result.docId})"/>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        statusTextTech.textContent = 'Upload failed.';
                        toastr.error('Error merging uploaded chunks.');
                    }
                } catch (err) {
                    statusTextTech.textContent = 'Merge error.';
                    toastr.error('Merge failed.');
                }

                input.value = '';
            }

            sendChunkTechPack();
        }

        function cancelProductUploadTechPack(e) {
            e.preventDefault();
            if (uploadControllerTechPack) {
                uploadControllerTechPack.abort();
                uploadControllerTechPack = null;
                document.getElementById('cancel-upload-btn-techpack').style.display = 'none';
            }
        }

        function cleanupCancelledUploadTechPack(event, uploadIdTech) {
            event.preventDefault();
            const progressSection = document.getElementById('upload-progress-wrapper-techpack');
            fetch('/api/cancel-upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        upload_id: uploadIdTech
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        progressSection.style.display = 'none';
                        $('#techPackInputFile').val('');
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                })
                .catch(() => {
                    toastr.error('Error cleaning up upload.');
                });
        }
    </script>
    {{-- End Here --}}
    <!-- Modal -->


    <script>
        function toggleNotification() {
            $('#notificationTable').DataTable().ajax.reload();
            const box = document.getElementById('notification');
            if (box.style.display === "none" || box.style.display === "") {
                box.style.display = "block";
            } else {
                box.style.display = "none";
            }
        }
    </script>

    {{-- Notification Modal --}}
    <script>
        $(document).ready(function () {
        const table = $('#notificationTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            searching: false,
            ordering: false,
            pageLength: 10,
            info: false,
            scrollY: '400px',
            scrollCollapse: true,
            lengthChange: false,
            autoWidth: true,
            scroller: true,
            ajax: {
                url: '{{ route("notification.fetch") }}',
                type: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                {
                    data: 'note',
                    name: 'note',
                    render: function (data) {
                        return data;
                    }
                }
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('notification-item');
                $(row).attr('data-id', data.id);
                if (data.read_at) {
                    $(row).addClass('read').css('opacity', '0.6');
                }
            },
            language: {
                emptyTable: "No notifications",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next:     '<i class="fas fa-chevron-right"></i>'
                }
            },
        });

        // mark notification as read
        $(document).on('click', '.notification-item', function () {
            const id = $(this).data('id');
            $.ajax({
                url: "{{ route('notification.read') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function (res) {
                    if (res.success) {
                        $('#notificationTable').DataTable().ajax.reload();
                        // el.addClass('read').css('opacity', '0.6');
                    }
                }
            });
        });
    });
    </script>

    {{-- End Notification --}}


</body>

</html>