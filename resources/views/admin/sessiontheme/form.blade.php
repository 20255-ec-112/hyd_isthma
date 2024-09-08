@extends('layouts.app', ['ptype' => 'child', 'purl' => request()->route()->getName(), 'id' => $theme->id ?? '', 'ptitle' => 'Theme Session', 'ctitle' => $theme->id ? 'Edit' : 'Add'])
@section('pagetitle', 'Theme Sessions')
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container mt-3 px-3 rounded-2 ">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $theme->id ? 'Edit' : 'Add' }} Theme Session
                    </h3>
                </div>
                <form action="{{ $theme->id ? route('sessiontheme.update', $theme->id) : route('sessiontheme.store') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    {{ $theme->id ? method_field('PUT') : '' }}
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-lg-6">
                                {{-- Name --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="theme_name">Session Theme Name</label>
                                        <input type="text" class="form-control" name="theme_name" id="theme_name"
                                            value="{{ old('theme_name', $theme->theme_name) }}">
                                        @if ($errors->has('theme_name'))
                                            <span class="text-danger">
                                                {{ $errors->first('theme_name') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Session Type --}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="theme_type">Session Type</label>
                                        <select class="form-control" name="theme_type" id="theme_type">
                                            <option value="">-- Theme Role --</option>
                                            <option value="fixed" {{ $theme->theme_type == 'fixed' ? 'selected' : '' }}>
                                                Fixed</option>
                                            <option value="parallel"
                                                {{ $theme->theme_type == 'parallel' ? 'selected' : '' }}>Parallel</option>
                                        </select>
                                        @if ($errors->has('theme_type'))
                                            <span class="text-danger">
                                                {{ $errors->first('theme_type') }}
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
                                            <option value="1" {{ $theme->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $theme->status == 0 ? 'selected' : '' }}>In Active
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
                                <button class="btn btn-purple  mr-2">{{ $theme->id ? 'Update' : 'Save' }}</button>
                                <a href="{{ route('sessiontheme.index') }}" class="btn btn-secondary ">Cancel</a>
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
