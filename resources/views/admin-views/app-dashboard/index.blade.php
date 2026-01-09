@extends('layouts.admin.app')

@section('title', 'App Dashboard Sections')

@include('admin-views.partials._loader')

@push('css_or_js')
<style>
    .sortable-handle {
        cursor: move;
        color: #5e6278;
    }
    
    .sortable-handle:hover {
        color: #009ef7;
    }
    
    .sort-order-input {
        width: 80px;
        text-align: center;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-dashboard"></i>
                </span>
                <span>
                    App Dashboard Sections
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                Dashboard Sections <span class="badge badge-soft-dark ml-2">{{ $sections->count() }}</span>
                            </h5>
                            <div>
                                <small class="text-muted">Manage which sections appear in the mobile app dashboard</small>
                            </div>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th class="border-0" style="width: 50px;">#</th>
                                    <th class="border-0" style="width: 50px;">Sort</th>
                                    <th class="border-0">Section Name</th>
                                    <th class="border-0">Section Key</th>
                                    <th class="border-0">Sort Order</th>
                                    <th class="border-0 text-center">Status</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-sections">
                                @foreach($sections as $key => $section)
                                    <tr data-id="{{ $section->id }}" data-sort-order="{{ $section->sort_order }}">
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{ $key + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="tio-drag sortable-handle" title="Drag to reorder"></i>
                                        </td>
                                        <td>
                                            <span class="d-block font-size-sm text-body font-weight-bold">
                                                {{ $section->section_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-secondary">
                                                {{ $section->section_key }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.app-dashboard.update-sort-order', $section->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" 
                                                       name="sort_order" 
                                                       value="{{ $section->sort_order }}" 
                                                       min="0"
                                                       class="form-control form-control-sm sort-order-input d-inline-block"
                                                       onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <label class="toggle-switch toggle-switch-sm" for="status-{{ $section->id }}">
                                                <input type="checkbox" 
                                                       class="toggle-switch-input dynamic-checkbox"
                                                       data-id="status-{{ $section->id }}"
                                                       data-type="status"
                                                       data-image-on='{{ asset('assets/admin/img/modal/zone-status-on.png') }}'
                                                       data-image-off="{{ asset('assets/admin/img/modal/zone-status-off.png') }}"
                                                       data-title-on="Want to activate this section?"
                                                       data-title-off="Want to deactivate this section?"
                                                       id="status-{{ $section->id }}" 
                                                       {{ $section->is_active ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <form action="{{ route('admin.app-dashboard.update-status') }}" method="post" id="status-{{ $section->id }}_form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $section->id }}">
                                                <input type="hidden" name="status" value="{{ $section->is_active ? 0 : 1 }}">
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($sections->isEmpty())
                            <div class="text-center p-4">
                                <img class="w-7rem mb-3" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="Image Description">
                                <h5>No dashboard sections found</h5>
                                <p class="text-muted">Run the seeder to create default sections.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Hide loader immediately if page is already loaded
    if (document.readyState === 'complete' && typeof PageLoader !== 'undefined') {
        PageLoader.hide();
    }
    
    $(document).ready(function () {
        // Initialize Sortable
        var sortable = Sortable.create(document.getElementById('sortable-sections'), {
            handle: '.sortable-handle',
            animation: 150,
            onEnd: function(evt) {
                // Update sort orders based on new position
                var sections = [];
                $('#sortable-sections tr').each(function(index) {
                    var id = $(this).data('id');
                    sections.push({
                        id: id,
                        sort_order: index + 1
                    });
                });
                
                // Send AJAX request to update sort orders
                $.ajax({
                    url: '{{ route('admin.app-dashboard.update-sort-orders') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sections: sections
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload page to reflect changes
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to update sort order:', xhr);
                        alert('Failed to update sort order. Please try again.');
                        location.reload();
                    }
                });
            }
        });
        
        // Hide loader immediately when document is ready
        if (typeof PageLoader !== 'undefined') {
            setTimeout(function() {
                PageLoader.hide();
            }, 300);
        }
        
        // Handle status toggle
        $(document).on('change', '.dynamic-checkbox', function() {
            var $checkbox = $(this);
            var formId = $checkbox.data('id') + '_form';
            var $form = $('#' + formId);
            
            // Update hidden input value
            $form.find('input[name="status"]').val($checkbox.is(':checked') ? 1 : 0);
            
            // Submit form normally to redirect back to listing
            $form.submit();
        });
        
        // Handle delete confirmation with form-alert using project modal
        $(document).on('click', '.form-alert', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            var baseId = $btn.data('form-id') || $btn.data('id');
            var message = $btn.data('message') || 'Want to delete this?';
            var title = $btn.data('title') || 'Delete Confirmation';
            var imageOn = $btn.data('image-on') || '{{ asset('assets/admin/img/modal/delete-icon.png') }}';
            
            // Set modal content
            $('#toggle-status-title').text(title);
            $('#toggle-status-message').text(message);
            $('#toggle-status-image').attr('src', imageOn);
            $('#toggle-status-ok-button').attr('toggle-ok-button', baseId);
            
            // Show modal
            $('#toggle-status-modal').modal('show');
        });
        
        // Fallback: Hide loader when window is fully loaded
        $(window).on('load', function() {
            setTimeout(function() {
                if (typeof PageLoader !== 'undefined') {
                    PageLoader.hide();
                }
            }, 100);
        });
    });
</script>
@endpush

