@extends('layouts.admin')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2> Show Details</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-primary" href="{{ route('appointments.index') }}"> Back</a>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">
        <strong>Patient Detail:</strong>
        <hr />
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Name:</strong>

                {{ $appointment->patient->name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Email:</strong>

                {{ $appointment->patient->email }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Mobile No:</strong>

                {{ $appointment->patient->mobile_no }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Gender:</strong>

                {{ $appointment->patient_gender }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Age:</strong>

                 {{ $appointment->patient_age }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Note:</strong>

                 {{ $appointment->patient_note }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Address:</strong>
                 {{'Address :'. $appointment->address }}, {{ 'Country :'.$appointment->patient_country_name }},
                 {{ 'Division :'.$appointment->patient_division_name }}, {{ 'City:'.$appointment->patient_city_name }},
                 {{ 'Thana/Zone:'.$appointment->patient_zone_name }}

            </div>

        </div>
       

    </div>




    <div class="col-xs-12 col-sm-12 col-md-6">

        <strong>Doctor Detail:</strong>
        <hr />

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Name </strong>

                {{ $appointment->doctor->name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor E-mail </strong>

                {{ $appointment->doctor->email }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Phone </strong>

                {{ $appointment->doctor->mobile_no }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Gender </strong>

                {{ $appointment->doctor_gender }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Age </strong>

                {{ $appointment->doctor_age}}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Registration No:</strong>

                {{ $appointment->bmdc_regi_no }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Specialty:</strong>

                {{ $appointment->doctor_specialty }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Education Detail:</strong>

                {{ 'Degree Name :'.$appointment->degree }}
                {{ 'Institute Name:'.$appointment->institute_name }}
                {{ 'Relevant Degree:'.$appointment->relevant_degree }}

            </div>

        </div>
        

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Chamber Address :</strong>

                 {{ $appointment->chamber_address }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Others:</strong>

                {{ 'Reference :'.$appointment->reference }}

                 {{ 'Note :'.$appointment->note }}

            </div>

        </div>

    </div>

</div>

<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12">
        <strong>Appointment Detail:</strong>
        <hr />
    </div> 
        <div class="col-xs-12 col-sm-12 col-md-6">


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Appointment Date and time:</strong>

                    {{ App\Appointment::schedule_date($appointment->appointment_date,$appointment->schedule->start_time)}}

                </div>

            </div>
            
            

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Appointment No:</strong>

                    {{ $appointment->appoint_no }}

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Appointment Slot Name:</strong>

                    {{ $appointment->slot_name }}

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Appointment Duration:</strong>

                    {{ $appointment->slot_duration.' mins.' }}

                </div>

            </div>

            
        </div>


        <div class="col-xs-12 col-sm-12 col-md-6">
            
            

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Device Type:</strong>

                    {{ $appointment->device_id==1?'API':'Web' }}

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Posted By:</strong>

                    {{ $appointment->postedby->name }}

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Status:</strong>

                    {{ $appointment->status?'Active':'Inactive' }}

                </div>

            </div>
        </div>
    
</div>

@endsection