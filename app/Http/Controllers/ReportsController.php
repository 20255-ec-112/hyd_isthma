<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelperFunctions;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\RegFamilyDetail;
use App\Models\RegPurchasesDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Http;

class ReportsController extends Controller
{
    public function familyDetailsReport(Request $request, DataTables $datatables)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View FamilyDetailsReport')){
            abort(403);
        }
        if($request->ajax())
        {
            $query = RegFamilyDetail::with('registration', 'registration.member')->where(function ($query) use ($request) {
                if (isset($request->gender))
                    $query->where('gender', $request->gender);
                if (isset($request->age_group))
                    $query->where('type', $request->age_group);
                if (isset($request->interested_in_volunteering)) {
                    $interested_in_volunteering = $request->interested_in_volunteering == 'null' ? null : $request->interested_in_volunteering;
                    $query->where('interested_in_volunteering', $interested_in_volunteering);
                }
            })->where(function ($query) use($request) {
                if(isset($request->zone_name)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('zone_name', $request->zone_name);
                    });
                }
                if(isset($request->division_name)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('division_name', $request->division_name);
                    });
                }
                if(isset($request->unit_name)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('unit_name', $request->unit_name);
                    });
                }
                $query->whereHas('registration.member', function ($q) use ($request) {
                    $q->filterByZone();
                });
            })->orderBy('id', 'asc');
            return $datatables->eloquent($query)
                    ->editColumn('interested_in_volunteering', function(RegFamilyDetail $familyDetail) {
                    return ($familyDetail->interested_in_volunteering == null ? '' : ($familyDetail->interested_in_volunteering == 'yes' ? AppHelperFunctions::getGreenBadge('Yes') : AppHelperFunctions::getRedBadge('No')));
                    })
                    ->rawColumns(['interested_in_volunteering'])->addIndexColumn()->make(true);
        }
        return view('admin.reports.family-details-report');
    }
    public function paymentDetailsReport(Request $request, DataTables $datatable)
    {
        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View PaymentDetailsReport')){
            abort(403);
        }
        if($request->ajax())
        {
            $query = Registration::with('member')->where(function ($query) use($request) {
                    if(isset($request->paid_status)) {
                        $condition = $request->paid_status == 'no' ? '=' : '!=';
                        $query->where('fees_paid_to_ameer', $condition, null);
                    }
             })->whereHas('member', function ($query) use ($request) {
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
            });
            return $datatable->eloquent($query)
                ->addColumn('member_fees', function (Registration $registration) {
                        $memberFee = $registration->member_fees ?? 0;
                        $familyFee = RegFamilyDetail::where('registration_id', $registration->id)->sum('fees');
                        return $memberFee + $familyFee;
                    })
                ->rawColumns(['member_fees'])
                ->with('sum_total_fees', $query->sum('member_fees'))->addIndexColumn()->make(true);
        }
        return view('admin.reports.payment-details-report');
    }
    public function arrivalReport(Request $request, DataTables $dataTables) {

        $user = User::find(auth()->user()->id);
        if (auth()->user()->id != 1 && !$user->hasPermissionTo('View TravelReport')){
            abort(403);
        }
        if($request->ajax()) {
            $query = Registration::with('member')->whereHas('member', function ($query) use ($request) {
                if (!empty($request->unit_name)) {
                    $query->where('unit_name', $request->unit_name);
                }
                if (!empty($request->zone_name)) {
                    $query->where('zone_name', $request->zone_name);
                }
                if (!empty($request->division_name)) {
                    $query->where('division_name', $request->division_name);
                }
                $query->filterByZone();
            })->select('registrations.*')->where([['confirm_arrival', '=', 1], ['arrival_details', '!=', '']])
                ->where(function ($query) use($request) {
                    if(!empty($request->date_time)){
                        $query->where('arrival_details->datetime', 'like', "%{$request->date_time}%");
                    }

                    if(!empty($request->travel_mode)) {
                        $query->where('arrival_details->mode', 'like', "%{$request->travel_mode}%");
                    }

                    if(!empty($request->end_point)){
                        $query->where('arrival_details->end_point', 'like', "%{$request->end_point}%");
                    }

                    if(!empty($request->mode_identifier)) {
                        $query->where('arrival_details->mode_identifier', 'like', "%{$request->mode_identifier}%");
                    }
                })->orderBy('id', 'asc');
            return $dataTables->eloquent($query)
                ->addColumn('travel_mode', function (Registration $registration) {
                    return $registration->arrival_details['mode'] ?? 'NA';
                })
                ->addColumn('date_time', function (Registration $registration) {
                    return $registration->arrival_details['datetime'] ?
                        '<span class="badge badge-success">' . date('Y-m-d', strtotime($registration->arrival_details['datetime'])) . '</span>' : 'NA';
                    })
                ->addColumn('end_point', function (Registration $registration) {
                    return $registration->arrival_details['end_point'] ?
                        $registration->arrival_details['end_point'] : 'NA';
                    })
                ->addColumn('mode_identifier', function (Registration $registration) {
                    return $registration->arrival_details['mode_identifier'] ?
                        $registration->arrival_details['mode_identifier'] : 'NA';
                    })
                ->addColumn('total_family_members', function (Registration $registration) {
                    return $registration->familyDetails->count();
                    })
                ->rawColumns(['travel_mode', 'date_time' , 'end_point', 'mode_identifier', 'total_family_members'])
                    ->addIndexColumn()->make(true);
        }
        return view('admin.reports.arrival-report');
    }
    public function departureReport(Request $request, DataTables $dataTables) {

        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View TravelReport')){
            abort(403);
        }

        if($request->ajax()) {
            $query = Registration::with('member')->whereHas('member', function ($query) use ($request) {
                if (!empty($request->unit_name)) {
                    $query->where('unit_name', $request->unit_name);
                }
                if (!empty($request->zone_name)) {
                    $query->where('zone_name', $request->zone_name);
                }
                if (!empty($request->division_name)) {
                    $query->where('division_name', $request->division_name);
                }
                $query->filterByZone();
            })->select('registrations.*')->where([['confirm_arrival', '=', 1], ['departure_details', '!=', '']])
                ->where(function ($query) use($request) {
                    if(!empty($request->date_time)){
                        $query->where('departure_details->datetime', 'like', "%{$request->date_time}%");
                    }

                    if(!empty($request->travel_mode)) {
                        $query->where('departure_details->mode', 'like', "%{$request->travel_mode}%");
                    }

                    if(!empty($request->start_point)){
                        $query->where('departure_details->start_point', 'like', "%{$request->start_point}%");
                    }

                    if(!empty($request->mode_identifier)) {
                        $query->where('departure_details->mode_identifier', 'like', "%{$request->mode_identifier}%");
                    }
                })->orderBy('id', 'asc');
            return $dataTables->eloquent($query)
                ->addColumn('travel_mode', function (Registration $registration) {
                    return $registration->departure_details['mode'] ?? 'NA';
                })
                ->addColumn('date_time', function (Registration $registration) {
                    return $registration->departure_details['datetime'] ?
                        '<span class="badge badge-success">' . date('Y-m-d', strtotime($registration->departure_details['datetime'])) . '</span>' : 'NA';
                    })
                ->addColumn('start_point', function (Registration $registration) {
                    return $registration->departure_details['start_point'] ?? 'NA';
                    })
                ->addColumn('mode_identifier', function (Registration $registration) {
                    return $registration->departure_details['mode_identifier'] ?
                        $registration->departure_details['mode_identifier'] : 'NA';
                    })
                ->addColumn('total_family_members', function (Registration $registration) {
                    return $registration->familyDetails->count();
                    })
                ->rawColumns(['travel_mode', 'date_time' , 'start_point', 'mode_identifier', 'total_family_members'])
                    ->addIndexColumn()->make(true);
        }
        return view('admin.reports.departure-report');
    }
    public function commonDataReport(Request $request, DataTables $dataTables) {

        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View CommonDataReport')){
            abort(403);
        }
        if($request->ajax()) {
            $query = Registration::with('member')->whereHas('member', function ($query) use ($request) {
                if (!empty($request->unit_name)) {
                    $query->where('unit_name', $request->unit_name);
                }
                if (!empty($request->zone_name)) {
                    $query->where('zone_name', $request->zone_name);
                }
                if (!empty($request->division_name)) {
                    $query->where('division_name', $request->division_name);
                }
                $query->filterByZone();
            })->select('registrations.*')->where([['confirm_arrival', '=', 1]])
                ->where(function ($query) use($request) {
                    if(!empty($request->hotel_required)){
                        $query->where('hotel_required', $request->hotel_required);
                    }
                    if(!empty($request->need_attendant)) {
                        $query->where('special_considerations->need_attendant', $request->need_attendant);
                    }
                    if(!empty($request->cot_or_bed)){
                        $query->where('special_considerations->cot_or_bed', $request->cot_or_bed);
                    }
                    if(!empty($request->health_concern)) {
                        $condition = $request->health_concern == 'yes' ? '!=' : '=';
                        $query->where('health_concern', $condition, null);
                    }
                    if(!empty($request->management_experience)) {
                        $query->where('management_experience', $request->management_experience);
                    }
                })->orderBy('id', 'asc');
            return $dataTables->eloquent($query)
                    ->addColumn('need_attendant', function(Registration $registration) {
                        return $registration->special_considerations['need_attendant'] ?? 'NA';
                    })
                    ->addColumn('cot_or_bed', function(Registration $registration) {
                        return $registration->special_considerations['cot_or_bed'] ?? 'NA';
                    })->rawColumns(['need_attendant', 'cot_or_bed'])->addIndexColumn()->make(true);
        }
        return view('admin.reports.common-data-report');
    }
    public function purchaseDataReport(Request $request, DataTables $datatables) {

        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View PurchaseDataReport')){
            abort(403);
        }
        if($request->ajax()) {
            $query = RegPurchasesDetail::with('registration', 'registration.member')->where('qty', '>', 0)->where(function ($query) use ($request) {
                if (isset($request->purchase_type))
                    $query->where('type', $request->purchase_type);
            })->where(function ($query) use($request) {
                if(isset($request->zone_name)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('zone_name', $request->zone_name);
                    });
                }
                if(isset($request->division_name)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('division_name', $request->division_name);
                    });
                }
                if(isset($request->unit_name)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('unit_name', $request->unit_name);
                    });
                }
                if(isset($request->gender)){
                    $query->whereHas('registration.member', function ($q) use ($request) {
                        $q->where('gender', $request->gender);
                    });
                }
                $query->whereHas('registration.member', function ($q) use ($request) {
                    $q->filterByZone();
                });
            })->orderBy('id', 'asc');
            return $datatables->eloquent($query)->addIndexColumn()->make(true);
        }
        return view('admin.reports.purchase-data-report');
    }
    public function sightSeeingDetailsReport(Request $request, DataTables $dataTables) {

        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View SightSeeingReport')){
            abort(403);
        }
        if($request->ajax()) {
            $query = Registration::with('member')->whereHas('member', function ($query) use ($request) {
                if (!empty($request->unit_name)) {
                    $query->where('unit_name', $request->unit_name);
                }
                if (!empty($request->zone_name)) {
                    $query->where('zone_name', $request->zone_name);
                }
                if (!empty($request->division_name)) {
                    $query->where('division_name', $request->division_name);
                }
                if (!empty($request->gender)) {
                    $query->where('gender', $request->gender);
                }
                $query->filterByZone();
            })->select('registrations.*')->where([['confirm_arrival', '=', 1], ['sight_seeing->required', 'yes']])
                ->orderBy('id', 'asc');
            return $dataTables->eloquent($query)
                ->addColumn('total_members', function (Registration $registration) {
                    return $registration->sight_seeing['members_count'] ?? 0;
                })
                ->rawColumns(['total_members'])
                ->addIndexColumn()->make(true);
        }
        return view('admin.reports.sight-seeing-details-report');
    }
    public function globalReport(Request $request, DataTables $dataTables) {

        $user = User::find(auth()->user()->id);
        if ($user->id != 1 && !$user->hasPermissionTo('View GlobalReport')){
            abort(403);
        }
        if($request->ajax()) {
            $zoneFilter = $request->zone_name;
            $divisionFilter = $request->division_name;

            $queryBy = 'zone_name';
            $queryByValue = '';
            $selector = 'zone_name';

            // Filter by unit if zone & division filter is not empty & unit filter is set
            if(!empty($zoneFilter) && empty($divisionFilter)) {
                $queryBy = 'zone_name';
                $queryByValue = $zoneFilter;
                $selector = 'division_name';
            }
            // Filter by division if zone filter is not empty & unit filter is empty and division filter is set
            if (!empty($zoneFilter) && !empty($divisionFilter)) {
                $queryBy = 'division_name';
                $queryByValue = $divisionFilter;
                $selector = 'unit_name';
            }

            $query = Member::select(DB::raw("$selector , count(*) as total_arkans"))
                            ->filterByRegionType($queryBy, $queryByValue, $zoneFilter)
                            ->groupBy($selector)
                            ->orderBy($selector, 'asc');

            $globalData = array_reduce($this->globalReportData($query, $selector, $zoneFilter), 'array_merge', []);

            return $dataTables->eloquent($query)
                ->addColumn('region_name', function (Member $member) use ($selector, $queryByValue) {
                    if (empty($queryByValue))
                        return $member->zone_name;

                    if (!empty($queryByValue) && $selector === 'division_name')
                        return $member->division_name;

                    if (!empty($queryByValue) && $selector === 'unit_name')
                        return $member->unit_name;
                    })
                    ->rawColumns(['region_name'])
                    ->with('global_data', $globalData)
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('admin.reports.global-report');
    }

    public function globalReportData($query, $selector, $zoneFilter = null) {
        $data = [];
        $distinctRegions = $query->get();
        $sum_total_attendees = 0;
        $sum_total_non_attendees = 0;
        $sum_total_registered = 0;
        // This is the data specific for each region type
        foreach ($distinctRegions as $region) {
            $totalArkans = $region->total_arkans;

            $totalAttendees = Registration::with('member')->whereHas('member', function ($query) use ($region, $selector, $zoneFilter) {
                $query->filterByRegionType($selector, $region->{$selector}, $zoneFilter);
            })->confirmArrival(1)->count();

            $totaNonAttendees = Registration::with('member')->whereHas('member', function ($query) use ($region, $selector, $zoneFilter) {
                $query->filterByRegionType($selector, $region->{$selector}, $zoneFilter);
            })->confirmArrival(0)->count();

            $totalRegistered = Registration::with('member')->whereHas('member', function ($query) use ($region, $selector) {
                        $query->where($selector, $region->{$selector});
                    })->count();

            $totalMembersCompletedFamilyDtls = Registration::with('member')->whereHas('member', function($query) use($region, $selector, $zoneFilter) {
                        $query->filterByRegionType($selector, $region->{$selector}, $zoneFilter);
                    })->where('member_fees', '!=', null)->count();

            $totalMembersPartialsHalfPayment = Registration::with('member')->whereHas('member', function($query) use($region, $selector, $zoneFilter) {
                       $query->filterByRegionType($selector, $region->{$selector}, $zoneFilter);
                    })->where('fees_paid_to_ameer', '!=', null)->where('fees_paid_to_ameer', '>', 0)->count();

            $totalCompletedLastStep = Member::where('year_of_rukniyat', '!=', null)->where(function($query) use($region, $selector, $zoneFilter) {
                       $query->filterByRegionType($selector, $region->{$selector}, $zoneFilter);
                    })->count();

            $sortedData = [
                $region->{$selector} => [
                    'registered' => $totalRegistered,
                    'total_attendees' => $totalAttendees,
                    'total_non_attendees' => $totaNonAttendees,
                    'tot_attendees_percentage' => floatval($totalArkans > 0 ? round(($totalAttendees / $totalArkans) * 100, 2) : 0),
                    'tot_registered_percentage' => floatval($totalArkans > 0 ? round(($totalRegistered / $totalArkans) * 100, 2) : 0),
                    "percentage_family_details_completed"  => floatval( $totalMembersCompletedFamilyDtls > 0 ? round(($totalMembersCompletedFamilyDtls / $totalRegistered) * 100, 2) : 0),
                    'percentage_full_half_payment_done' => floatval($totalMembersPartialsHalfPayment > 0 ? round(($totalMembersPartialsHalfPayment / $totalRegistered) * 100, 2) : 0),
                    "completed_last_step" => floatval($totalCompletedLastStep > 0 ? round($totalCompletedLastStep / $totalRegistered * 100,  2): 0),
                ],
            ];
            $sum_total_attendees += $totalAttendees;
            $sum_total_non_attendees += $totaNonAttendees;
            $sum_total_registered += $totalRegistered;
            array_push($data, $sortedData);
        }
        $footer_toals = [
            "footer_totals" => [
                'sum_total_attendees' => $sum_total_attendees,
                'sum_total_non_attendees' => $sum_total_non_attendees,
                'sum_total_registered' => $sum_total_registered,
            ]
            ];
        array_push($data, $footer_toals);
        return $data;
    }
    public function syncRukunData(Request $request) {
        $request = Http::get(env('JIH_MARJAZ_API_URL'));
        $response = $request->json();
        $data  = $response['data'];
        $active_rids = [];
        foreach($data as $item) {
            array_push($active_rids, (string) $item['userNumber']);
        }
        $existing_rids = Member::all()->pluck('user_number')->toArray();
        $inActiveList = array_diff($existing_rids, $active_rids);
        foreach($inActiveList as $item) {
            $member = Member::where('user_number', $item)->first();
            $member->status = 'InActive';
            $member->save();
        }
    }
}