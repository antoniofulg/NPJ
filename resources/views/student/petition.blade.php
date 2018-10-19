@extends('layouts.student')
@section('component')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-12 my-5">
      <div class="card">
        <div class="card-header">
            <h4>Gerenciar Petições
                <button type="button" class="btn btn-md btn-primary float-right" role="button" data-toggle="modal" data-target="#novaModalPetition" data-toggle="tooltip" data-placement="left" title="Clique para cadastrar nova petição"><i class="fa fa-plus"></i> Nova Peticão</button>
            </h4>
        </div> 
        <div class="card-body">
            <div class="col-lg-12">

                <div class="row">
                  @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul>
                      @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif
                  @if(Session::has('status'))
                    <p class="alert alert-info" style="width:20%;">{{ Session::get('status') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                  @endif
                </div>
                  <div class="row mb-3">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="search" name="" class="form-control" value="" placeholder="Buscar por nome..." onkeyup="filtroDeBusca(this.value)">
                          <div class="input-group-append">
                            <span class="input-group-text">
                              <i class="fas fa-search"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                  </div>
        
            
                  <div class="table-responsive">
                      <table class="table table-striped">
                        <thead class="thead-dark">
        
                        <tr>
                          <th class="text-center">Versão</th>
                          <th class="text-center">Status</th>
                          <th class="text-center">Descrição</th>
                          <th class="text-center">Ações</th>
                        </tr>
                      </thead>
                      <tbody>
                        <br>
                        @forelse($petitions as $petition)
                        @if($petition->visible == 'true')
                            <tr class="object" name="{{$petition->description}}">
                              <td style="width:20%;font-size:10pt;text-align:center">
                                {{$petition->version}}.0
                              </td>
                              <td style="font-size:10pt" class="text-center">
                                @if($petition->student_ok == '')
                                  RASCUNHO
                                @endif
                                @if($petition->teacher_ok == 'false' && $petition->student_ok == 'false')
                                  (PROFESSOR RECUSADA - AGUARDANDO ALUNO)
                                @endif
                                @if($petition->student_ok == 'true' && $petition->teacher_ok != 'true')
                                  AGUARDANDO PROFESSOR
                                @endif
                                @if($petition->student_ok == 'true' && $petition->teacher_ok == 'true' && $petition->defender_ok != 'true')
                                  AGUARDANDO DEFENSOR
                                @endif
                                @if($petition->defender_ok == 'true')
                                  PETIÇÃO FINALIZADA
                                @endif
                                @if($petition->defender_ok == 'false' && $petition->student_ok == 'false')
                                  (DEFENSOR RECUSADA - AGUARDANDO ALUNO)
                                @endif
                              </td>
                              <td style="font-size:10pt" class="text-center">
                                @if($petition->template_id != null)
                                  {{$petition->description}}
                                @else
                                  NULL
                                @endif
                              </td>
                              <td style="font-size:10pt;width:20%" class="text-center">
                                  <button type="button" class="btn btn-outline-success" role="button" onClick="location.href='Peticao/Show/{{$petition->id}}'" title="Visualizar Petição"><i class="fa fa-eye"></i></button>
                                  @if($petition->student_ok != 'true')
                                    <button type="button" class="btn btn-outline-warning" role="button" onClick="location.href='Peticao/Edit/{{$petition->id}}'" title="Editar Petição"><i class="fa fa-trash"></i></button>
                                  @endif
                                  <!-- Quando a Petição for rascunho -->
                                  @if($petition->student_ok == '')
                                    <button type="button" class="btn btn-outline-danger" role="button" data-toggle="modal" data-target="#deleteModalPetition" onclick="deletePetition('{{$petition->id}}','{{$petition->description}}')" title="Excluir Petition"><i class="fa fa-trash"></i></button>
                                  @endif
                              </td>
                            </tr>
                          @endif
                        @empty
                        <tr>
                          <td class="text-center" colspan="4">Nenhuma Petição registrada!</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
           
            </div>
          </div>
    </div>
  </div>

  



<!-- Modal -->
<div class="modal fade" id="novaModalPetition" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Nova Petição</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form action="{{URL::to('Aluno/Template/Escolher')}}" method="post">
          {{ csrf_field() }}
            <div class="row">
              <div class="col-md-12">
                <h3 class="text-center">Selecione um dos Templates abaixo:</h3>
              </div>
            </div>
            <br>
            <div class="row" id="options">
              <div class="col-md-12 text-center">
                <select class="form-control" name="template_id" required>
                  <option value="">Selecione o Template</option>
                  @foreach($templates as $template)
                    <option value="{{$template->id}}">{{$template->title}}</option>
                  @endforeach
                </select>
              </div>
            </div>
       
            <div class="modal-footer ">
              <button type="button" class="btn btn-danger"data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Criar</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="deleteModalPetition" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <form action="{{URL::to('Aluno/Peticao/Delete')}}" method="post">
          {{ csrf_field() }}
          <input type="hidden" name="id" id="deleteIdPetition" value="">
          <div class="text-center">
            <i class="fa fa-exclamation-circle fa-x6" aria-hidden="true"></i>
          </div>
          <h3 class="text-center"><strong style="color:red;">Atenção!</strong></h3>
          <p class="text-center">Caso deseje excluir a PETIÇÃO clique no botão confirmar</p>
          <h4 class="text-center"><strong id="deleteDescriptionPetition"></strong></h4>
          <br>
          <div class="text-center">
            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Fechar</button>
            <button type="submit" class="btn btn-danger btn-lg">Confirmar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>





@endsection
