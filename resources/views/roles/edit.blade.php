@extends('layouts.admin')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Edit Role</title>
        <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>


    <body>
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header">
                        <div class="float-start">
                            Edit Role
                        </div>
                        <div class="float-end">
                            <a href="{{ route('roles.index') }}" class="btn btn-primary btn-sm">&larr; Back</a>
                        </div>
                    </div>
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
        
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                    <div class="card-body">
                        <form action="{{ route('roles.update', $role->id) }}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="mb-3 row">
                                <label for="name" class="col-md-4 col-form-label text-md-end text-start">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ $role->name }}">
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="permissions"
                                    class="col-md-4 col-form-label text-md-end text-start">Permissions</label>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <input type="text" id="permission_search" class="form-control"
                                            placeholder="Search permissions (e.g. work-order, asset, view-…)"
                                            autocomplete="off">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all_permissions">
                                        <label class="form-check-label" for="select_all_permissions" id="select_all_permissions_label">
                                            Select all visible
                                        </label>
                                    </div>
                                    <div class="form-check permission-list"
                                        style="height: 210px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px;">
                                        @forelse ($permissions as $permission)
                                            <div class="form-check permission-item" data-permission-name="{{ strtolower($permission->name) }}">
                                                <input class="form-check-input @error('permissions') is-invalid @enderror"
                                                    type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                    id="permission_{{ $permission->id }}"
                                                    {{ in_array($permission->id, $rolePermissions ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <p>No permissions available</p>
                                        @endforelse
                                    </div>
                                    <p id="permission_search_no_match" class="small text-muted mt-1 mb-0" style="display: none;">No permissions match your search.</p>
                                    @if ($errors->has('permissions'))
                                        <span class="text-danger">{{ $errors->first('permissions') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Update Role">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('select_all_permissions');
                const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
                const permissionItems = document.querySelectorAll('.permission-item');
                const searchInput = document.getElementById('permission_search');
                const noMatchEl = document.getElementById('permission_search_no_match');

                function getVisiblePermissionItems() {
                    return document.querySelectorAll('.permission-item.permission-visible');
                }

                function syncSelectAllCheckbox() {
                    var visible = getVisiblePermissionItems();
                    var visibleChecked = document.querySelectorAll('.permission-item.permission-visible input[name="permissions[]"]:checked');
                    selectAllCheckbox.checked = visible.length > 0 && visibleChecked.length === visible.length;
                }

                function updateSelectAllLabel() {
                    var label = document.getElementById('select_all_permissions_label');
                    if (label) {
                        var hasActiveSearch = searchInput && (searchInput.value || '').trim();
                        label.textContent = hasActiveSearch ? 'Select all visible' : 'Select all';
                    }
                }

                function filterPermissions() {
                    const q = searchInput ? (searchInput.value || '').trim().toLowerCase() : '';
                    let visibleCount = 0;
                    permissionItems.forEach(function(item) {
                        const name = (item.getAttribute('data-permission-name') || '').toLowerCase();
                        const show = !q || name.indexOf(q) !== -1;
                        item.style.display = show ? '' : 'none';
                        if (show) {
                            item.classList.add('permission-visible');
                            visibleCount++;
                        } else {
                            item.classList.remove('permission-visible');
                        }
                    });
                    noMatchEl.style.display = (q && visibleCount === 0) ? 'block' : 'none';
                    updateSelectAllLabel();
                    syncSelectAllCheckbox();
                }

                if (searchInput) {
                    searchInput.addEventListener('input', filterPermissions);
                    searchInput.addEventListener('keyup', filterPermissions);
                }

                // Run once on load so all items get permission-visible when search is empty
                filterPermissions();

                // "Select all visible" must only affect visible items; never touch hidden (filtered) checkboxes to avoid data loss.
                selectAllCheckbox.addEventListener('change', function() {
                    getVisiblePermissionItems().forEach(function(item) {
                        var cb = item.querySelector('input[name="permissions[]"]');
                        if (cb) cb.checked = selectAllCheckbox.checked;
                    });
                });

                permissionCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', syncSelectAllCheckbox);
                });
            });
        </script>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
