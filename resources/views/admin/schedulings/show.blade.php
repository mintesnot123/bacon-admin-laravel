@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2> Show Scheduling</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('schedulings.index') }}"> Back</a>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Name:</strong>

                {{ $schedulings->doctor->name}}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Day Name:</strong>

               {{ App\Scheduling::dayofweek($schedulings->day_id) }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Slot Name:</strong>

                {{ $schedulings->slot_name }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Slot Durations:</strong>

                {{ $schedulings->slot_duration.' mins.' }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Posted By:</strong>

                {{ $schedulings->postedby->name}}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Status</strong>

               {{ $schedulings->status?'Active':'Inactive' }}

            </div>

        </div>

        

    </div>

@endsection

