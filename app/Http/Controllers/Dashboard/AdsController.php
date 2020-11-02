<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\UserOffice;
use App\Ad;

class AdsController extends Controller
{
    public function index(){
        $officeId = UserOffice::where('user_offices.user_id', Auth::id())->first();

        $objAds = Ad::where([
            ['office_id', $officeId->office_id],
            ['is_active', 1]
        ])
        ->orderBy('order', 'asc')
        ->get();

        return view('dashboard.contents.carousel.Index', ['ads' => $objAds]);
    }

    public function create(){
        return view('dashboard.contents.carousel.Create');
    }

    public function store(Request $request){
        // $request->validate([
        //     'txtName'           => 'required|string|max:255',
        //     'txtPhone'          => 'nullable|string|max:50',
        //     'txtAddress'        => 'required|string',
        //     'txtChannel'        => 'required|string|unique:offices,channel|max:80',
        //     'txtOfficeKey'      => 'required|string|unique:offices,office_key|max:20',
        //     'cmbMunicipality'   => 'required|integer|exists:municipalities,id'
        // ],[
        //     'txtChannel.unique'     => 'El canal ingresado ya pertenece a otra sucursal.',
        //     'txtOfficeKey.unique'   => 'Este código de sucursal no está disponible.'
        // ]);

        $file = $request->imgAd;

        $path = $file->store('carousel');

        $officeId = UserOffice::where('user_id', Auth::id())->select('office_id')->first();

        $objAd = new Ad();
        $objAd->name        = $request->txtName;
        $objAd->path        = $path;
        $objAd->order       = $request->intOrder;
        $objAd->is_first    = (($request->intOrder == 1)? "active":"");
        $objAd->duration    = ($request->intDuration*1000);
        $objAd->office_id   = $officeId->office_id;
        $objAd->is_active   = true;

        $objAd->save();

        return redirect()->route('ad.create');
    }

    public function delete($adId){

        $adDelete = Ad::find($adId);
        $adDelete->delete();

        return redirect()->route('ad.index');
    }
}
