@extends('layouts.supervisor')
@section('component')
<div class="container-fluid">
  <div class="row justify-content-center mt-3">
    <div class="text-center">
      <h1 class="h1 h1-responsive text-center">Status das Petições</h1>
    </div>
  </div>

    <div class="row justify-content-center mt-3">
      
      <div class="col-lg-4 col-md-4 col-12 mb-3">
        @cardhome(['items' => ['title' => 'ALUNO', 'icon' => 'fa-user', 'color' => 'primary']])
          {{$petitions->where('supervisor_id',$supervisor->id)->where('student_ok','!=','true')->count()}}
        @endcardhome
      </div>
      
      <div class="col-lg-4 col-md-4 col-12 mb-3">
        @cardhome(['items' => ['title' => 'DEFENSOR', 'icon' => 'fa-user-tie', 'color' => 'primary']])  
          {{$petitions->where('supervisor_id',$supervisor->id)->where('defender_ok','!=','true')->where('teacher_ok','true')->where('student_ok','true')->count()}}
        @endcardhome
      </div>
      
      <div class="col-lg-4 col-md-4 col-12">
        @cardhome(['items' => ['title' => 'DISPONÍVEIS', 'icon' => 'fa-user-tie', 'color' => 'primary']])  
          {{$petitions->where('student_ok','true')->where('teacher_ok','true')->where('defender_ok','=','')->where('supervisor_id','=','')->count()}}
        @endcardhome
      </div>
    </div>

    <div class="row justify-content-center mt-3">
      
      <div class="col-lg-4 col-md-6 mb-3">
        @cardhome(['items' => ['title' => 'RECUSADAS', 'icon' => 'fa-user-graduate', 'color' => 'danger']])  
          {{$petitions->where('supervisor_id',$supervisor->id)->where('defender_ok','false')->count()}}
        @endcardhome
      </div>
      
      <div class="col-lg-4 col-md-6 mb-3">
        @cardhome(['items' => ['title' => 'FINALIZADAS', 'icon' => 'fa-check', 'color' => 'success']])  
          {{$petitions->where('student_ok','true')->where('teacher_ok','true')->where('defender_ok','true')->where('supervisor_id',$supervisor->id)->count()}}
        @endcardhome
      </div>

    </div>
  </div>

@endsection