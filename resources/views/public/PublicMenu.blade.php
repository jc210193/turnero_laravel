<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Inicio | Madero</title>     
        
        <link rel="stylesheet" href="css/public-css.css">
		<link href="{{ asset('css/all.css') }}" rel="stylesheet">
    </head>
    <body>
        <section class="material-half-bg">
            <div class="cover"></div>
        </section>
        <section class="login-content">
            <div class="row justify-content-center" style="width: 80%">
                <div class="col-lg-7 col-md-10 col-sm-10 tile text-center">
                    <h3 class="tile-title">¿Qué pantalla deseas abrir?</h3>
                    <div class="tile-body">
                        <div class="row my-4">
                            <div class="col-md-6 col-sm-12 mb-2">
                                <a class="btn btn-dark btn-lg btn-block" href="{{ route('shift.generator') }}">
                                    <p><i class="fas fa-ticket-alt fa-3x"></i></p>
                                    Generador de turnos
                                </a>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <a class="btn btn-dark btn-lg btn-block" href="{{ route('shift.list') }}">
                                    <p><i class="fas fa-desktop fa-3x"></i></p>
                                    Lista de turnos
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>
                    
            </div>
        </section>
    </body>
</html>
