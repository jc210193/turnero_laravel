<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OfficeController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Events\MenuGeneratorMsg;
use App\Events\AdminPanelMsg;
use App\SpecialityType;
use App\Incident;
use App\Client;
use App\Shift;

class ShiftController extends Controller
{
    public function generateTicketNumber($speciality){
        $numberTicket = "";
        //OBTENIENDO LA LETRA DE LA ESPECIALIDAD PARA GENERAR EL NÚMERO
        $objSpeciality = SpecialityType::where('id', $speciality)->select('name', 'id')->first();
        $letter = substr($objSpeciality->name, 0, -(strlen($objSpeciality->name)-1));

        $number = (Shift::where([
                                    ['office_id', Cookie::get('OFFICE')],
                                    ['created_at', 'like', OfficeController::setDate().'%']
                                ])->count())+1;

        switch (strlen($number)) {
            case 1:
                $numberTicket = $letter . "00" . $number;
                break;
            
            case 2:
                $numberTicket = $letter . "0" . $number;
                break;

            case 3:
                $numberTicket = $letter . $number;
                break;
                
            default:
                $numberTicket = $letter . $number . "--Error code--";
                break;
        }

        return $numberTicket;
    }

    public function create(Request $request){

        $specialityId = $request->input('speciality');
        $clientNumber = $request->input('client_number');
        $clientSex = $request->input('sex');
        $channel = $request->input('channel');
        $typeTicket = 1;

        if ($clientNumber != null) {
            $dataClient = Client::where([
                                        ['client_number', $clientNumber],
                                        ['is_active', 1]
                                ])->first();
            
            $clientSex = $dataClient->sex;
            $typeTicket = 2;
        }

        $newTicket = new Shift();

        $newTicket->shift               = ShiftController::generateTicketNumber($specialityId);
        $newTicket->shift_type_id       = $typeTicket;
        $newTicket->speciality_type_id  = $specialityId;
        $newTicket->office_id           = Cookie::get('OFFICE');
        $newTicket->shift_status_id     = 1;
        $newTicket->user_advisor_id     = \App\Http\Controllers\AdvisorController::selectAdvisor($specialityId);
        $newTicket->sex_client          = $clientSex;
        $newTicket->number_client       = $clientNumber;
        $newTicket->has_incident        = 0;
        $newTicket->is_active           = 1;
        $newTicket->save();

        event(new MenuGeneratorMsg($channel, $newTicket->id, $newTicket->user_advisor_id));

        $ticket = Shift::join('shift_types', 'shifts.shift_type_id', '=', 'shift_types.id')
                        ->join('user_offices', 'shifts.user_advisor_id', '=', 'user_offices.user_id')
                        ->join('boxes', 'user_offices.box_id', '=', 'boxes.id')
                        ->select(
                            'shifts.shift',
                            'boxes.box_name as box',
                            'shifts.created_at as hora'
                        )
                        ->where('shifts.id', $newTicket->id)
                        ->first();

        return ['ticket' => $ticket];
    }

    public function nextShift(Request $request){
        $return = null;
        $shiftId = $request->input('shiftId');
        $panelChannel = $request->input('panel_channel');

        $newStateShift = Shift::where('id', $shiftId)->first();
        $newStateShift->start_shift = now();
        $newStateShift->save();

        
        if ($newStateShift->start_shift != null) {
            event(new AdminPanelMsg($panelChannel, $shiftId));
            $return = [
                        'state' => true,
                        'text' => 'Turno iniciado - '.substr($newStateShift->start_shift, 11, 19),
                        'type' => 'info',
                        'icon' => 'fa fa-info-circle'
                    ];
        } else {
            $return = [
                        'state' => false,
                        'text' => 'No se pudo iniciar el turno. Recargue la pagina',
                        'type' => 'danger',
                        'icon' => 'fa fa-times-circle'
                    ];
        }


        return $return;
    }

    public function reassignmentShift(Request $request  ){

        $shiftId    = $request->input('shift_id');
        $reciveId   = $request->input('recive_id');
        $sendId     = $request->input('send_id');
        $channel    = $request->input('menu_channel');

        // CAMBIO DE USUARIO
        $reassignment = Shift::where('id', $shiftId)->first();
        $reassignment->shift_status_id = 2;
        $reassignment->user_advisor_id = $reciveId;
        $reassignment->has_incident = 1;
        $reassignment->save();

        // REGISTRO DEL INCIDENTE CON EL USUARIO QUE ESTA HACIENDO LA REASIGNACIÓN
        $objIncident = new Incident();
        $objIncident->shift_id = $shiftId;
        $objIncident->incident_type_id =  1;
        $objIncident->user_reassigned_id = $sendId;        
        $objIncident->is_active = 1;   
        $objIncident->save();

        event(new MenuGeneratorMsg($channel, $objIncident->shift_id, $reassignment->user_advisor_id));
        
        $return = [
            'state' => true,
            'text' => 'EL turno <b>'.$reassignment->shift.'</b> ha sido reasignado',
            'type' => 'success',
            'icon' => 'fa fa-exchange-alt'
        ];

        return $return;
    }

    public function changeStatusShift(Request $request){

        $shiftId = $request->input('shiftId');
        $statusId = $request->input('typeStatus');
        $shiftText = '<b>Error.</b> No se pudo realizar la acción';
        $shiftIcon = 'fa fa-times-circle';
        $shiftType = 'warning';

        $status = Shift::where('id', $shiftId)->first();
        
        if ($status->count() > 0) {
            
            $status->shift_status_id = $statusId;
            $status->end_shift = now();
            $status->save();

            if ($statusId == 3) {
                $shiftText = "Turno <b>finalizado</b>";
                $shiftIcon = 'fa fa-exclamation-triangle';
            } elseif ($statusId == 4) {
                $shiftText = "Turno <b>abandonado</b>";
                $shiftIcon = "fas fa-walking";
            }
        }

        return [
            'text' => $shiftText,
            'type' => $shiftType,
            'icon' => $shiftIcon
        ];
    }
}
