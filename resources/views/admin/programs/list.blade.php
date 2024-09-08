@extends('layouts.app', ['ptype' => 'parent', 'purl' => request()->route()->getName(), 'ptitle' => 'Programs'])
@section('content')
    <x-content-wrapper>
        <x-slot:title>
            Programs
        </x-slot>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 mb-5">
                    <a href="{{ route('programs.create') }}" class="btn btn-purple float-right"><i
                            class="fas fa-plus mr-2"></i>Create</a>
                    <button class="btn btn-purple float-right mr-2" type="button" data-toggle="collapse"
                        data-target="#regFilters" aria-expanded="false" aria-controls="regFilters">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn btn-purple float-right mr-2" onclick="clearFilters()"> <i class="fas fa-filter "></i>
                        Clear
                        Filters</button>

                </div>
            </div>
            <div class="collapse container" id="regFilters">
                <div class="card card-body shadow-none">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Date & Time</label>
                                <input type="text" class="form-control datetimepicker-input" id="datetime"
                                    data-toggle="datetimepicker" data-target="#datetime" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-table id="programs-table">
                <th>SL.No </th>
                <th>Topic</th>
                <th>Date & Time</th>
                <th>Session Name</th>
                <th>Speaker Name</th>
                <th>Speaker Image</th>
                <th>Status</th>
                <th>Action</th>
            </x-table>
        </div>
    </x-content-wrapper>
@endsection
@push('scripts')
    <script type="text/javascript">
        function clearFilters() {
            $("#datetime").val('');
            setFilter();
        }
        const programsTable = $("#programs-table").DataTable({
            ajax: {
                url: "{{ route('programs.index') }}",
                data: function(d) {
                    d.datetime = $("#datetime").val()
                }
            },
            columns: [
                dtIndexCol(),
                {
                    data: 'topic',
                },
                {
                    data: 'datetime'
                },
                {
                    data: 'session_theme'
                },
                {
                    data: 'speaker_name',
                },
                {
                    data: 'speaker_image',
                },
                {
                    data: 'status',
                },
                {
                    data: 'action'
                }
            ]
        })

        function setFilter() {
            programsTable.draw();
        }
    </script>
@endpush
