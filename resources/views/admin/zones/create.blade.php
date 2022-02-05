@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Add New Zone/Thana</h2>

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


    <form action="{{ route('zones.store') }}" method="POST">

    	@csrf


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Name:</strong>

		            <input type="text" name="name" class="form-control" placeholder="Name">

		        </div>

		    </div>

		   <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Country:</strong>

                    {!! Form::select('country_id', $countries, old('country_id'),array('id'=>'country_id','class'=>'form-control')) !!} 

                </div>

            </div>



            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Division:</strong>

                    {!! Form::select('division_id', $divisions, old('division_id'),array('id'=>'division_id','class'=>'form-control')) !!} 

                </div>

            </div>


             <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>City:</strong>

                    {!! Form::select('city_id', $cities, old('city_id'),array('id'=>'city_id','class'=>'form-control')) !!} 

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