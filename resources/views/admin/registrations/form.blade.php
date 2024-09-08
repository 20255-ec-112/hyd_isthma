@extends('layouts.app', ['ptype' => 'child', 'purl' => request()->route()->getName(), 'id' => $reg->id ?? '', 'ptitle' => 'Registration', 'ctitle' => $reg->id ? 'Edit' : 'Add'])
@section('pagetitle', 'Dashboard')
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container  mt-3 px-3  rounded-2">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $reg->id ? 'Edit' : 'Add' }} Registration
                    </h3>
                </div>
                <form autocapitalize="off"
                    action="{{ $reg->id ? route('registrations.update', $reg->id) : route('registrations.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    {{ $reg->id ? method_field('PUT') : '' }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-stepper linear reg_form">
                                <div class="bs-stepper-header" role="tablist">
                                    <div class="step active" data-target="#registration-part">
                                        <button type="button" class="step-trigger" role="tab"
                                            aria-controls="registration-part" id="registration-part-trigger"
                                            aria-selected="true">
                                            <span class="bs-stepper-circle">1</span>
                                            <span class="bs-stepper-label">Registration</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#purchases-required">
                                        <button type="button" class="step-trigger" role="tab"
                                            aria-controls="purchases-required" id="purchases-required-trigger"
                                            aria-selected="false" disabled="disabled">
                                            <span class="bs-stepper-circle">2</span>
                                            <span class="bs-stepper-label">Purchases Required</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#family-details">
                                        <button type="button" class="step-trigger" role="tab"
                                            aria-controls="family-details" id="family-details-trigger" aria-selected="false"
                                            disabled="disabled">
                                            <span class="bs-stepper-circle">3</span>
                                            <span class="bs-stepper-label">Family Details</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#more-information">
                                        <button type="button" class="step-trigger" role="tab"
                                            aria-controls="more-information" id="more-information-trigger"
                                            aria-selected="false" disabled="disabled">
                                            <span class="bs-stepper-circle">4</span>
                                            <span class="bs-stepper-label">More Information</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="bs-stepper-content">
                                    <div id="registration-part" class="content active dstepper-block" role="tabpanel"
                                        aria-labelledby="registration-part-trigger">
                                        <div class="form-group">
                                            {{-- Member ID --}}
                                            <label for="member_id">Member</label>
                                            <select class="form-control select2bs4" name="member_id" id="member_id">
                                                <option value="">Select Member</option>
                                                @foreach ($members as $member)
                                                    <option value="{{ $member->id }}"
                                                        {{ $reg->member_id == $member->id ? 'selected' : '' }}>
                                                        {{ $member->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('member_id'))
                                                <span class="text-danger">
                                                    {{ $errors->first('member_id') }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Confirm Arrival --}}
                                        <div class="form-group">
                                            <label for="confirm_arrival">Confirm Arrival</label>
                                            <div class="row">
                                                <div class="col-lg-1">
                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio"
                                                            id="confirm_arrival_yes" name="confirm_arrival" value="1"
                                                            {{ $reg->confirm_arrival == 1 || $reg->confirm_arrival == null ? 'checked' : '' }}>
                                                        <label for="confirm_arrival_yes"
                                                            class="custom-control-label">Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input custom-control-input-danger"
                                                            type="radio" id="confirm_arrival_no" name="confirm_arrival"
                                                            value="0"
                                                            {{ $reg->confirm_arrival == 0 ? 'checked' : '' }}>
                                                        <label for="confirm_arrival_no"
                                                            class="custom-control-label">No</label>
                                                    </div>
                                                </div>
                                                @if ($errors->has('confirm_arrival'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('confirm_arrival') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- Reason for not coming --}}
                                        <div class="form-group reason_for_not_coming_input">
                                            <label for="reason_for_not_coming">Reason for not coming</label>
                                            <input type="text" class="form-control" id="reason_for_not_coming"
                                                name="reason_for_not_coming" placeholder="Reason for not coming"
                                                value="{{ old('reason_for_not_coming', $reg->reason_for_not_coming) }}">
                                            @if ($errors->has('reason_for_not_coming'))
                                                <span class="text-danger">
                                                    {{ $errors->first('reason_for_not_coming') }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Ameer Permission Taken --}}
                                        <div class="form-group  ameer_permission_taken_input">
                                            <label for="ameer_permission_taken">Ameer Permisison Token</label>
                                            <select class="form-control" name="ameer_permission_taken">
                                                <option value="">Select Option</option>
                                                <option {{ $reg->ameer_permission_taken == 1 ? 'selected' : '' }}
                                                    {{ old('ameer_permission_taken') == 1 ? 'selected' : '' }}
                                                    value="1">Yes</option>
                                                <option {{ $reg->ameer_permission_taken == 0 ? 'selected' : '' }}
                                                    {{ old('ameer_permission_taken') == 0 ? 'selected' : '' }}
                                                    value="0">No</option>
                                            </select>
                                            @if ($errors->has('ameer_permission_taken'))
                                                <span class="text-danger">
                                                    {{ $errors->first('ameer_permission_taken') }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Emergency Contact --}}
                                        <div class="form-group">
                                            <label for="emergency_contact">Emergency Contact</label>
                                            <input type="text" class="form-control" id="emergency_contact"
                                                placeholder="Enter Emergency Contact Number" name="emergency_contact"
                                                value="{{ old('emergency_contact', $reg->emergency_contact) }}">
                                            @if ($errors->has('emergency_contact'))
                                                <span class="text-danger">
                                                    {{ $errors->first('emergency_contact') }}
                                                </span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper.next()">Next</button>
                                    </div>
                                    <div id="purchases-required" class="content" role="tabpanel"
                                        aria-labelledby="purchases-required-trigger">
                                        {{-- Purchases Details --}}
                                        <div class="form-group">
                                            <label>Purchases Details</label>
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label for="purchase_details_matress">Matress</label>
                                                    <input type="text" class="form-control"
                                                        id="purchase_details_matress" name="purchase_details_matress"
                                                        placeholder="Matress"
                                                        value="{{ old('purchase_details_matress', !empty($purchaseDetails) ? $purchaseDetails['Mattress'] : 0) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label for="purchase_details_cot">Cot</label>
                                                    <input type="text" class="form-control" id="purchase_details_cot"
                                                        name="purchase_details_cot" placeholder="Matress"
                                                        value="{{ old('purchase_details_cot', !empty($purchaseDetails) ? $purchaseDetails['Cot'] : 0) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label for="purchase_details_plate">Plate</label>
                                                    <input type="text" class="form-control"
                                                        id="purchase_details_plate" name="purchase_details_plate"
                                                        placeholder="Plates"
                                                        value="{{ old('purchase_details_plate', !empty($purchaseDetails) ? $purchaseDetails['Plate'] : 0) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label for="purchase_details_spoons">Spoons</label>
                                                    <input type="text" class="form-control"
                                                        id="purchase_details_spoons" name="purchase_details_spoons"
                                                        placeholder="Spoons"
                                                        value="{{ old('purchase_details_spoons', !empty($purchaseDetails) ? $purchaseDetails['Spoons'] : 0) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label for="purchase_details_carpet">Carpet</label>
                                                    <input type="text" class="form-control"
                                                        id="purchase_details_carpet" name="purchase_details_carpet"
                                                        placeholder="Carpet"
                                                        value="{{ old('purchase_details_carpet', !empty($purchaseDetails) ? $purchaseDetails['Carpet'] : 0) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper.previous()">Previous</button>
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper.next()">Next</button>
                                    </div>
                                    <div id="family-details" class="content" role="tabpanel"
                                        aria-labelledby="family-details-trigger">
                                        <div class="form-group mb-5">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Is anyone in your family acompanying you?</label>
                                                    <select class="form-control" id="is_family_dtls_exists"
                                                        name="is_family_dtls_exists">
                                                        <option value="">Select Option</option>
                                                        <option
                                                            {{ count($mehramDetails) > 0 || count($childrenDetails) > 0 ? 'selected' : '' }}
                                                            value="yes">Yes</option>
                                                        <option
                                                            {{ count($mehramDetails) == 0 && count($childrenDetails) == 0 ? 'selected' : '' }}
                                                            value="no">No</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 family_dtls_tree mt-3">
                                                    <h5 class="font-weight-bold">Adults Details <span
                                                            class="ml-2 cursor-pointer" onclick="addNewRow('adults')"><i
                                                                class="fas fa-plus text-primary"></i></span>
                                                    </h5>
                                                    <div class="adults_members">
                                                        @if (!empty($mehramDetails))
                                                            @foreach ($mehramDetails as $key => $adult)
                                                                <div class="row mb-2"
                                                                    data-adults-id="{{ $key + 1 }}">
                                                                    <input type="hidden" name="adults_dtls[id][]"
                                                                        value="{{ $adult->id }}">
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="adults_dtls[name][]"
                                                                            value="{{ $adult->name }}"
                                                                            placeholder="Enter Adult name">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="adults_dtls[age][]"
                                                                            value="{{ $adult->age }}"
                                                                            placeholder="Enter Adult age">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <select class="form-control"
                                                                            name="adults_dtls[gender][]">
                                                                            <option
                                                                                {{ $adult->gender == 'Male' ? 'selected' : '' }}
                                                                                value="Male">Male</option>
                                                                            <option
                                                                                {{ $adult->gender == 'Female' ? 'selected' : '' }}
                                                                                value="Femae">Female</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <button type="button" class="btn btn-danger"
                                                                            data-adults-id-remove="{{ $key + 1 }}"
                                                                            onclick="removeRow(event, 'adults')">Remove</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <h5 class="font-weight-bold mt-3">Childrens Details <span
                                                            class="ml-2 cursor-pointer"
                                                            onclick="addNewRow('childrens')"><i
                                                                class="fas fa-plus text-primary"></i></span>
                                                    </h5>
                                                    <div class="childrens_members mb-3">
                                                        @if (!empty($childrenDetails))
                                                            @foreach ($childrenDetails as $key => $children)
                                                                <div class="row mb-2"
                                                                    data-childrens-id="{{ $key + 1 }}">
                                                                    <input type="hidden" name="childrens_dtls[id][]"
                                                                        value="{{ $children->id }}">
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="childrens_dtls[name][]"
                                                                            value="{{ $children->name }}"
                                                                            placeholder="Enter Children name">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control"
                                                                            name="childrens_dtls[age][]"
                                                                            value="{{ $children->age }}"
                                                                            placeholder="Enter Children age">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <select class="form-control"
                                                                            name="childrens_dtls[gender][]">
                                                                            <option
                                                                                {{ $children->gender == 'Male' ? 'selected' : '' }}
                                                                                value="Male">Male</option>
                                                                            <option
                                                                                {{ $children->gender == 'Female' ? 'selected' : '' }}
                                                                                value="Femae">Female</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <button type="button" class="btn btn-danger"
                                                                            data-childrens-id-remove="{{ $key + 1 }}"
                                                                            onclick="removeRow(event, 'childrens')">Remove</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper.previous()">Previous</button>
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper.next()">Next</button>
                                    </div>
                                    <div id="more-information" class="content" role="tabpanel"
                                        aria-labelledby="more-information-trigger">
                                        {{-- Arrival Details --}}
                                        <h5 class="font-weight-bold">Arrival Details</h5>
                                        <div class="pl-3">
                                            <div class="row">
                                                {{-- date & time --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bio">Date & Time</label>
                                                        <input type="text"
                                                            class="form-control datetimepicker-input datetime"
                                                            data-toggle="datetimepicker" name="arrival_dtls_datetime"
                                                            data-target="#datetime"
                                                            value="{{ old('arrival_dtls_datetime', $reg->arrival_details ? $reg->arrival_details['datetime'] : '') }}" />
                                                        @if ($errors->has('arrival_dtls_datetime'))
                                                            <span class="text-danger">
                                                                {{ $errors->first('arrival_dtls_datetime') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- mode of transport --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mode of transport</label>
                                                        <select class="form-control w-full" id="arrival_dtls_travel_mode"
                                                            name="arrival_dtls_travel_mode">
                                                            <option value="">All</option>
                                                            @php
                                                                $arrival_dtls_mode = $reg->arrival_details
                                                                    ? $reg->arrival_details['mode']
                                                                    : '';
                                                            @endphp
                                                            @foreach (config('stationslist') as $key => $value)
                                                                <option {{ $arrival_dtls_mode == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    {{ $key }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- Mode identifier --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Bus/Flight/Train/Own vehicle number</label>
                                                        <input type="text" class="form-control w-full"
                                                            id="arrival_dtls_mode_identifier"
                                                            name="arrival_dtls_mode_identifier"
                                                            value="{{ old('arrival_dtls_mode_identifier', $reg->arrival_details ? $reg->arrival_details['mode_identifier'] : '') }}" />
                                                    </div>
                                                </div>
                                                {{-- station names --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Station name of depart</label>
                                                        <input type="text" class="form-control w-full"
                                                            id="arrival_dtls_start_point" name="arrival_dtls_start_point"
                                                            value="{{ old('arrival_dtls_start_point', $reg->arrival_details ? $reg->arrival_details['start_point'] : '') }}" />
                                                    </div>
                                                </div>
                                                {{-- station names  --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Station name of arrival</label>
                                                        @php
                                                            $arrival_dtls_end_point = $reg->arrival_details
                                                                ? $reg->arrival_details['end_point']
                                                                : '';
                                                        @endphp
                                                        <select class="form-control w-full select2bs4"
                                                            id="arrival_dtls_end_point" name="arrival_dtls_end_point">
                                                            <option value="">All</option>
                                                            @foreach ($stationslist as $key => $value)
                                                                <option
                                                                    {{ $arrival_dtls_end_point == $value ? 'selected' : '' }}
                                                                    value="{{ $value }}">
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Departure Details --}}
                                        <h5 class="font-weight-bold">Departure Details</h5>
                                        <div class="pl-3">
                                            <div class="row">
                                                {{-- date & time --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bio">Date & Time</label>
                                                        <input type="text"
                                                            class="form-control datetimepicker-input datetime "
                                                            data-toggle="datetimepicker" name="departure_dtls_datetime"
                                                            data-target="#datetime"
                                                            value="{{ old('departure_dtls_datetime', $reg->departure_details ? $reg->departure_details['datetime'] : '') }}" />
                                                        @if ($errors->has('departure_dtls_datetime'))
                                                            <span class="text-danger">
                                                                {{ $errors->first('departure_dtls_datetime') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- mode of transport --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mode of transport</label>
                                                        <select class="form-control w-full"
                                                            id="departure_dtls_travel_mode"
                                                            name="departure_dtls_travel_mode">
                                                            <option value="">All</option>
                                                            @php
                                                                $arrival_dtls_mode = $reg->departure_details
                                                                    ? $reg->departure_details['mode']
                                                                    : '';
                                                            @endphp
                                                            @foreach (config('stationslist') as $key => $value)
                                                                <option {{ $arrival_dtls_mode == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    {{ $key }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- Mode identifier --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Bus/Flight/Train/Own vehicle number</label>
                                                        <input type="text" class="form-control w-full"
                                                            id="departure_dtls_mode_identifier"
                                                            name="departure_dtls_mode_identifier"
                                                            value="{{ old('departure_dtls_mode_identifier', $reg->departure_details ? $reg->departure_details['mode_identifier'] : '') }}" />
                                                    </div>
                                                </div>
                                                {{-- station names --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Station name of depart</label>
                                                        @php
                                                            $departure_dtls_start_point = $reg->departure_details
                                                                ? $reg->departure_details['start_point']
                                                                : '';
                                                        @endphp
                                                        <select class="form-control w-full select2bs4"
                                                            id="departure_dtls_start_point"
                                                            name="departure_dtls_start_point">
                                                            <option value="">All</option>
                                                            @foreach ($stationslist as $key => $value)
                                                                <option
                                                                    {{ $departure_dtls_start_point == $value ? 'selected' : '' }}
                                                                    value="{{ $value }}">
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- station names  --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Station name of arrival</label>
                                                        <input type="text" class="form-control w-full"
                                                            id="departure_dtls_end_point" name="departure_dtls_end_point"
                                                            value="{{ old('departure_dtls_end_point', $reg->departure_details ? $reg->departure_details['end_point'] : '') }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Special Considerations --}}
                                        <h5 class="font-weight-bold">Special Considerations</h5>
                                        <div class="pl-3 pb-5">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {{-- Peferences --}}
                                                    @php
                                                        $food_preferences = $reg->special_considerations
                                                            ? $reg->special_considerations['food_preferences']
                                                            : '';
                                                        $need_attendant = $reg->special_considerations
                                                            ? $reg->special_considerations['need_attendant']
                                                            : '';
                                                    @endphp
                                                    <div class="form-group">
                                                        <label for="food_preferences">Peferences</label>
                                                        <input type="text" class="form-control" id="food_preferences"
                                                            name="food_preferences" placeholder="Peferences"
                                                            value="{{ old('food_preferences', $food_preferences) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    {{-- Need Attendent --}}
                                                    <div class="form-group">
                                                        <label for="need_attendant">Need Attendant</label>
                                                        <input type="text" class="form-control" id="need_attendant"
                                                            name="need_attendant" placeholder="Need Attendant"
                                                            value="{{ old('need_attendant', $need_attendant) }}">
                                                    </div>
                                                </div>
                                                {{-- Floor Matress & Bed --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        @php
                                                            $matressorbed = $reg->special_considerations
                                                                ? $reg->special_considerations['cot_or_bed']
                                                                : '';
                                                        @endphp
                                                        <label for="matress_bed">Floor Matress / Bed</label>
                                                        <select class="form-control" name="cot_or_bed">
                                                            <option
                                                                {{ $matressorbed == 'Floor Matress' ? 'selected' : '' }}
                                                                value="Floor Matress">Floor Matress</option>
                                                            <option {{ $matressorbed == 'Bed' ? 'selected' : '' }}
                                                                value="Bed">Bed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- Hotel Required --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="hotel_required">Hotel Required</label>
                                                        <select class="form-control" name="hotel_required">
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- Sight Seeing --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        @php
                                                            $sightSeeing = $reg->sight_seeing
                                                                ? $reg->sight_seeing['required']
                                                                : '';
                                                        @endphp
                                                        <label for="hotel_required">Sight Seeing</label>
                                                        <select class="form-control" name="sightseeing_required">
                                                            <option {{ $sightSeeing == 'yes' ? 'selected' : '' }}
                                                                value="Yes">Yes</option>
                                                            <option {{ $sightSeeing == 'no' ? 'selected' : '' }}
                                                                value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- Members Count --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        @php
                                                            $membersCount = $reg->sight_seeing
                                                                ? $reg->sight_seeing['members_count']
                                                                : '';
                                                        @endphp
                                                        <label for="members_count">Members Count</label>
                                                        <input type="text" class="form-control" id="members_count"
                                                            name="members_count" placeholder="Members Count"
                                                            value="{{ old('members_count', $membersCount) }}">
                                                    </div>
                                                </div>
                                                {{-- Health Concerns --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="health_concern">Health Concerns</label>
                                                        <input type="text" class="form-control" name="health_concern"
                                                            value="{{ old('health_concern', $reg->health_concern) }}"
                                                            id="health_concern" placeholder="Health Concerns">
                                                    </div>
                                                </div>
                                                {{-- Management Experience --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="management_experience">Management
                                                            Experience</label>
                                                        <select class="form-control" name="management_experience">
                                                            <option
                                                                {{ $reg->management_experience == 'yes' ? 'selected' : '' }}
                                                                value="Yes">Yes</option>
                                                            <option
                                                                {{ $reg->management_experience == 'no' ? 'selected' : '' }}
                                                                value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- Comments --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="comments">Comments if any for Ijtema
                                                            management?</label>
                                                        <input type="text" class="form-control" name="comments"
                                                            value="{{ old('comments', $reg->comments) }}" id="comments"
                                                            placeholder="Comments if any?">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary"
                                            onclick="stepper.previous()">Previous</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script type="text/javascript">
        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })
        // Confirm Arrival Conditional logic
        if ($('#confirm_arrival_no').is(':checked')) {
            $('.ameer_permission_taken_input').show();
            $('.reason_for_not_coming_input').show();
        } else {
            $('.ameer_permission_taken_input').hide();
            $('.reason_for_not_coming_input').hide();
        }
        if ($('#is_family_dtls_exists').val() == 'yes') {
            $('.family_dtls_tree').show();
        } else {
            $('.family_dtls_tree').hide();
        }
        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                if ($(this).attr('id') == 'confirm_arrival_no') {
                    $('.ameer_permission_taken_input').show();
                    $('.reason_for_not_coming_input').show();
                } else {
                    $('.ameer_permission_taken_input').hide();
                    $('.reason_for_not_coming_input').hide();
                }
            });
        });

        function getTreeLength(type = null) {
            return $(`input[name="${type}_dtls[name][]"]`).length;
        }
        console.log(getTreeLength());
        // Adults Familt details
        let adults_dtls_tree = getTreeLength('adults');
        $('#is_family_dtls_exists').on('change', function() {
            if ($(this).val() == 'yes') {
                $('.family_dtls_tree').show();
                if (adults_dtls_tree == 0) {
                    cleanDom('adults');
                    cleanDom('childrens');
                    addNewRow('adults');
                    addNewRow('childrens');
                }
            } else {
                $('.family_dtls_tree').hide();
            }
        })

        function addNewRow(type = null) {
            let treeLength = type == 'adults' ? getTreeLength('adults') + 1 : 0;
            let row = `<div class="row mb-2" data-${type}-id="${treeLength}">
                            <div class="col-md-3">
                                ${domInserter('text', `${type}_dtls[name]`, '', `Enter ${type} name`)}
                            </div>
                            <div class="col-md-3">
                                ${domInserter('text', `${type}_dtls[age]`, '', `Enter ${type} age`)}
                            </div>
                            <div class="col-md-3">
                                ${domInserter('select', `${type}_dtls[gender]`, ['Male', 'Female'])}
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-danger" data-${type}-id-remove="${treeLength}" onclick="removeRow(event, '${type}')">Remove</button>
                            </div>
                        </div>`;
            $(`.${type}_members`).append(row);
        }

        function removeRow(event, type = null) {
            $(`div[data-${type}-id="${event.target.getAttribute(`data-${type}-id-remove`)}"]`).remove();
            if (type == 'adults' && getTreeLength('adults') == 0) {
                $('#is_family_dtls_exists').val('no');
            }
            updateDomIds('adults');
        }

        function updateDomIds(type) {
            $(`div[data-${type}-id]`).each(function(index) {
                $(this).attr(`data-${type}-id`, index + 1);
            });
            $(`button[data-${type}-id-remove]`).each(function(index) {
                $(this).attr(`data-${type}-id-remove`, index + 1);
            });
        }

        function cleanDom(type) {
            $(`.${type}_members`).html('');
        }

        function domInserter(inputType, nameAttribute, inputValue, placeholder = null) {
            if (['text', 'number'].includes(inputType)) {
                return `<input type="${inputType}" name="${nameAttribute}[]" class="form-control"  placeholder="${placeholder}" value="${inputValue}">`;
            } else if (inputType == 'select') {
                let select = `<select class="form-control" name="${nameAttribute}[]">`;
                inputValue.forEach(element => {
                    select += `<option value="${element}">${element}</option>`
                });
                return select + '</select>';
            }
        }
    </script>
@endpush
