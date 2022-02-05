@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Edit Charge</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('appointment_charges.index') }}"> Back</a>

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


    <form action="{{ route('appointment_charges.update',$charges->id) }}" method="POST">

    	@csrf

        @method('PUT')


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Amount:</strong>

		            <input type="text" name="amount" value="{{ $charges->amount }}" class="form-control" placeholder="Amount">

		        </div>

		    </div>

		     <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Doctor:</strong>

                    {!! Form::select('user_id', $doctors, $charges->user_id,array('class'=>'form-control') ) !!} 

                </div>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">

                <div class="form-group">

                    <strong>Status:</strong>

                    {!! Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $charges->status, array('class'=>'form-control') ) !!} 

                </div>

            </div>

		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">

		      <button type="submit" class="btn btn-primary">Submit</button>

		    </div>

		</div>

    </form>

@endsection