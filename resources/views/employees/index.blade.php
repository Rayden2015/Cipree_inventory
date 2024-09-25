@extends('layouts.admin')

@section('content')
    <h2>Employee List</h2>
    @can('add-employee')
    <a href="{{ route('employees.create') }}" class="btn btn-primary float-right">Create New Employee</a>
    <br>
    @endcan
    

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Job Title</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->fname }}</td>
                    <td>{{ $employee->lname }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->job_title }}</td>
                    
                        
             
                    <td>
                        @can('edit-employee')
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-success">Edit</a>
                        @endcan

                        @can('view-employee')
                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary">Show</a>
                        @endcan
                        @can('delete-employee')
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
