@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Add New Scheduling</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('schedulings.index') }}"> Back</a>

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


    <form action="{{ route('schedulings.store') }}" method="POST">

    	@csrf


         <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Slot Name:</strong>

                    {!! Form::text('slot_name', old('slot_name'), array('class'=>'form-control','placeholder'=>'Slot Name')) !!} 

                </div>

            </div>


		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Name of Day:</strong>

		            {!! Form::select('day_id', $days, old('day_id'), array('class'=>'form-control')) !!} 

		        </div>

		    </div>


            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Select Doctor:</strong>

                   {!! Form::select('user_id', $doctors, old('user_id'), array('class'=>'form-control')) !!} 

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Slot Start Time:</strong>

                    {{Form::time('start_time', \Carbon\Carbon::now(), ['class'=>'form-control','style' => 'max-width: 200px'])}}

                </div>

            </div>



            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Slot Duration:</strong>

                    {!! Form::selectRange('slot_duration', 10, 60, old('slot_duration'), array('style' => 'max-width: 200px','class'=>'form-control','placeholder'=>'Slot Duration mins.')) !!} 
                    
                </div>

            </div>


            
            

		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">

		            <button type="submit" class="btn btn-primary">Submit</button>

		    </div>

		</div>


    </form>



@endsection