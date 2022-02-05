@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Edit Zone/Thana</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('zones.index') }}"> Back</a>

            </div>

        </div>

    </div>


    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Whoops!</strong> There were some problems with your input.<br><br>

            <ul>

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('zones.update',$zones->id) }}" method="POST">

    	@csrf

        @method('PUT')


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Name:</strong>

		            <input type="text" name="name" value="{{ $zones->name }}" class="form-control" placeholder="Name">

		        </div>

		    </div>


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Country:</strong>

                    {!! Form::select('country_id', $countries, $zones->country_id, array('id'=>'country_id','class'=>'form-control')) !!} 

                </div>

            </div>



            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Division:</strong>
                     <input type="hidden" id="old_division_id" value="{{ $zones->division_id }}" >

                    {!! Form::select('division_id', $divisions, $zones->division_id, array('id'=>'division_id','class'=>'form-control')) !!} 

                </div>

            </div>


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>City:</strong>
                     <input type="hidden" id="old_city_id" value="{{ $zones->city_id }}" >

                    {!! Form::select('city_id', $cities, $zones->city_id, array('id'=>'city_id','class'=>'form-control')) !!} 

                </div>

            </div>



            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Status:</strong>

                    {!! Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $zones->status, array('class'=>'form-control')) !!} 

                </div>

            </div>

		    

		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">

		      <button type="submit" class="btn btn-primary">Submit</button>

		    </div>

		</div>


    </form>

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

      });
    </script>


@endsection