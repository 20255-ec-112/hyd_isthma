@extends('layouts.app', ['ptype' => 'child', 'purl' => request()->route()->getName(), 'id' => $program->id ?? '', 'ptitle' => 'Programs', 'ctitle' => $program->id ? 'Edit' : 'Add'])
@section('pagetitle', 'Programs')
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container mt-3 px-3 rounded-2 row">
            <div class="card shadow-sm col-lg-6">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $program->id ? 'Edit' : 'Add' }} Programs
                    </h3>
                </div>
                <form action="{{ $program->id ? route('programs.update', $program->id) : route('programs.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    {{ $program->id ? method_field('PUT') : '' }}
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-lg-12">
                                {{-- Name --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="topic">Topic</label>
                                        <input type="text" class="form-control" name="topic" id="topic"
                                            value="{{ old('topic', $program->topic) }}">
                                        @if ($errors->has('topic'))
                                            <span class="text-danger">
                                                {{ $errors->first('topic') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Date&time --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="bio">Date&Time</label>
                                        <input type="text" class="form-control datetimepicker-input" id="datetime"
                                            data-toggle="datetimepicker" name="datetime" data-target="#datetime"
                                            value="{{ old('datetime', $program->datetime) }}" />
                                        @if ($errors->has('datetime'))
                                            <span class="text-danger">
                                                {{ $errors->first('datetime') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Session Theme --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label>Session Theme</label>
                                        <select class="form-control select2bs4" style="width: 100%;" id="session_theme_id"
                                            name="session_theme_id">
                                            @isset($session_themes)
                                                <option value="">All</option>
                                                @foreach ($session_themes as $theme)
                                                    <option {{ $theme->id == $program->session_theme_id ? 'selected' : '' }}
                                                        value="{{ $theme->id }}"> {{ $theme->theme_name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        @if ($errors->has('session_theme_id'))
                                            <span class="text-danger">
                                                {{ $errors->first('session_theme_id') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Speaker Details --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label>Speaker Name</label>
                                        <select class="form-control select2bs4" style="width: 100%;" id="program_speaker_id"
                                            name="program_speaker_id">
                                            @isset($program_speakers)
                                                <option value="">All</option>
                                                @foreach ($program_speakers as $speaker)
                                                    <option
                                                        {{ $speaker->id == $program->program_speaker_id ? 'selected' : '' }}
                                                        value="{{ $speaker->id }}"> {{ $speaker->name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        @if ($errors->has('program_speaker_id'))
                                            <span class="text-danger">
                                                {{ $errors->first('program_speaker_id') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Status --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="">-- Status --</option>
                                            <option value="1" {{ $program->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $program->status == 0 ? 'selected' : '' }}>In Active
                                            </option>
                                        </select>
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
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple  mr-2">{{ $program->id ? 'Update' : 'Save' }}</button>
                                <a href="{{ route('programs.index') }}" class="btn btn-secondary ">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('scripts')
@endpush
