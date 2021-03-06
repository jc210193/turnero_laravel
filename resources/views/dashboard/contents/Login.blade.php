<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- Main CSS-->
        <link href="{{ asset('css/main.css') }}" rel="stylesheet" />
        <!-- Font-icon css-->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <title>Login - Dashboard</title>
    </head>
    <body>
        <section class="material-half-bg">
            <div class="cover"></div>
        </section>

        <section class="login-content">
            @if(Session::has('login_error_message'))
                <div class="row">
                    <div class="col-lg-12">
                        <div class="bs-component">
                            <div class="alert alert-dismissible alert-warning">
                                <button class="close" type="button" data-dismiss="alert">×</button>
                                <h4>{{ Session::get('login_error_title' )}}</h4>
                                <p>{{ Session::get('login_error_message' )}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            

            <div class="logo">
                <h1>Madero Refaccionarias</h1>
            </div>

            <div class="login-box">
                {!! Form::open(['route' => 'login-dashboard', 'class' => 'login-form']) !!}
                    <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>INICIAR SESIÓN</h3>
                    <div class="form-group">
                        <label class="control-label">USUARIO</label>
                        <input class="form-control" type="text" placeholder="Correo" name="txtEmail" autofocus />
                    </div>
                    <div class="form-group">
                        <label class="control-label">CONTRASEÑA</label>
                        <input class="form-control" type="password" name="txtPassword" placeholder="Contraseña" />
                    </div>
                    <div class="form-group">
                        <div class="utility">
                            <p class="semibold-text mb-2"><a href="#" data-toggle="flip">¿Olvidaste la contraseña?</a></p>
                        </div>
                    </div>
          
                    <div class="form-group btn-container">
                        <button class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i>ENTRAR</button>
                    </div>
                {!! Form::close() !!}

                <form class="forget-form" action="index.html">
                    <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>¿Olvidaste la contraseña?</h3>
                    <div class="form-group">
                        <label class="control-label">EMAIL</label>
                        <input class="form-control" type="text" placeholder="Email">
                    </div>
                    <div class="form-group btn-container">
                        <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i>RESETEAR</button>
                    </div>
                    <div class="form-group mt-3">
                        <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Volver al Login</a></p>
                    </div>
                </form>
            </div>
        </section>
        <!-- Essential javascripts for application to work-->
        <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        <!-- The javascript plugin to display page loading on top-->
        <script src="{{ asset('js/plugins/pace.min.js') }}"></script>
        <script type="text/javascript">
            // Login Page Flipbox control
            $('.login-content [data-toggle="flip"]').click(function() {
                $('.login-box').toggleClass('flipped');
                return false;
            });
        </script>
    </body>
</html>