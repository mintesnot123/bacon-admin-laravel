@extends('layouts.admin')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2>Edit Doctor</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-primary" href="{{ route('doctors.index') }}"> Back</a>

        </div>

    </div>

</div>


@if (count($errors) > 0)

  <div class="alert alert-danger">

    <strong>Whoops!</strong> There were some problems with your input.<br><br>

    <ul>

       @foreach ($errors->all() as $error)

         <li>{{ $error }}</li>

       @endforeach

    </ul>

  </div>

@endif


{!! Form::model($doctor, ['method' => 'PATCH','route' => ['doctors.update', $doctor->id], 'enctype'=>'multipart/form-data']) !!}

<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Photo:</strong>

                {!! Form::file('photo', null, array('placeholder' => 'Photo','class' => 'form-control')) !!}

            </div>

        </div>



        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Name:</strong>

                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}

            </div>

        </div>

        


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Email:</strong>

                {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Mobile No:</strong>

                {!! Form::text('mobile_no', null, array('placeholder' => 'Mobile No','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Sex:</strong>

                <!-- {!! Form::text('sex', null, array('placeholder' => 'Gender','class' => 'form-control')) !!} -->

                {!! Form::select('sex', array('Male'=>'Male','Female'=>'Female'), $doctor->sex, array('class'=>'form-control') ) !!} 

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Age:</strong>

                {!! Form::text('age', null, array('placeholder' => 'Age','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Password:</strong>

                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Confirm Password:</strong>

                {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Role:</strong>

                {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control')) !!}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Status:</strong>

                    {!! Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $doctor->status, array('class'=>'form-control')) !!} 

                </div>

            </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Signature:</strong>

                {!! Form::file('signature', null, array('placeholder' => 'Doctor Signature','class' => 'form-control')) !!}

            </div>

        </div>

        
    </div>



    <div class="col-xs-12 col-sm-12 col-md-6">


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>BMDC Registration No:</strong>

                {!! Form::text('bmdc_regi_no', null, array('placeholder' => 'BMDC Registration No','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Education Degree:</strong>

                {!! Form::text('degree', null, array('placeholder' => 'Degree','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Institute Name:</strong>

                {!! Form::text('institute_name', null, array('placeholder' => 'Institute Name', 'class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Specialty:</strong>

                {!! Form::select('speciality_id', $specialities, $doctor->speciality_id, array('class'=>'form-control') ) !!} 

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Relevant Degree:</strong>

                {!! Form::text('relevant_degree', null, array('placeholder' => 'Relevant Degree','class' => 'form-control')) !!}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Reference:</strong>

                {!! Form::text('reference', null, array('placeholder' => 'Reference','class' => 'form-control')) !!}

            </div>

        </div>



        

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Chamber Address:</strong>

                {!! Form::textarea('chamber_address', null, array('rows'=>'2','placeholder' => 'Chamber Address','class' => 'form-control')) !!}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Note:</strong>

                {!! Form::textarea('note', null, array('rows'=>'2','placeholder' => 'Note','class' => 'form-control')) !!}

            </div>

        </div>
       
    </div>

    <div class="form-group row">
        <label for="photo" class="col-md-12 col-form-label text-md-right"> <strong>Certificates:</strong></label>
    </div>    

    <div class="form-group row">
       
        <div class="col-md-3">
            <input id="photo1" type="file" class="form-control" name="photo1">
           
        </div>
    
        
        <div class="col-md-3">
            <input id="photo2" type="file" class="form-control" name="photo2">
            
        </div>


         <div class="col-md-3">
            <input id="photo3" type="file" class="form-control" name="photo3">
            
        </div>

        <div class="col-md-3">
            <input id="photo4" type="file" class="form-control" name="photo4">
            
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 text-right">

        <button type="submit" class="btn btn-primary">Submit</button>

    </div>


</div>

{!! Form::close() !!}




@endsection