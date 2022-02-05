@extends('layouts.admin')


@section('content')

<div class="row">

    <div class="col-lg-12 margin-tb">

        <div class="pull-left">

            <h2>Edit Patient</h2>

        </div>

        <div class="pull-right">

            <a class="btn btn-primary" href="{{ route('patients.index') }}"> Back</a>

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


{!! Form::model($patient, ['method' => 'PATCH','route' => ['patients.update', $patient->id], 'enctype'=>'multipart/form-data']) !!}

<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-6">

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

                {!! Form::select('sex', array('Male'=>'Male','Female'=>'Female'), $patient->sex, array('class'=>'form-control') ) !!} 

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
        
    </div>



    <div class="col-xs-12 col-sm-12 col-md-6">


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Country:</strong>

                {!! Form::select('country_id', $countries, $patient->country_id, array('id'=>'country_id','class'=>'form-control')) !!} 

            </div>

        </div>



        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Division:</strong>
                 <input type="hidden" id="old_division_id" value="{{ $patient->division_id }}" >

                {!! Form::select('division_id', $divisions, $patient->division_id, array('id'=>'division_id','class'=>'form-control')) !!} 

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>City:</strong>
                 <input type="hidden" id="old_city_id" value="{{ $patient->city_id }}" >

                {!! Form::select('city_id', $cities, $patient->city_id, array('id'=>'city_id','class'=>'form-control')) !!} 

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Zone/Thana:</strong>
                 <input type="hidden" id="old_zone_id" value="{{ $patient->zone_id }}" >

                {!! Form::select('zone_id', $zones, $patient->zone_id, array('id'=>'zone_id','class'=>'form-control')) !!} 

            </div>

        </div>

        

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Address:</strong>

                {!! Form::textarea('address', null, array('rows'=>'2','placeholder' => 'Address','class' => 'form-control')) !!}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Note:</strong>

                {!! Form::textarea('note', null, array('rows'=>'2','placeholder' => 'Note','class' => 'form-control')) !!}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Status:</strong>

                {!! Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $patient->status, array('class'=>'form-control')) !!} 

            </div>

        </div>
       
    </div>


    <div class="form-group row">
        <label for="photo" class="col-md-12 col-form-label text-md-right"> <strong>Patient's Prescriptions:</strong></label>
    </div>    

    <div class="form-group row">
       
        <div class="col-md-3">
            <input id="photo" type="file" class="form-control" name="photo">
           
        </div>
    
        
        <div class="col-md-3">
            <input id="photo1" type="file" class="form-control" name="photo1">
            
        </div>


         <div class="col-md-3">
            <input id="photo2" type="file" class="form-control" name="photo2">
            
        </div>

        <div class="col-md-3">
            <input id="photo3" type="file" class="form-control" name="photo3">
            
        </div>
    </div>



    <div class="col-xs-12 col-sm-12 col-md-12 text-right">

        <button type="submit" class="btn btn-primary">Submit</button>

    </div>


</div>

{!! Form::close() !!}


    <script type="text/javascript">

     $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });


      // edit Sub Category 
      var country_id=$("select[name='country_id']").val();
      var division_id=$("#old_division_id").val();
      var city_id=$("#old_city_id").val();
      var zone_id=$("#old_zone_id").val();

      $.ajax({
      type:'GET',
      url:"{{ route('ajaxDivisionRequest.get') }}",
      data: {
         country_id: country_id,
         division_id: division_id,
      },
      success:function(data){
          $("select[name='division_id'").html('');
          $("select[name='division_id'").html(data.options);
        }
      });


      $.ajax({
      type:'GET',
      url:"{{ route('ajaxCityRequest.get') }}",
      data: {
         city_id: city_id,
         division_id: division_id,
      },
      success:function(data){
          $("select[name='city_id'").html('');
          $("select[name='city_id'").html(data.options);
        }
      });


      $.ajax({
      type:'GET',
      url:"{{ route('ajaxZoneRequest.get') }}",
      data: {
         city_id: city_id,
         zone_id: zone_id,
      },
      success:function(data){
          $("select[name='zone_id'").html('');
          $("select[name='zone_id'").html(data.options);
        }
      });


      $(document).ready(function () {
       
          $('#country_id').on('change',function(e) {
           
           var country_id = e.target.value;

           $.ajax({
                 
                 url:"{{ route('ajaxDivisionRequest.post') }}",
                 type:"POST",
                 data: {
                     country_id: country_id
                  },
                
                 success:function (data) {
                    $("select[name='division_id'").html('');
                    $("select[name='division_id'").html(data.options);
                 }
             })
          });


          $('#division_id').on('change',function(e) {
               
           var division_id = e.target.value;

           $.ajax({
                 
                 url:"{{ route('ajaxCityRequest.post') }}",
                 type:"POST",
                 data: {
                     division_id: division_id
                  },
                
                 success:function (data) {
                    $("select[name='city_id'").html('');

                    $("select[name='city_id'").html(data.options);

                 }
             })
          });

          $('#city_id').on('change',function(e) {
               
           var city_id = e.target.value;

           $.ajax({
                 
                 url:"{{ route('ajaxZoneRequest.post') }}",
                 type:"POST",
                 data: {
                     city_id: city_id
                  },
                
                 success:function (data) {
                    $("select[name='zone_id'").html('');

                    $("select[name='zone_id'").html(data.options);

                 }
             })
          });


      });
    </script>

@endsection