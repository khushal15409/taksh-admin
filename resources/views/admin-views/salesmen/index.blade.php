@extends('layouts.admin.app')

@section('title', 'Salesmen Management')

@push('css_or_js')
@endpush

@section('content')
@include('admin-views.partials._loader')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Salesmen Management</h1>
                <p class="page-header-text">Manage salesmen and their locations</p>
            </div>
            <div class="col-sm-auto">
                <a href="{{ route('admin.salesmen.create') }}" class="btn btn-primary">
                    <i class="tio-add"></i> Add Salesman
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">Salesmen List</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Last Location</th>
                            <th>Last Updated</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesmen as $salesman)
                        <tr>
                            <td>{{ $salesman->id }}</td>
                            <td>{{ $salesman->name }}</td>
                            <td>{{ $salesman->mobile }}</td>
                            <td>{{ $salesman->email ?? 'N/A' }}</td>
                            <td>{{ $salesman->salesmanProfile->state->name ?? 'N/A' }}</td>
                            <td>{{ $salesman->salesmanProfile->city->name ?? 'N/A' }}</td>
                            <td>
                                @if($salesman->location)
                                <small>
                                    {{ number_format($salesman->location->latitude, 6) }}, 
                                    {{ number_format($salesman->location->longitude, 6) }}
                                </small>
                                @else
                                <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td>
                                @if($salesman->location && $salesman->location->updated_at)
                                <small>{{ $salesman->location->updated_at->format('d M Y H:i') }}</small>
                                @else
                                <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                @if($salesman->is_active)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.salesmen.edit', $salesman->id) }}" 
                                       class="btn btn-sm btn-info" title="Edit">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.salesmen.toggle-status', $salesman->id) }}" 
                                          method="POST" 
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Are you sure you want to {{ $salesman->is_active ? 'disable' : 'enable' }} this salesman?');">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $salesman->is_active ? 'btn-warning' : 'btn-success' }}" 
                                                title="{{ $salesman->is_active ? 'Disable' : 'Enable' }}">
                                            <i class="tio-{{ $salesman->is_active ? 'block' : 'checkmark' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No salesmen found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $salesmen->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    function hideLoader() {
        if (typeof PageLoader !== 'undefined' && PageLoader.hide) {
            PageLoader.hide();
        }
        var loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hide');
            loader.style.display = 'none';
            loader.style.visibility = 'hidden';
            loader.style.opacity = '0';
        }
        $('#page-loader').addClass('hide').hide().css({
            'display': 'none',
            'visibility': 'hidden',
            'opacity': '0'
        });
    }
    
    $(document).ready(function() {
        hideLoader();
        setTimeout(function() {
            hideLoader();
        }, 100);
    });
    
    $(window).on('load', function() {
        hideLoader();
    });
    
    if (document.readyState === 'complete') {
        hideLoader();
    }
    
    setTimeout(function() {
        hideLoader();
    }, 1000);
</script>
@endpush

