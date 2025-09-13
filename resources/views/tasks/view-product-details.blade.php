@extends('layouts.main')
@section('title', '- Task Product Details')
@section('main-content')
<style>
    .dataTables_scrollHeadInner {
        width: 100% !important;
    }
</style>

<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <a href="javascript:void(0);" onclick="window.history.back();">
                                <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                            </a>
                            <h1>Product Details <span class="product-name">({{ $product->product_code }})</span></h1>
                        </div>
                        <div class="add-members-btn-box">
                            @if ($product->product_status == 0)
                            <button class="pending-btn" style="cursor: none">
                                Pending
                            </button>
                            @elseif($product->product_status == 1)
                            <button class="in-progress-btn" style="cursor: none">
                                In Progress
                            </button>
                            @elseif($product->product_status == 2)
                            <button class="on-hold-btn" style="cursor: none">
                                On Hold
                            </button>
                            @elseif($product->product_status == 3)
                            <button class="completed-btn" style="cursor: none">
                                Completed
                            </button>
                            @endif
                            <a href="{{ route('product.stages', ['productId' => $product->id]) }}"
                                class="tracking-btn">Tracking</a>
                        </div>
                    </div>
                    <div class="content-box">
                        <div class="products-tab-box">
                            <ul class="nav nav-pills hr-line">
                                @if ($product->product_type == 1)
                                <li class="nav-item">
                                    <a class="nav-link {{ $product->product_type == 1 ? 'active' : '' }}"
                                        data-toggle="pill" href="#sizeChart" data-type="1">Size
                                        Chart</a>
                                </li>
                                @endif
                                @if ($product->product_type == 2)
                                <li class="nav-item">
                                    <a class="nav-link {{ $product->product_type == 2 ? 'active' : '' }}"
                                        data-toggle="pill" href="#techPack" data-type="2">Tech
                                        Pack</a>
                                </li>
                                @endif
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content tab-content-box">
                                <div id="sizeChart"
                                    class="tab-pane {{ $product->product_type == 1 ? 'active show' : 'fade' }}">
                                    {!! $sizechart !!}
                                </div>

                                <div id="techPack"
                                    class="tab-pane {{ $product->product_type == 2 ? 'active show' : 'fade' }}">
                                    {!! $techpack !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Modal --}}
<div class="modal fade" id="jsonModalTask" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail View</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="jsonContentTask" style="white-space: pre-wrap;"></pre>
            </div>
        </div>
    </div>
</div>
{{-- end modal --}}
@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $dynamicTableId = "#task-history-table-{{ $product->id }}";
        $($dynamicTableId).DataTable({
            processing: true,
            serverSide: true,
            bFilter: false,
            pageLength: 10,
            scrollX: false,
            responsive: true,
            autoWidth: true,
            lengthMenu: [
                [5, 10, 25, 50],
                [5, 10, 25, 50]
            ],
            ajax: {
                url: "{{ route('task.product.history.list') }}",
                type: "POST",
                data: function(d) {
                        d.productId = "{{$product->id}}";
                    }
            },
            language: {
                paginate: {
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>'
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'note',
                    name: 'note',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'null',
                    name: 'changes',
                    orderable: false,
                    searchable: false,
                     render: function(data, type, row) {
                        return `<button class="btn btn-sm view-json" style="background-color:#b98f6d;color:#fff;" data-json='${JSON.stringify(row.changes)}'>view</button>`;
                    }
                },
            ],
        });
    });


    // Modal script for history
    $(document).on('click', '.view-json', function () {
        const jsonData = $(this).data('json');
        const formatted = JSON.stringify(jsonData, null, 4);
        $('#jsonContentTask').text(formatted);
        const myModal = new bootstrap.Modal(document.getElementById('jsonModalTask'));
        myModal.show();
    });
</script>
{{-- Change Tab Script --}}
<script>
    $(document).ready(function() {
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            const tabType = $(e.target).data('type');
            const target = $(e.target).attr('href');
            const productSlug = "{{ $product->slug }}"
            window.location.href = base_url + '/product/' + productSlug + '/product-details?type=' +
                tabType;
        });
    });
