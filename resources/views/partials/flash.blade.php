@if(session('success'))
    <div class="alert alert-success" role="status">
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" role="alert">
        <span>{{ session('error') }}</span>
    </div>
@endif