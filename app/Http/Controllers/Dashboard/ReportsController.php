<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\SpecialityType;
use App\UserOffice;
use App\ShiftStatus;
use App\Office;
use App\Shift;
use PDF;

class ReportsController extends Controller
{
    public function index(){
        return view('dashboard.contents.reports.Index');
    }

    public function getUsers(){
        $officeId = 

        // BUSCAMOS LOS USARIOS DE SUCURSAL
        $objUserOffices = UserOffice::select('user_id', 'office_id')
                                        ->where([
                                            ['office_id', $officeId],
                                            ['is_active', 1]
                                        ])
                                        ->get();

        // BUSCAMOS LAS ESPECIALIDADES DE CADA USUARIO 
        foreach ($objUserOffices as $user) {
            $duplicateSpeciality = false;

            // UN USUARIO PUEDE TENER MAS DE UNA ESPECIALIDAD
            $objSpecialityUser = SpecialityTypeUser::join('speciality_types', 'speciality_type_users.speciality_type_id', '=', 'speciality_types.id')
                                                ->where('speciality_type_users.user_id', $user->user_id)
                                                ->select(
                                                    'speciality_types.id AS speciality_id',
                                                    'speciality_types.name AS speciality_name',
                                                    'speciality_types.class_icon'
                                                )
                                                ->get();

            // SI EL SUSUARIO TIENE MAS DE UNA ESPECIALIDAD SE DEBE BUSCAR QUE NO SE DUPLIQUEN EN LA 
            // REPRESENTACION DE LA PANTALLA
            foreach ($objSpecialityUser as $specialityUser) {
                foreach ($arrSpecialities as $speciality) {
                    if ($specialityUser->speciality_id == $speciality['id']) {
                        $duplicateSpeciality = true;
                    }
                }

                // SOLO SE INSERTA EN EL ARRAY SU NO ESTA DUPLICADO EL VALOR
                if (!$duplicateSpeciality) {
                    array_push($arrSpecialities, array(
                        'id' => $specialityUser->speciality_id,
                        'speciality' => $specialityUser->speciality_name,
                        'class_btn' => $specialityUser->class_icon
                    ));
                } else {
                    $duplicateSpeciality = false;
                }
            }            
        }
    }

    public function generalReport(){
        $return = null;
        $arrShift = array();
        $arrSpeciality = array();
        $arrUserOffices = array();


       $objOffice = Office::join('user_offices', 'offices.id', 'user_offices.office_id')
                            ->where('user_offices.user_id', Auth::id())
                            ->select(
                                'offices.id',
                                'offices.name',
                                'offices.address',
                                'offices.phone',
                            )
                            ->first();

        $officeId = $objOffice->id;
        $today = \App\Http\Controllers\OfficeController::setDate();

        // PRIMERA TABLA
        $objStatus = ShiftStatus::all();
        foreach ($objStatus as $status) {
            $countShitf = Shift::where([
                                    ['shifts.office_id', $officeId],
                                    ['shifts.is_active', 1],
                                    ['shifts.shift_status_id', $status->id],
                                    ['shifts.created_at', 'like', $today."%"]
                                ])
                                ->count();

            array_push($arrShift, array(
                'type' => $status->shift_status,
                'count' => $countShitf,
            ));
        }

        // TABLA POR ESPECIALIDADES
        $objSpecialities = SpecialityType::all();
        $statusId = [3,4];
        foreach ($objSpecialities as $speciality) {
            $arrStatus = array();
            $turn = 0;
            foreach ($statusId as $index => $id) {
                $turn++;            
                $countStatus = collect(DB::select(DB::raw('SELECT
                                                                COUNT(shifts.shift_status_id) as quantity,
                                                                shift_status.shift_status,
                                                                shift_status.id
                                                            FROM shifts
                                                            JOIN shift_status ON shifts.shift_status_id = shift_status.id
                                                            WHERE shifts.shift_status_id = '.$id.'
                                                                AND shifts.created_at like "'.$today.'%"
                                                                AND shifts.office_id = '.$officeId.'
                                                                AND shifts.is_active = 1
                                                                AND shifts.speciality_type_id = '.$speciality->id)));
                array_push($arrStatus, array(
                    'id'        => $countStatus[0]->id,
                    'type'      => $countStatus[0]->shift_status,
                    'quantity'  => $countStatus[0]->quantity,
                ));
                if (sizeof($statusId) == $turn) {
                    $total = $arrStatus[0]['quantity'] + $arrStatus[1]['quantity'];
                    array_push($arrStatus, array(
                        'id'        => 0,
                        'type'      => 'Total',
                        'quantity'  => $total,
                    ));
                }
            }

            array_push($arrSpeciality, array(
                'type' => $speciality->name,
                'shifts' => $arrStatus
                
            ));
        }

        // TABLA POR ASESORES
        $objUserOffices = UserOffice::join('users', 'user_offices.user_id', 'users.id')
                                        ->join('boxes', 'user_offices.box_id', '=', 'boxes.id')
                                        ->where([
                                            ['user_offices.office_id', $officeId],
                                            ['users.user_type_id', 2],
                                            ['users.is_active', 1]
                                        ])
                                        ->select(
                                            'user_offices.office_id',
                                            'user_offices.user_id',
                                            'users.name',
                                            'users.first_name',
                                            'users.second_name',
                                            'users.email',
                                            'boxes.box_name'
                                        )
                                        ->get();


                                        // return $objUserOffices;

        foreach ($objUserOffices as $index => $userOffice) {
            $shiftsUserOffice = Shift::where([
                                            ['shifts.user_advisor_id', $objUserOffices[$index]->user_id],
                                            ['shifts.is_active', 1],
                                            ['shifts.created_at', 'like', $today."%"]

                                        ])
                                        ->count();

            array_push($arrUserOffices, array(
                'user'      => $userOffice->name." ".$userOffice->first_name." ".$userOffice->second_name,
                'mail'      => $userOffice->email,
                'box'       => $userOffice->box_name,
                'quantity'  => $shiftsUserOffice
            ));            
        }

        $pdf = PDF::loadView('dashboard.contents.reports.pdf.generalReport', [
                                                                                'office'        => $objOffice,
                                                                                'shifts'        => $arrShift,
                                                                                'specialities'  => $arrSpeciality,
                                                                                'advisors'      => $arrUserOffices
                                                                            ]);
        $pdf->setPaper('A4');
        $return = $pdf->stream();


        return $return;
    }
}
