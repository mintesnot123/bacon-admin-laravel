

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2> Prescription </h2>

        </div>


    </div>

</div>

<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">
        <strong>Patient Appointment Detail:</strong>
        <hr />
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Appointment No:</strong>

                {{ $data[0]->appoint_no }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Appointment Date:</strong>

                {{ $data[0]->appointment_date }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Name:</strong>

                {{ $data[0]->name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Age:</strong>

                {{ $data[0]->age }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Patient Gender:</strong>

                {{ $data[0]->sex }}

            </div>

        </div>


    </div>

    <br />< <br />

    <div class="col-xs-12 col-sm-12 col-md-6">
        <strong>Prescription Detail:</strong>
        <hr />
        
    </div>

</div>        

 @foreach ($data as $key=>$appointment)

 <div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">
      
        
        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">


                {{ $appointment->detail }}

            </div>

        </div>

        <hr />

    </div>
</div>    


@endforeach


<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Name</strong>

                {{ $data[0]->doctor_name }}

            </div>

            <div class="form-group">

                <strong> E-mail</strong>

                {{ $data[0]->doctor_email }}

            </div>


            <div class="form-group">

                <strong> Mobile </strong>

                {{ $data[0]->doctor_mobile_no }}

            </div>

            <br /><br />

            <div class="form-group">

                <strong> Signature </strong>
                <img src="{{ $data[0]->doctor_signature }}" height="50px;">

                

            </div>

        </div>
    </div>
</div>        