@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>User group details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Name:</td>
                            <td>{{ $userGroup->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <x-admin.detail-actions 
                :id="$userGroup->id"
                baseName="user_group"
                itemName="User group"
            />
        </div>
    </div>
@endsection
