@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Edit City</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('cities.index') }}"> Back</a>

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


    <form action="{{ route('cities.update',$cities->id) }}" method="POST">

    	@csrf

        @method('PUT')


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Name:</strong>

		            <input type="text" name="name" value="{{ $cities->name }}" class="form-control" placeholder="Name">

		        </div>

		    </div>


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Country:</strong>

                    {!! Form::select('country_id', $countries, $cities->country_id, array('id'=>'country_id','class'=>'form-control')) !!} 

                </div>

            </div>



            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Division:</strong>
                     <input type="hidden" id="old_division_id" value="{{ $cities->division_id }}" >

                    {!! Form::select('division_id', $divisions, $cities->division_id, array('id'=>'division_id','class'=>'form-control')) !!} 

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Status:</strong>

                    {!! Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $cities->status, array('class'=>'form-control')) !!} 

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

          });
    </script>




@endsection