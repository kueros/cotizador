<!--<nav aria-label="breadcrumb" style="margin-left: 15px;">-->
@if(isset($page) && isset($breadcrumbs) && count($breadcrumbs))
    <ol class="breadcrumb">
        @foreach($breadcrumbs as $breadcrumb)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
        @endforeach
        @if(isset($page))
            <li class="breadcrumb-item active" aria-current="page">{{ $page ?? 'Página Actual' }}</li>
        @endif
    </ol>
@endif
<!--</nav>-->
