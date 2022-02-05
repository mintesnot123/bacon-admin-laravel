@extends('layouts.admin')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2> Show Details</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-primary" href="{{ route('doctors.index') }}"> Back</a>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">


         @if($doctor->signature )
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

          <!--       <strong>Photo:</strong> -->

               <img src="{!! getenv('APP_URL').$doctor->photo !!}" width="80" >

            </div>

        </div>
        @endif


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Name:</strong>

                {{ $doctor->name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Email:</strong>

                {{ $doctor->email }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Mobile No:</strong>

                {{ $doctor->mobile_no }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Sex:</strong>

                {{ $doctor->sex }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Age:</strong>

                {{ $doctor->age }}

            </div>

        </div>

        

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Roles:</strong>

                @if(!empty($doctor->role_name))

                        <label class="badge badge-success">{{ $doctor->role_name }}</label>

                @endif

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Device Type:</strong>

                {{ $doctor->device_id==1?'API':'Web' }}

            </div>

        </div>

        @if($doctor->signature )
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Signature:</strong>

               <img src="{!! getenv('APP_URL').$doctor->signature !!}" width="80" >

            </div>

        </div>
        @endif



        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Posted By:</strong>

                {{ $doctor->postedby->name }}

            </div>

        </div>


     

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Status:</strong>

                {{ $doctor->status?'Active':'Inactive' }}

            </div>

        </div>

    </div>




    <div class="col-xs-12 col-sm-12 col-md-6">

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Registration No:</strong>

                {{ $doctor->bmdc_regi_no }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Specialty:</strong>

                {{ $doctor->specialty_name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Education Degree:</strong>

                {{ $doctor->degree }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Institute Name:</strong>

                {{ $doctor->institute_name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Relevant Degree:</strong>

                 {{ $doctor->relevant_degree }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Chamber Address :</strong>

                 {{ $doctor->chamber_address }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Reference:</strong>

                {{ $doctor->reference }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Note:</strong>

                {{ $doctor->note }}

            </div>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12">
        <strong>Doctor's Certificate(s):</strong>
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