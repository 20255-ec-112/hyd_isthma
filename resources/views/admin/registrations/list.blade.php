@extends('layouts.app', ['ptype' => 'parent', 'purl' => request()->route()->getName(), 'ptitle' => 'Registrations'])
@section('content')
    <x-content-wrapper>
        <x-slot:title>
            Registrations
        </x-slot>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 mb-5">

                    <button class="btn btn-purple float-right" type="button" data-toggle="collapse" data-target="#regFilters"
                        aria-expanded="false" aria-controls="regFilters">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn btn-purple float-right mr-2" onclick="clearFilters()"> <i class="fas fa-filter "></i>
                        Clear
                        Filters</button>
                    @if (auth()->user()->id == 1 || auth()->user()->can('Create Registrations'))
                        <a href="{{ route('registrations.create') }}" class="btn btn-purple float-right  mr-2"><i
                                class="fas fa-plus mr-2"></i>Create</a>
                    @endif
                </div>
                <div class="collapse container" id="regFilters">
                    <div class="card card-body shadow-none">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>ZONE NAME</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="zone_name"
                                        onchange="getLocations('zone_name', 'division_name')">
                                        @isset($locationsList['distnctZoneName'])
                                            <option value="">All</option>
                                            @foreach ($locationsList['distnctZoneName'] as $name)
                                                <option value="{{ $name->zone_name }}"> {{ $name->zone_name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>DISTRICT NAME</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="division_name"
                                        onchange="getLocations('division_name', 'unit_name')">
                                        <option value="">All</option>
                                        {{-- data will be dynamically filled --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>UNIT NAME</label>
                                    <select class="form-control select2bs4" style="width: 100%;" id="unit_name"
                                        placeholder="Select Unit Name" onchange="setFilter('unit_name')">
                                        <option value="">All</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Arrival</label>
                                    <select class="form-control w-full" id="confirm_arrival"
                                        onchange="setFilter('confirm_arrival')">
                                        <option value="">-Select Filter-</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-table id="registrations-table">
                <th>Sl No </th>
                <th>Name</th>
                <th>Rukun ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Unit Name</th>
                <th>Zone Name</th>
                <th>Division Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Confirm Arrival</th>
                <th>Non Availibility Reason</th>
                <th>Ammer Permission Taken</th>
                <th>Emergency Contact</th>
                <th>Year of Rukuniyat</th>
                <th>Action</th>
            </x-table>
        </div>
    </x-content-wrapper>
    <!-- /.content -->
@endsection
@push('scripts')
    <script type="text/javascript">
        // clear filters data
        function clearFilters() {
            $('#zone_name').val('').trigger('change');
            $('#division_name').val('').trigger('change');
            $('#unit_name').val('').trigger('change');
        }
        $(function() {
            regTable = $('#registrations-table').DataTable({
                ajax: {
                    url: "{{ route('registrations.index') }}",
                    data: function(d) {
                        const queryParams = new URLSearchParams(window.location.search);
                        d.confirm_arrival = $("#confirm_arrival").val() == '' ? queryParams.has(
                            'confirm_arrival') ? queryParams.get('confirm_arrival') : '' : $(
                            "#confirm_arrival").val();
                        d.unit_name = $("#unit_name").val()
                        d.zone_name = $("#zone_name").val()
                        d.division_name = $("#division_name").val()
                    }
                },
                columns: [
                    dtIndexCol(),
                    {
                        data: 'member.name',
                    },
                    {
                        data: 'member.user_number',
                    },
                    {
                        data: 'member.email',
                    },
                    {
                        data: 'member.phone',
                    },
                    {
                        data: 'member.unit_name',
                    },
                    {
                        data: 'member.zone_name',
                    },
                    {
                        data: 'member.division_name',
                    },
                    {
                        data: 'member.gender',
                    },
                    {
                        data: 'member.age',
                    },
                    {
                        data: 'confirm_arrival'
                    },
                    {
                        data: 'reason_for_not_coming'
                    },
                    {
                        data: 'ameer_permission_taken',
                    },
                    {
                        data: 'emergency_contact',
                    },
                    {
                        data: 'member.year_of_rukniyat',
                    },
                    {
                        data: 'action'
                    },
                ],
            });
        });

        function setFilter(type) {
            regTable.draw();
        }
    </script>
@endpush
