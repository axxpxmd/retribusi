@if (session()->has('success'))
<div class="alert alert-success alert-dismissible fade show text-center bdr-5 col-md-12 container" id="successAlert" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<!-- Alert Errors -->
@if (count($errors) > 0)
<div class="alert alert-danger" id="errorAlert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong>Whoops Error!</strong>&nbsp;
    <span>You have {{ $errors->count() }} error</span>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#successAlert").fadeTo(10000, 1000).slideUp(1000, function() {
            $("#successAlert").slideUp(1000);
        });
    });

    $(document).ready(function() {
        $("#errorAlert").fadeTo(10000, 1000).slideUp(1000, function() {
            $("#errorAlert").slideUp(1000);
        });
    });
</script>