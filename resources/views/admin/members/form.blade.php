@extends('layouts.app', ['ptype' => 'child', 'purl' => request()->route()->getName(), 'id' => $member->id ?? '', 'ptitle' => 'Member', 'ctitle' => $member->id ? 'Edit' : 'Add'])
@section('pagetitle', 'Dashboard')
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container  mt-3 px-3  rounded-2 ">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $member->id ? 'Edit' : 'Add' }} Member
                    </h3>
                </div>
                <form action="{{ $member->id ? route('members.update', $member->id) : route('members.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    {{ $member->id ? method_field('PUT') : '' }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                {{-- name --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ old('name', $member->name) }}">
                                        @if ($errors->has('name'))
                                            <span class="text-danger">
                                                {{ $errors->first('name') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Phone --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="phone">Phone <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="phone" id="phone"
                                            value="{{ old('phone', $member->phone) }}">
                                        @if ($errors->has('phone'))
                                            <span class="text-danger">
                                                {{ $errors->first('phone') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Zone name --}}
                                <div class="form-group">
                                    <label for="zone_name">ZONE NAME <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="zone_name" id="zone_name"
                                        value="{{ old('zone_name', $member->zone_name) }}">
                                    @if ($errors->has('zone_name'))
                                        <span class="text-danger">
                                            {{ $errors->first('zone_name') }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Unit name --}}
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="unit_name">UNIT NAME <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="unit_name" id="unit_name"
                                                value="{{ old('unit_name', $member->unit_name) }}">
                                            @if ($errors->has('unit_name'))
                                                <span class="text-danger">
                                                    {{ $errors->first('unit_name') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- date of birth --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="dob">Date Of Birth</label>
                                        <input type="text" class="form-control" name="dob" id="dob"
                                            placeholder="dd-mm-yyyy" value="{{ old('dob', $member->dob) }}">
                                        @if ($errors->has('dob'))
                                            <span class="text-danger">
                                                {{ $errors->first('dob') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                {{-- email --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="{{ old('email', $member->email) }}">
                                        @if ($errors->has('email'))
                                            <span class="text-danger">
                                                {{ $errors->first('email') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- User Number --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="user_number">User Number</label>
                                        <input type="number" class="form-control" name="user_number" id="user_number"
                                            value="{{ old('user_number', $member->user_number) }}">
                                        @if ($errors->has('user_number'))
                                            <span class="text-danger">
                                                {{ $errors->first('user_number') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- District Name --}}
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="division_name">DISTRICT NAME <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="division_name"
                                                id="division_name"
                                                value="{{ old('division_name', $member->division_name) }}">
                                            @if ($errors->has('division_name'))
                                                <span class="text-danger">
                                                    {{ $errors->first('division_name') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Gender --}}
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label for="gender">Gender</label>
                                        <select class="form-control select2bs4" style="width: 100%;" id="gender"
                                            name="gender">
                                            <option value="">-- Select Gender -- </option>
                                            @if ($member->gender === 'Male')
                                                <option selected name="Male">Male</option>
                                                <option name="Female">Female</option>
                                            @else
                                                <option name="Male">Male</option>
                                                <option selected name="Female">Female</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                {{-- Status --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="gender">Status</label>
                                        <div
                                            class="custom-control custom-switch custom-switch-off-gray custom-switch-on-success">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch3"
                                                name="status"
                                                {{ $member->status === 'Active' ? 'checked' : 'unchecked' }}>
                                            <label class="custom-control-label" for="customSwitch3"></label>
                                        </div>
                                        @if ($errors->has('status'))
                                            <span class="text-danger">
                                                {{ $errors->first('status') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-primary  mr-2">{{ $member->id ? 'Update' : 'Save' }}</button>
                        <a href="{{ route('members.index') }}" class="btn btn-secondary ">Cancel</a>
                    </div>
                </div>
            </div>
            </form>
        </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('scripts')
@endpush
