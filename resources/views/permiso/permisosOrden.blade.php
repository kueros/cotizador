

<x-app-layout>

@section('content')
<div class="container">
    <h1>Gestionar Permisos</h1>
    <ul id="sortable-list" class="list-group">
        @foreach($permisos as $permiso)
            <li class="list-group-item" data-id="{{ $permiso->id }}">
                <i class="fas fa-bars" style="margin-right:10px; cursor:grab; "></i>{{ $permiso->nombre }} (Orden: {{ $permiso->orden }}, Sección: {{ $permiso->seccion_id }})
            </li>
        @endforeach
    </ul>
    <button id="save-order" class="btn btn-primary mt-3">Guardar Orden</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('sortable-list');
        var sortable = Sortable.create(el, {
            animation: 150,
            ghostClass: 'blue-background-class'
        });

        document.getElementById('save-order').addEventListener('click', function () {
            var orden = sortable.toArray();

            fetch('{{ route('permisos.reordenar') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ orden: orden })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>
</x-app-layout>	
