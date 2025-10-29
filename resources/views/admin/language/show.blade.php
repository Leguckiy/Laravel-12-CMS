@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>Language details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Name:</td>
                            <td>{{ $language->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Code:</td>
                            <td>{{ $language->code }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Sort order:</td>
                            <td>{{ $language->sort_order }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                <x-admin.status-badge :status="$language->status" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <x-admin.detail-actions 
                :id="$language->id"
                baseName="language"
            />
        </div>
    </div>
@endsection
