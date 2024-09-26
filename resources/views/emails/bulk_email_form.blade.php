@extends('layouts.admin')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Bulk Email</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <h1>Send Bulk Email</h1>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('send.bulk.email.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="users">Select Users</label>

                <!-- Buttons for select all / deselect all -->
                <div>
                    <button type="button" class="btn btn-secondary" id="select-all">Select All</button>
                    <button type="button" class="btn btn-secondary" id="deselect-all">Deselect All</button>
                </div>

                <!-- User multi-select -->
                <select id="users" name="users[]" class="form-control" multiple required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Send Emails</button>
        </form>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Script to handle select all / deselect all functionality -->
    <script>
        document.getElementById('select-all').addEventListener('click', function() {
            let users = document.getElementById('users');
            for (let i = 0; i < users.options.length; i++) {
                users.options[i].selected = true;
            }
        });

        document.getElementById('deselect-all').addEventListener('click', function() {
            let users = document.getElementById('users');
            for (let i = 0; i < users.options.length; i++) {
                users.options[i].selected = false;
            }
        });
    </script>
</body>
</html>
@endsection
