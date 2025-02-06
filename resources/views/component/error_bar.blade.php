@if(session()->has('success'))
    <div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">
        <button class="close" type="button" data-dismiss="alert">
            <span>×</span>
            <span class="sr-only">Close</span>
        </button>
        {!! session()->get('success') !!}
    </div>
@endif
@if(session()->has('error'))
    <div class="alert alert-danger alert-styled-left">
        <button class="close" type="button" data-dismiss="alert">
            <span>×</span>
            <span class="sr-only">Close</span>
        </button>
        {!! session()->get('error') !!}
    </div>
@endif