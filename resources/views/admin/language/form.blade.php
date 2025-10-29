@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>Please check the form for errors!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ isset($language) ? 'Edit Language' : 'Add Language' }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($language) ? route('admin.language.update', $language->id) : route('admin.language.store') }}" method="POST">
                @csrf
                @if(isset($language))
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="name" label="Language name" placeholder="Language name" :value="$language->name ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="code" label="Code" placeholder="Code" :value="$language->code ?? ''" :required="true"/>
                <x-admin.input-field type="number" name="sort_order" label="Sort order" placeholder="Sort order" :value="$language->sort_order ?? ''"/>
                <x-admin.switch-field name="status" label="Status" :value="$language->status ?? false"/>
                <x-admin.form-actions 
                    :isEdit="isset($language)"
                    :backRoute="route('admin.language.index')"
                />
            </form>
        </div>
    </div>
@endsection
