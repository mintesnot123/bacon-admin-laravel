@extends('layouts.admin')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2> Show Details</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-primary" href="{{ route('patients.index') }}"> Back</a>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Name:</strong>

                {{ $patient->name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Email:</strong>

                {{ $patient->email }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Mobile No:</strong>

                {{ $patient->mobile_no }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Sex:</strong>

                {{ $patient->sex }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Age:</strong>

                {{ $patient->age }}

            </div>

        </div>

        

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Roles:</strong>

                @if(!empty($patient->role_name))

                        <label class="badge badge-success">{{ $patient->role_name }}</label>

                @endif

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Device Type:</strong>

                {{ $patient->device_id==1?'API':'Web' }}

            </div>

        </div>

        @if($patient->postedby)

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Posted By:</strong>

                {{ $patient->postedby->name }}

            </div>

        </div>
        @endif

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Status:</strong>

                {{ $patient->status?'Active':'Inactive' }}

            </div>

        </div>

    </div>




    <div class="col-xs-12 col-sm-12 col-md-6">

        @if($patient->country)

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Country Name:</strong>

                {{ $patient->country->name }}

            </div>

        </div>
        @endif
        @if($patient->division)
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Division Name:</strong>

                {{ $patient->division->name }}

            </div>

        </div>
        @endif
        @if($patient->city)

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>City Name:</strong>

                {{ $patient->city->name }}

            </div>

        </div>
        @endif
        @if($patient->zone)

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Zone Name:</strong>

                {{ $patient->zone->name }}

            </div>

        </div>
        @endif
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong> Address :</strong>

                 {{ $patient->address }}

            </div>

        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Note:</strong>

                {{ $patient->note }}

            </div>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12">
        <strong>Patient's Prescription(s):</strong>
        <hr />
    </div> 
    <div class="col-xs-12 col-sm-12 col-md-12">

         @foreach ($files as $key => $file) 
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">
                  <a target="_blank" href="{{ url($file->path) }}"> {{$file->name}}</a>
            </div>

        </div>
         @endforeach
    </div>
</div>  

@endsection