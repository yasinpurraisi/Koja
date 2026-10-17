@if($errors->any())
    <div class="alert alert-error" role="alert">
        <strong>Please check the following:</strong>

        <ul class="error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif