@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Edit Setting</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('settings.index') }}"> Back</a>

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


    <form action="{{ route('settings.update',$settings->id) }}" method="POST">

    	@csrf

        @method('PUT')


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Label Name:</strong>

                    {!! Form::text('label_name', $settings->label_name, array('class'=>'form-control','placeholder'=>'Label Name')) !!} 

                </div>

            </div>


            <!-- <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Field Name:</strong>

                     {!! Form::text('field_name', $settings->field_name, array('class'=>'form-control','placeholder'=>'Field Name')) !!} 

                </div>

            </div>  -->


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Field Value:</strong>

                    <textarea class="form-control" style="height:150px" name="field_value" placeholder="Field Value">{{ $settings->field_value }}</textarea>

                     <!-- {!! Form::text('field_value', $settings->field_value, array('class'=>'form-control','placeholder'=>'Field Value')) !!} --> 

                </div>

            </div>


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Status:</strong>

                    
                    {!! Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $settings->status, array('class'=>'form-control')) !!} 

                </div>

            </div>


            


		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">

		      <button type="submit" class="btn btn-primary">Submit</button>

		    </div>

		</div>


    </form>




@endsection