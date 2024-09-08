<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\RegFamilyDetail;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RegistrationController extends Controller
{
    public $stationsList;
    public function __construct() {
       $this->stationsList = [
            'Mg Bus Station, Imlibun, Gowliguda, Hyderabad',
            'Jubilee Bus Station, Gandhi Nagar',
            'L. B. Nagar, Hyderabad',
            'Aramghar Bus Stop',
            'Secunderabad Railway Station',
            'Kacheguda Railway Station',
            'Lingampally Railway Station',
            'Hyderabad Deccan (Nampally) Railway Station',
            'Rajiv Gandhi International Airport, Shamshabad',
            'Wadi e Huda',
            'Other'
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $dataTables)
    {
        $user = User::find(auth()->user()->id);
        if ( !($user->hasPermissionTo('View Registrations')) && auth()->user()->id != 1){
                abort(403);
        }

        if ($request->ajax()) {
            $query = Registration::with('member')->whereHas('member', function ($query) use ($request) {
                if (isset($request->unit_name)) {
                    $query->where('unit_name', $request->unit_name);
                }
                if (isset($request->zone_name)) {
                    $query->where('zone_name', $request->zone_name);
                }
                if (isset($request->division_name)) {
                    $query->where('division_name', $request->division_name);
                }
                $query->filterByZone();
            })->select('registrations.*')->where(function ($query) use ($request) {
                if (isset($request->confirm_arrival)) {
                    $query->where('confirm_arrival', $request->confirm_arrival);
                }
            })->orderBy('id', 'asc');
            return $dataTables->eloquent($query)
                ->editColumn('confirm_arrival', function (Registration $registration) {
                    return $registration->confirm_arrival ? '<span class="badge badge-success">Confirmed</span>' : '<span class="badge badge-danger">NA</span>';
                })
                ->editColumn('ameer_permission_taken', function (Registration $registration) {
                    return $registration->ameer_permission_taken ? '<span class="badge badge-success">Yes</span>' : ($registration->ameer_permission_taken === 0 ? '<span class="badge badge-danger">NO</span>' : '-');
                })
                ->addColumn('action', function (Registration $registration) {
                    return   '<a href="' . route('registrations.show', $registration->id) . '" class="badge badge-primary" title="View"><i class="fas fa-eye" ></i></a>'.
                              '<a href="' . route('registrations.edit', $registration->id) . '" class="badge badge-warning ml-2" title="View"><i class="fas fa-edit" ></i></a>';
                })->rawColumns(['confirm_arrival', 'ameer_permission_taken', 'action'])->addIndexColumn()->make(true);
        }

        return view('admin.registrations.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // $user = User::find(auth()->user()->id);
        // if ( !($user->hasPermissionTo('View Registrations')) && auth()->user()->id != 1){
        //         abort(403);
        // }
        return view('admin.registrations.form')->with([
            'reg' => new Registration(),
            'members' => Member::doesntHave('registration')->get(),
            'stationslist' => $this->stationsList,
            'mehramDetails' => [],
            'childrenDetails' => [],
            'purchaseDetails' => []
        ]);
    }

    public function prepareRegData(Request $request) {
        $regData = [];
        $regData['member_id'] = $request->get('member_id');
        $regData['confirm_arrival'] = $request->get('confirm_arrival');
        $regData['reason_for_not_coming'] = $request->get('reason_for_not_coming');
        $regData['ameer_permission_taken'] = $request->get('ameer_permission_taken');
        $regData['emergency_contact'] = $request->get('emergency_contact');
        $regData['arrival_details'] = [
            'datetime' => $request->get('arrival_dtls_datetime'),
            'mode' => $request->get('arrival_dtls_travel_mode'),
            'mode_identifier' => $request->get('arrival_dtls_mode_identifier'),
            'start_point' => $request->get('arrival_dtls_start_point'),
            'end_point' => $request->get('arrival_dtls_end_point')
        ];
        $regData['departure_details'] = [
            'datetime' => $request->get('departure_dtls_datetime'),
            'mode' => $request->get('departure_dtls_travel_mode'),
            'mode_identifier' => $request->get('departure_dtls_mode_identifier'),
            'start_point' => $request->get('departure_dtls_start_point'),
            'end_point' => $request->get('departure_dtls_end_point')
        ];
        $regData['hotel_required'] = $request->get('hotel_required');
        $regData['special_considerations'] = [
            'food_preferences' => $request->get('food_preferences'),
            'need_attendant' => $request->get('need_attendant'),
            'cot_or_bed' => $request->get('cot_or_bed')
        ];
        $regData['sight_seeing'] = [
            'required' => $request->get('sightseeing_required'),
            'members_count' => $request->get('members_count')
        ];
        $regData['health_concern'] = $request->get('health_concern');
        $regData['management_experience'] = $request->get('health_concern');
        $regData['comments'] = $request->get('comments');
        $memberState = Member::find($request->get('member_id'))->pluck('zone_name')->first();
        $regData['member_fees'] = in_array($memberState, config('fees.special_states')) ? 3000 : 2000;

        return $regData;
    }

    public function regFormValidations(Request $request) {
        $validations = [
            'member_id' => 'required',
            'confirm_arrival' => 'required',
        ];
        $confirm_arraival = $request->get('confirm_arrival');
        if( $confirm_arraival == 0) {
            $validations['reason_for_not_coming'] = 'required';
            $validations['ameer_permission_taken'] = 'required';
        }

        return $validations;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->regFormValidations($request));
        $regData = $this->prepareRegData($request);

        // Purchases Details
        $regis = Registration::create($regData);
        $purchasesData['Mattress'] = $request->get('purchase_details_matress');
        $purchasesData['Cot'] = $request->get('purchase_details_cot');
        $purchasesData['Plate'] = $request->get('purchase_details_plate');
        $purchasesData['Spoons'] = $request->get('purchase_details_spoons');
        $purchasesData['Carpet'] = $request->get('purchase_details_carpet');
        foreach ($purchasesData as $key => $value) {
            $data = [
                'registration_id' => $regis->id,
                'type' => $key,
                'qty' => $value,
            ];
            $regis->purchaseDetails()->create($data);
        }
        $is_family_dtls_exists = $request->get('is_family_dtls_exists');
        // Family Details
        if($is_family_dtls_exists == 'yes') {
            $adults = $request->get('adults_dtls');
            if(isset($adults) && !empty($adults['name'])) {
                foreach ($adults['name'] as $key => $value) {
                    $data = [
                        'registration_id' => $regis->id,
                        'name' => $value,
                        'gender' => $adults['gender'][$key],
                        'age' => $adults['age'][$key],
                        'type' => 'mehram',
                        'fees' => 600
                    ];
                    $regis->familyDetails()->create($data);
                }
            }
            $childrens = $request->get('childrens_dtls');
            if(isset($childrens) && !empty($childrens['name'])) {
                foreach ($childrens['name'] as $key => $value) {
                    $data = [
                        'registration_id' => $regis->id,
                        'name' => $value,
                        'gender' => $childrens['gender'][$key],
                        'age' => $childrens['age'][$key],
                        'type' => 'children',
                        'fees' => $childrens['age'][$key] < 7 ? 0 : 300
                    ];
                    $regis->familyDetails()->create($data);
                }
            }
        }
        return redirect()->route('registrations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // if ( auth()->user()->id != 1 && !auth()->user()->hasPermissionTo('View Registrations')){
        //     abort(403);
        // }

        $member = Registration::with('member')->where('id', $id)->get()->first();
        $registration = Registration::with(['familyDetails', 'purchaseDetails'])->find($id);
        $purchaseDetails =   $registration->purchaseDetails;

        $mehramDetails = $registration->familyDetails->where('type', 'mehram');
        $childrenDetails = $registration->familyDetails->where('type', 'children');
        $data = [
            'member' => $member,
            'mehramDetails' => $mehramDetails,
            'childrenDetails' => $childrenDetails,
            'purchaseDetails' => $purchaseDetails
        ];
        return view('admin.registrations.show', $data);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        // $user = User::find(auth()->user()->id);
        // if ( !($user->hasPermissionTo('View Registrations')) && auth()->user()->id != 1){
        //         abort(403);
        // }
        $registration = Registration::with(['familyDetails', 'purchaseDetails'])->find($id);
        $purchaseDetails =   $registration->purchaseDetails->keyBy('type')->pluck('qty', 'type')->toArray();
        $mehramDetails = $registration->familyDetails->where('type', 'mehram');
        $childrenDetails = $registration->familyDetails->where('type', 'children');
        return view('admin.registrations.form')->with([
            'reg' => $registration,
            'members' => Member::all(),
            'stationslist' => $this->stationsList,
            'mehramDetails' => $mehramDetails,
            'childrenDetails' => $childrenDetails,
            'purchaseDetails' => $purchaseDetails
        ]);

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate($this->regFormValidations($request));
        $regData = $this->prepareRegData($request);

        // Purchases Details
        $regis = Registration::find($id);
        $regis->update($regData);

        $purchasesData['Mattress'] = $request->get('purchase_details_matress');
        $purchasesData['Cot'] = $request->get('purchase_details_cot');
        $purchasesData['Plate'] = $request->get('purchase_details_plate');
        $purchasesData['Spoons'] = $request->get('purchase_details_spoons');
        $purchasesData['Carpet'] = $request->get('purchase_details_carpet');
        foreach ($purchasesData as $key => $value) {
            $data = [
                'qty' => $value,
            ];
            $regis->purchaseDetails()->updateOrCreate(['type' => $key, 'registration_id' => $id], $data);
        }
        // Family Details
        $is_family_dtls_exists = $request->get('is_family_dtls_exists');
        if ($is_family_dtls_exists) {
                $adults = $request->get('adults_dtls');
            if(!empty($adults) && isset($adults['id'])) {
                $ids = array_values($adults['id']);
                RegFamilyDetail::where('registration_id', $id)->whereNotIn('id', $ids)->where('type', 'mehram')->delete();
            }
            if(isset($adults) && !empty($adults['name'])) {
                foreach ($adults['name'] as $key => $value) {
                    $data = [
                        'gender' => $adults['gender'][$key],
                        'age' => $adults['age'][$key],
                        'fees' => 600
                    ];
                    $regis->familyDetails()->updateOrCreate(['type' => 'mehram', 'name' => $value, 'registration_id' => $id], $data);
                }
            }
            $childrens = $request->get('childrens_dtls');
            if(!empty($childrens) && isset($childrens['id'])) {
                $ids = array_values($childrens['id']);
                RegFamilyDetail::where('registration_id', $id)->whereNotIn('id', $ids)->where('type', 'children')->delete();
            }
            if(isset($childrens) && !empty($childrens['name'])) {
                foreach ($childrens['name'] as $key => $value) {
                    $data = [
                        'registration_id' => $id,
                        'name' => $value,
                        'gender' => $childrens['gender'][$key],
                        'age' => $childrens['age'][$key],
                        'type' => 'children',
                        'fees' => $childrens['age'][$key] < 7 ? 0 : 300
                    ];
                    $regis->familyDetails()->updateOrCreate(['type' => 'children', 'name' => $value, 'registration_id' => $id], $data);
                }
            }
        } else {
            RegFamilyDetail::where('registration_id', $id)->delete();
        }

        return redirect()->route('registrations.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
