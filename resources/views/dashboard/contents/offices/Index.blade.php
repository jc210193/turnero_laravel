@section('title', 'Sucursales')
@section('subtitle', 'Listado de sucursales')
@section('icon', 'fas fa-map-marker-alt')
@section('breadcrumb')
    <li class="breadcrumb-item active"><a href="#">Sucursales</a></li>
@endsection

@section('content')

@section('content')

@if(Session::has('success_message'))
<div class="row">
    <div class="col-lg-6">
        <div class="bs-component">
            <div class="alert alert-dismissible alert-success">
                <button class="close" type="button" data-dismiss="alert">×</button>
                <strong>{{ Session::get('success_title' )}}</strong> {{ Session::get('success_message' )}}
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="/dashboard/offices/create" class="btn btn-primary" role="button">Nueva sucursal</a>
                    </div>
                </div>
            </div>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="officeTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Municipio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lstOffices as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->address }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->municipality->name }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                            <button class="btn btn-primary" type="button"><i class="fa fa-cogs fa-lg"></i></button>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="/dashboard/offices/edit/{{ $item->id }}">Editar</a>
                                                    <a class="dropdown-item" href="#" onclick="modal({{ $item->id }})">Eliminar</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.contents.offices.modal.Delete')
@endsection

@section('scripts')
    <!-- Data table plugin-->
    <script type="text/javascript" src="{{ asset('js/plugins/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/plugins/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript">
        $('#officeTable').DataTable( {
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No existen registros",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ total registros)"
            }
        });

        function modal(office){
            $('#confirm-delete-modal').modal('show')
            $('#btnYes').attr('href', 'offices/delete/'+office)
        };
    </script>
@endsection

@include('dashboard.components.Navbar')
@include('dashboard.components.Sidebar')
@include('dashboard.components.Scripts')
@include('dashboard.components.Stylesheets')

@extends('dashboard.components.Main')