@extends('layouts.app', ['ptype' => 'child', 'purl' => request()->route()->getName(), 'id' => $speaker->id ?? '', 'ptitle' => 'Program Speaker', 'ctitle' => $speaker->id ? 'Edit' : 'Add'])
@section('pagetitle', 'Program Speaker')
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container mt-3 px-3 rounded-2 ">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $speaker->id ? 'Edit' : 'Add' }} Theme Session
                    </h3>
                </div>
                <form
                    action="{{ $speaker->id ? route('programSpeakers.update', $speaker->id) : route('programSpeakers.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    {{ $speaker->id ? method_field('PUT') : '' }}
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-lg-6">
                                {{-- Name --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ old('name', $speaker->name) }}">
                                        @if ($errors->has('name'))
                                            <span class="text-danger">
                                                {{ $errors->first('name') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Bio --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="bio">BIO</label>
                                        <textarea type="text" class="form-control" name="bio" id="bio" value="{{ old('bio', $speaker->bio) }}">{{ old('bio', $speaker->bio) }}</textarea>
                                        @if ($errors->has('bio'))
                                            <span class="text-danger">
                                                {{ $errors->first('bio') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Speaker Image --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="bio">Profile Photo</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="speaker_image"
                                                    name="speaker_image">
                                                <label class="custom-file-label" for="speaker_image">Choose
                                                    file</label>
                                            </div>
                                            <div class="input-group-append">
                                                <span class="input-group-text">Upload</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-purple  mr-2">{{ $speaker->id ? 'Update' : 'Save' }}</button>
                                <a href="{{ route('programSpeakers.index') }}" class="btn btn-secondary ">Cancel</a>
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
