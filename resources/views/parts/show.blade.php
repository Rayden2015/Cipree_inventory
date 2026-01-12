@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-cogs"></i> Part Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parts.index') }}">Parts</a></li>
                    <li class="breadcrumb-item active">Part Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye mr-1"></i> Part Information</h3>
                <div class="card-tools">
                    <a href="{{ route('parts.edit', $part->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('parts.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">ID</th>
                                    <td>{{ $part->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><strong>{{ $part->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Supplier</th>
                                    <td>{{ $part->supplier->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Site</th>
                                    <td>{{ $part->site->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $part->location->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <td>
                                        <span class="badge badge-info">{{ $part->quantity ?? 0 }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $part->description ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('parts.edit', $part->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i> Edit Part
                    </a>
                    <form action="{{ route('parts.destroy', $part->id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this part?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                    <a href="{{ route('parts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
