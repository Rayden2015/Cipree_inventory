@extends('layouts.admin')
<title>Permissions</title>
@section('content')
    <div class="bg-light p-4 rounded">
        <h2>Permissions</h2>
        <div class="lead d-flex justify-content-between align-items-center flex-wrap">
            <span>Manage your permissions here.</span>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">Add permissions</a>
        </div>

        <form method="GET" action="{{ route('permissions.index') }}" class="form-inline mt-3 mb-2">
            <div class="form-group mr-2 mb-2">
                <input type="text"
                       name="search"
                       value="{{ old('search', $search ?? '') }}"
                       class="form-control"
                       placeholder="Search by name or guard">
            </div>
            <button type="submit" class="btn btn-primary mb-2">Search</button>
            <a href="{{ route('permissions.index') }}" class="btn btn-default mb-2 ml-2">Reset</a>
        </form>

        <div class="mt-2">
            {{-- @include('layouts.partials.messages') --}}
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col" width="15%">Name</th>
                    <th scope="col">Guard</th>
                    <th scope="col" colspan="3" width="1%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->guard_name }}</td>
                        <td>
                            <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-info btn-sm">
                                Edit
                            </a>
                        </td>
                        <td>
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure?')"
                                        class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No permissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-2">
            {{ $permissions->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
