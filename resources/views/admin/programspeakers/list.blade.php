@extends('layouts.app', ['ptype' => 'parent', 'purl' => request()->route()->getName(), 'ptitle' => 'Program Speakers'])
@section('content')
    <x-content-wrapper>
        <x-slot:title>
            Program Speakers
        </x-slot>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-12">
                        <a href="{{ route('programSpeakers.create') }}" class="btn btn-purple float-right"><i
                                class="fas fa-plus mr-2"></i>Create</a>
                    </div>

                </div>
            </div>
            <x-table id="speakers-table">
                <th>SL.No </th>
                <th>Name</th>
                <th>Bio</th>
                <th>Image</th>
                <th>Action</th>
            </x-table>
        </div>
    </x-content-wrapper>
@endsection
@push('scripts')
    <script type="text/javascript">
        const speakersTable = $("#speakers-table").DataTable({
            ajax: {
                url: "{{ route('programSpeakers.index') }}",
            },
            columns: [
                dtIndexCol(),
                {
                    data: 'name',
                },
                {
                    data: 'bio'
                },
                {
                    data: 'speaker_image',
                },
                {
                    data: 'action'
                }
            ]
        })
    </script>
@endpush
