<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($breadcrumbs as $breadcrumb)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
        @endforeach
        @if(isset($page))
            <li class="breadcrumb-item active" aria-current="page">{{ $page ?? 'Página Actual' }}</li>
        @endif
    </ol>
</nav>
