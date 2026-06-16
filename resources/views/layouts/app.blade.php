
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>

    {{-- CSRF meta tag — JavaScript reads this to get the token --}}
    {{-- Required for all fetch() POST/PUT/PATCH/DELETE requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<header class="site-header">
    <div class="container header-container">
        <div class="brand">
            <div class="brand-icon">✓</div>
            <div class="brand-text">
                <h1>Tasks Organizer</h1>
                <p>Your secretary to help you organize your day!</p>
            </div>
        </div>
        <nav class="nav-links">
            <a href="{{ route('tasks.index') }}#task-list-content">My Tasks</a>
            <a href="{{ route('tasks.index') }}#task-form">Add Task</a>
            <a href="{{ route('tasks.index') }}#task-filters">Filters</a>
        </nav>
    </div>
</header>

@yield('content')

<footer class="site-footer">
    <div class="container footer-container">
        <div class="footer-left">
            <h3>Task Manager</h3>
            <p>IS333 Web-Based Information Systems Project</p>
        </div>
        <div class="footer-right">
            <p>Faculty of Computing and Artificial Intelligence - Cairo University</p>
            <p>&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="{{ asset('js/API_Ops.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>