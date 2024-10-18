@extends('layouts.admin')
<title>Add Roles</title>
@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header">
                <div class="float-start">
                    Add New Role
                </div>
                <div class="float-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-primary btn-sm">&larr; Back</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="post">
                    @csrf

                    <div class="mb-3 row">
                        <label for="name" class="col-md-4 col-form-label text-md-end text-start">Name</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="permissions" class="col-md-4 col-form-label text-md-end text-start">Permissions</label>
                        <div class="col-md-8">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label" for="select-all">
                                    Select All
                                </label>
                            </div>
                            
                            <div class="row">
                                @forelse ($permissions as $permission)
                                    <div class="col-md-3"> <!-- Adjust this class to control the number of checkboxes per row -->
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" id="permission{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}"
                                                {{ in_array($permission->id, old('permissions') ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <p>No permissions available</p>
                                @endforelse
                            </div>
                    
                            @if ($errors->has('permissions'))
                                <span class="text-danger">{{ $errors->first('permissions') }}</span>
                            @endif
                        </div>
                    </div>
                    
                    
                    
                    <div class="mb-3 row">
                        <input type="submit" class="col-md-3 offset-md-5 btn btn-primary" value="Add Role">
                    </div>
                    
                </form>
            </div>
        </div>
    </div>    
</div>
<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });
</script>

    
@endsection