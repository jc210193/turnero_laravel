<!DOCTYPE html>
<html lang="es">
<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Pantalla de turnos</title>

		<link href="{{ asset('css/all.css') }}" rel="stylesheet">
		<link href="{{ asset('css/public-css.css') }}" rel="stylesheet">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
		
</head>
<body>
	
	<div class="full-display" id="app-public-display">
	
		<main class="app-public-display">
			<!-- ENCABEZADO -->
            <div class="app-public-display-title">
                <div>
                    <img src="{{ asset('img/madero-logo.jpeg') }}" height="20px" alt="">
                </div>
            
                <div><h1 v-on:click="pusher()">Bienvenidos</h1></div>
            
                <div><h5 class="text-success">${ hour }</h5></div>
            </div>

			<!-- CONTENIDO -->
			<div class="row height-content" style="margin: 0px;">
				<!-- PANEL IZQUIERDO -->
                <div class="col-4">
                    <div class="tile" style="height: 100%">
                        <div class="row text-center">
                            <div class="col line-head"><h2>Turno</h2></div>
                            <div class="col line-head"><h2>Caja</h2></div>
                        </div>

                        <item-shift v-for="shift in shiftList"
                            v-bind:key = "shift.id"
                            :id = "shift.id"
                            :shift = "shift.shift"
                            :box = "shift.box_name"
                        ></item-shift>
                       
                    </div> 
                </div>


				<!-- PANEL DERECHO -->
				<div class="col-8 text-center">
					<div class="row">
		
						<div class="col-6">
							<h1><i class="fa fa-dashboard"></i>${ attending.shift }</h1>
							<p>Turno</p>
						</div>
						<div class="col-6">
							<h1>${ attending.box }</h1>
							<p>Caja</p>
						</div>

						<!-- CARRUSEL -->
						@include('display.components.Carousel')
					</div>
				</div>

			</div>
			
	
		</main>

	</div>
			
	{{-- Scripts --}}
	<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
	{{-- <script src="{{ asset('js/main.js') }}"></script>
	<script src="{{ asset('js/popper.min.js') }}"></script> --}}
	
	<script src="{{ asset('js/axios.js') }}"></script>
	<script src="{{ asset('js/vue.js') }}"></script>
	<script src="{{ asset('js/public/display.js') }}"></script>

	<script>

		$('.carousel').carousel()

		// var _that = this
	
		// alert('hola')

		// // Enable pusher logging - don't include this in production
		// Pusher.logToConsole = true;

		// var pusher = new Pusher('56423364aba2e84b5180', {
		// 	cluster: 'us2'
		// });

		// var channel = pusher.subscribe(this.channel);

		// channel.bind('toPanel', function(data) {
		// 	// alert(JSON.stringify(data));
		// 	// this.addShift("hola")
		// 	if (data != null) {
		// 		_that.addShift(data.text)
		// 		alert('si entro')
		// 	}
		// });

	</script>
</body>
</html>