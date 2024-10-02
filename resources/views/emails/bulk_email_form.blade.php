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

                <!-- User checkboxes -->
                <div id="user-checkboxes">
                    @foreach($users as $user)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="users[]" value="{{ $user->id }}" id="user-{{ $user->id }}">
                            <label class="form-check-label" for="user-{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Send Emails</button>
        </form>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Script to handle select all / deselect all functionality -->
    <script>
        document.getElementById('select-all').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('#user-checkboxes input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = true);
        });

        document.getElementById('deselect-all').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('#user-checkboxes input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
        });
    </script>
</body>
</html>
@endsection