</script>

{{-- Comment Script --}}
<script>
    // Size Chart Click Handling
    $('#size-chart-comment-btn').on("click", function(e) {
        $('#size-chart-history-btn').removeClass('comman-btn').addClass('border-comman-btn');
        $(this).addClass('comman-btn').removeClass('border-comman-btn');
        $('#view-sizechart-comment').removeClass('d-none');
        $('#view-sizechart-history').addClass('d-none');
    });
    
    $('#size-chart-history-btn').on("click", function(e) {
        $('#task-history-table-{{ $product->id }}').DataTable().ajax.reload();
        $('#size-chart-comment-btn').removeClass('comman-btn').addClass('border-comman-btn');
        $(this).removeClass('border-comman-btn').addClass('comman-btn');
        $('#view-sizechart-history').removeClass('d-none');
        $('#view-sizechart-comment').addClass('d-none');
    });

     // Techpack click handling
    $('#techpack-comment-btn').on("click", function(e) {
        $('#techpack-history-btn').removeClass('comman-btn').addClass('border-comman-btn');
        $(this).addClass('comman-btn').removeClass('border-comman-btn');
        $('#view-techpack-comment').removeClass('d-none');
        $('#view-teckpack-history').addClass('d-none');
    });
    
    $('#techpack-history-btn').on("click", function(e) {
        $('#task-history-table-{{ $product->id }}').DataTable().ajax.reload();
        $('#techpack-comment-btn').removeClass('comman-btn').addClass('border-comman-btn');
        $(this).removeClass('border-comman-btn').addClass('comman-btn');
        $('#view-teckpack-history').removeClass('d-none');
        $('#view-techpack-comment').addClass('d-none');
    });
</script>

<script>
    $(document).ready(function () {
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            document.getElementById('fullscreenLoader').style.display = 'block';
            document.getElementById('loaderText').textContent = ``;
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('post.comment') }}",
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status) {
                        document.getElementById('fullscreenLoader').style.display = 'none';
                        $('#comments').prepend(response.html);
                        toastr.success(response.message);
                        $('#commentForm')[0].reset();
                        $('#comment-uploaded-files-wrapper').empty();
                    }else {
                        document.getElementById('fullscreenLoader').style.display = 'none';
                        toastr.error(response.message || 'Failed to add comment');
                    }
                },
                error: function(xhr) {
                    document.getElementById('fullscreenLoader').style.display = 'none';
                    toastr.error('Filed to add comment');
                }
            });
        });
    });
</script>

<script>
    // SiZe Chart
        const dtCom = new DataTransfer();
        $('#commentattchment').on('change', function () {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileName = file.name;
                const fileCard = $(`
                    <div class="col-md-3 uploaded-file">
                        <div class="comment-file-view">
                            <div class="comment-pdf-img">
                                <img src="{{ asset('/assets/images/attachment.png') }}" alt="file icon" />
                                </div>
                                <p style="width: 280px; overflow: hidden; text-overflow: ellipsis; white-space:nowrap">${fileName}</p>
                            <div class="comment-file-view-icons">
                                <img class="remove-icon" src="{{ asset('/assets/images/cross.png') }}" alt="remove icon" />
                            </div>
                        </div>
                    </div>
                `);
                // Add remove functionality
                fileCard.find('.remove-icon').on('click', function () {
                    fileCard.remove();
                    for (let j = 0; j < dtCom.items.length; j++) {
                        if (dtCom.items[j].getAsFile().name === fileName) {
                            dtCom.items.remove(j);
                            break;
                        }
                    }
                    $('#commentattchment')[0].files = dtCom.files;
                });
                $('#comment-uploaded-files-wrapper').append(fileCard);
                dtCom.items.add(file);
            }
            this.files = dtCom.files;
        });   
</script>
@endpush
@endsection