
<div class="form-group">
    <!-- {!! Form::label('keyword', 'Keyword: ') !!} -->
    {!! Form::text('keyword', old('keyword')?old('keyword'):Session::get('search.keyword') , ['class'=>'form-control','placeholder' => 'Search Keywords',]) !!}
</div>


<!-- <div class="form-group">
    {!! Form::label('status', 'Status: ') !!}
    {!! Form::select('status', [ 1 => 'Active', 2 => 'Inactive'], old('status')?old('status'):Session::get('search.status'), ['class'=>'form-control', 'placeholder'=> '-Select-']) !!}
</div> 
    
      
    
<div class="form-group">
    {!! Form::label('from', 'From: ') !!}
    {!! Form::date('from', old('from')?old('from'):Session::get('search.from'), ['class'=>'form-control datepicker']) !!}
</div>

<div class="form-group">
    {!! Form::label('to', 'To: ') !!}
    {!! Form::date('to', old('to')?old('to'):Session::get('search.to'), ['class'=>'form-control datepicker']) !!}
</div> -->

<div class="form-group" style="padding-left:10px; ">
  {!! Form::submit('search', ['class'=>'btn btn-info']) !!}
</div>
          