Vue.component('item-my-list', {
    props: [
        'id',
        'shift',
        'type',
        'speciality',
    ],
    template: `
        <div class="row text-center mb-2">
            <div class="col-4"><h6>{{ shift }}</h6></div>
            <div class="col-4"><h6>{{ type }}</h6></div>
            <div class="col-4"><h6>{{ speciality }}</h6></div>                  
        </div>
    `,
})

var appPanel = new Vue({
    delimiters: ['${', '}'],
    el: '#app-panel-tickets',
    data: {
        isActive: false,
        menuChannel: null,
        panelChannel: null,
        user: null,
        attending:{
            id: 0,
            status:'',
            shift: '-',
            speciality: '-',
            type: '-',
            time: '-',
            sex: '-',
            client: '-',
            number: '-',
        },
        shiftList:[],
        advisors: []
    },

    methods: {
        getListShift (dataChannel) {
            var _that = this
                            
            axios.post('get-shift-advisor', {
                type: 1,
                userId: this.user
            })
            .then(function (response) {
                _that.shiftList = response.data
            })
            .catch(function (error) {
                console.log(error)
            })
        },

        getListAdvisors () {
            var _that = this

            if (this.attending.id != 0) {

                axios.get('shift/get-advisors')
                .then(function (response) {
                    _that.advisors = response.data
                    $('#reassignment-modal').modal('show')
                })
                .catch(function (error) {
                    console.log(error)
                })   
            } else {
                _that.notify('warning', ' No tiene un turno en proceso', 'fa fa-exclamation-triangle')
            }
        },

        setServiceOn () {
            var _that = this

            axios.post('get-user')
            .then(function (response) {

                if (response.data['channel'] == null) {
                    _that.notify('info', 'No estas vinculado a una oficiona', 'fa fa-info-circle')
                }else{
                    _that.menuChannel = response.data['channel'].menu_channel
                    _that.panelChannel = response.data['channel'].panel_channel
                    _that.user = response.data['idUser']
                    _that.pusher()
                    _that.getListShift()
                }

            })
            .catch(function (error) {
                console.log(error)
            })
        },
        
        pusher () {        
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true
    
            var _that = this
            var pusher = new Pusher('56423364aba2e84b5180', {
                cluster: 'us2'
            })
            var menuChannelPusher = pusher.subscribe(this.menuChannel)

            menuChannelPusher.bind('toPublicPanel', function(data) {
                if (data != null) {
                    _that.addShift(data)
                }
            })

            this.isActive = true
        },
        //revisar la llamada que hace
        addShift (dataChannel) {
            var _that = this
            var shift_id = dataChannel.idTicket
            var bk = false

            if (dataChannel.idUser == this.user) {
                            
                axios.post('get-shift-advisor', {
                    type: 2,
                    shiftId: shift_id
                })
                .then(function (response) {
                    if (response.data[0].status == 1) {
                        _that.shiftList.push (
                            response.data[0]
                        )
                    } else {
                        if (_that.shiftList.length == 0) {
                            _that.shiftList.push (
                                response.data[0]
                            )
                        } else {
                            _that.shiftList.forEach(function (shift, index){
                                if (shift.id > response.data[0].id && bk == false) {
                                    _that.shiftList.splice(index, 0, response.data[0])
                                    bk = true
                                }
                                console.log(shift.id+ "<=>"+ response.data[0].id)
                            });
                            
                        }

                    }
                })
                .catch(function (error) {
                    console.log(error)
                })
            }
        },

        nextShift () {
            var _that = this

            if (this.shiftList.length > 0) {
                // console.log('No vacia')
                this.attending.id = this.shiftList[0].id

                axios.post('next-shift', {
                    'shiftId': _that.attending.id,
                    'panel_channel': _that.panelChannel
                })
                .then(function (response) {

                    if (response.data.state == true) {
                        _that.attending.shift = _that.shiftList[0].shift
                        _that.attending.speciality = _that.shiftList[0].speciality
                        _that.attending.type = _that.shiftList[0].shift_type
                        _that.attending.time = _that.shiftList[0].time.substring(11, 19)
                        _that.attending.client = _that.shiftList[0].name_client
                        _that.attending.number = _that.shiftList[0].number_client
                        _that.attending.sex = _that.shiftList[0].sex_client
        
                        _that.shiftList.splice(0, 1)
                    }

                    _that.notify(response.data.type, response.data.text, response.data.icon)
                })
                .catch(function (error) {
                    console.log(error)
                })               

            } else {
                $.notify({
                    title: "  ",
                    message: " No hay turnos por el momento.",
                    icon: 'fa fa-info-circle' 
                },{
                    newest_on_top: true,
                    type: "info"
                })
                this.setNotShiftAttending()
            }
        },

        reassignmentShift () {
            var data_form = $('#form-advisor').serializeArray()
            var _that = this

            if (data_form[2].value != 0) {
                
                axios.post('shift/reassignment',{
                    shift_id: _that.attending.id,
                    send_id: data_form[1].value,
                    recive_id: data_form[2].value,
                    menu_channel: _that.menuChannel
                })
                .then(function (response) {
                    $('#reassignment-modal').modal('hide')
                    _that.notify(response.data.type, response.data.text, response.data.icon)
                })
                .catch(function (error) {
                    console.log(error)
                }) 
            } else {
                _that.notify('info', 'Debe elegir un nuevo asesor.', 'fa fa-info-circle')
            }
        },

        changeStatusShift (status) {
            var _that = this

            if (this.attending.id != 0) {

                axios.post('status-shift', {
                    shiftId: this.attending.id,
                    typeStatus: status
                })
                .then(function (response) {
                    _that.notify(response.data.type, response.data.text, response.data.icon)
                    setTimeout(function() {
                        _that.setNotShiftAttending()
                    }, 3000)

                })
                .catch(function (error) {
                    console.log(error)
                })

            } else {
                this.notify('warning', ' No tiene un turno en proceso', 'fa fa-exclamation-triangle')
            }
        },

        setNotShiftAttending () {
            this.attending.shift = '-'
            this.attending.speciality = '-'
            this.attending.type = '-'
            this.attending.time = '-'
            this.attending.client = '-'
            this.attending.number = '-'
            this.attending.sex = '-'
        },

        notify (type, message, icon) { 
            $.notify({
                title: "",
                message: message,
                icon: icon 
            },{
                newest_on_top: true,
                type: type,
                z_index: 1100,
            })
        }
    }
})
