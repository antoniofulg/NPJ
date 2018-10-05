@extends('layouts.admin')
@section('component')
<div class="container-fluid">
  <div class="row justify-content-center mt-3 mb-3">
    <div class="col-lg-3 col-md-5 col-12">
      <div class="input-group">
        <div class="input-group-prepend">
          <a class="btn btn-outline-secondary" style="pointer-events: none;cursor: default;text-decoration: none;color: black;"><i class="fa fa-filter fa-1x"></i></a>
        </div>
        <select class="custom-select" id="valueHome" style="cursor:pointer" onchange="verifica(this.value)">
          <option value="1" selected><p>Status das petições</p></option>
          <option value="2">Ranking de duplas</option>
          <option value="3">Ranking de grupos</option>
        </select>
      </div>
    </div>
  </div>
  <div id="option1" style="display:block">    
      <div class="text-center">
        <h1 class="h1 h1-responsive text-center">Status das Petições</h1>
      </div>    
    <br>

    <div class="row justify-content-center">
      <div class="col-lg-4 col-md-4 col-12 mb-3">
        <div class="card">
          <div class="card-header bg-primary text-white">
            ALUNO
            <span class="fas fa-user fa-lg float-right my-1"></span>
          </div>
          <div class="card-body text-center text-primary">
            <h1 class="h1 h1-responsive">{{$petitions->where('student_ok','=','false')->count()}}</h1>            
          </div>
          <div class="card-footer bg-primary">
            <a href="" class="text-white">
              Detalhes
              <span class="fas fa-arrow-right mr-1"></span>
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-4 col-12 mb-3">
        <div class="card text-white">
          <div class="card-header bg-primary text-white">
              PROFESSOR
              <span class="fas fa-user-graduate fa-lg float-right my-1"></span>
            </div>
            <div class="card-body text-center text-primary">
              <h1 class="h1 h1-responsive">
                {{$petitions->where('student_ok','true')->where('teacher_ok','')->where('defender_ok','')->count()}}
              </h1>            
            </div>
            <div class="card-footer bg-primary">
              <a href="" class="text-white">
                Detalhes
                <span class="fas fa-arrow-right mr-1"></span>
              </a>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-4 col-12  ">
          <div class="card text-white">
            <div class="card-header bg-primary text-white">
              DEFENSOR
              <span class="fas fa-user-tie fa-lg float-right my-1"></span>
            </div>
            <div class="card-body text-center text-primary">
              <h1 class="h1 h1-responsive">
                {{$petitions->where('student_ok','true')->where('teacher_ok','true')->where('defender_ok','')->count()}}
              </h1>            
            </div>
            <div class="card-footer bg-primary">
              <a href="" class="text-white">
                Detalhes
                <span class="fas fa-arrow-right mr-1"></span>
              </a>
            </div>
          </div>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
      
      <div class="col-lg-4 col-md-6 mb-3">
          <div class="card text-white">
            <div class="card-header bg-warning text-white">
              RECUSADAS - PROFESSOR
              <span class="fas fa-user-graduate fa-lg float-right my-1"></span>
            </div>
            <div class="card-body text-center text-warning">
              <h1 class="h1 h1-responsive">
                {{$petitions->where('student_ok','false')->where('teacher_ok','false')->count()}}
              </h1>            
            </div>
            <div class="card-footer bg-warning">
              <a href="" class="text-white">
                Detalhes
                <span class="fas fa-arrow-right mr-1"></span>
              </a>
            </div>
          </div>
        </div>

      <div class="col-lg-4 col-md-6 mb-3">
          <div class="card text-white">
            <div class="card-header bg-danger text-white">
              RECUSADAS
              <span class="fas fa-user-tie fa-lg float-right my-1"></span>
            </div>
            <div class="card-body text-center text-danger">
              <h1 class="h1 h1-responsive">
                {{$petitions->where('defender_ok','=','false')->count()}}
              </h1>            
            </div>
            <div class="card-footer bg-danger">
              <a href="" class="text-white">
                Detalhes
                <span class="fas fa-arrow-right mr-1"></span>
              </a>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
          <div class="card text-white">
            <div class="card-header bg-success text-white">
              FINALIZADAS
              <span class="fas fa-check fa-lg float-right my-1"></span>
            </div>
            <div class="card-body text-center text-success">
              <h1 class="h1 h1-responsive">
                {{$petitions->where('student_ok','true')->where('teacher_ok','true')->where('defender_ok','true')->count()}}
              </h1>            
            </div>
            <div class="card-footer bg-success">
              <a href="" class="text-white">
                Detalhes
                <span class="fas fa-arrow-right mr-1"></span>
              </a>
            </div>
          </div>
        </div>
    </div>
    
  </div>

  <div id="option2" style="display:none;width:100%"><!--ranking de Duplas-->
    <ul class="nav nav-primary">
      <li class="nav-item">
        <h1 class="text-center" style="font-family:arial;font-weight:800;padding-left:40%">RANKING DE DUPLAS</h1>
      </li>
    </ul>
    <br><br>
    <table class="table table-condensed table-striped table-bordered">
      <thead class="table">
        <tr>
          <th style="font-size:18pt;width:20%" class="text-center">COLOCAÇÃO</th>
          <th style="font-size:18pt" class="text-center">ALUNO1</th>
          <th style="font-size:18pt" class="text-center">ALUNO2</th>
          <th style="font-size:18pt" class="text-center">N° DE PETIÇÕES</th>
          <th style="font-size:18pt" class="text-center">GRUPO</th>
        </tr>
      </thead>
      <tbody>
        <br>
        @forelse($doubleStudents as $doubleStudent)
            <tr>
              <td class="text-center">{{$count++}}</td>
              <td class="text-center">{{$humans->where('id',$doubleStudent->student_id)->first()->name}}</td>
              <td class="text-center">{{$humans->where('id',$doubleStudent->student2_id)->first()->name}}</td>
              <td class="text-center">{{$doubleStudent->qtdPetitions}}</td>
              <td class="text-center">{{$groups->where('id',$doubleStudent->group_id)->first()->name}}</td>
            </tr>
        @empty
            <td>Não há duplas cadastradas</td>
        @endforelse
      </tbody>
    </table>
  </div>

  <div id="option3" style="display:none;margin-left:2%"><!--ranking de GRUPOS-->
    <ul class="nav nav-primary">
      <li class="nav-item">
        <h1 class="text-center" style="font-family:arial;font-weight:800;padding-left:40%">RANKING DE GRUPOS</h1>
      </li>
    </ul>
    <br><br>
    <table class="table table-condensed table-striped table-bordered">
      <thead class="table">
        <tr>
          <th style="font-size:18pt;width:20%" class="text-center">COLOCAÇÃO</th>
          <th style="font-size:18pt" class="text-center">GRUPO</th>
          <th style="font-size:18pt" class="text-center">N° DE PETIÇÕES</th>
        </tr>
      </thead>
      <tbody>
        <br>
        @forelse($groups as $group)
            <tr>
              <td class="text-center">{{$countG++}}</td>
              <td class="text-center">{{$group->name}}</td>
              <td class="text-center">{{$group->qtdPetitions}}</td>
            </tr>
        @empty
            <td>Não há grupos cadastrados</td>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
