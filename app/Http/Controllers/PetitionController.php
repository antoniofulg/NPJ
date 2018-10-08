<?php

namespace App\Http\Controllers;

use App\Entities\DoubleStudent;
use App\Entities\Human;
use App\Entities\Petition;
use App\Entities\Template;
use App\Services\PetitionService;
use App\User;
use Auth;
use Illuminate\Http\Request;

class PetitionController extends Controller
{
    //
    public function __construct(PetitionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        if (Auth::user()->type == 'student') {
            $dados = $this->service->studentIndex();

            return view('student.petition')->with($dados);

        } else if (Auth::user()->type == 'teacher') {
            $dados = $this->service->teacherIndex();

            return view('teacher.petition')->with($dados);

        } else if (Auth::user()->type == 'defender') {
            $dados = $this->service->defenderIndex();

            return view('defender.petition')->with($dados);
        }
    }

    public function add()
    { //aluno
        if (Auth::user()->type != 'student') {
            return redirect()->back();
        }

        return view('student.petitionCadastrar')->with(['templates' => $templates]);

    }

    public function store(Request $request)
    {
        //dd($request->botao);
        if (Auth::user()->type == 'student') { // só autenticação

            $student = Human::all()->where('user_id', '=', Auth::user()->id)->where('status', '=', 'active')->first();
            $this->service->newPetition($request, $student);

            return redirect('Aluno/Peticoes');

        } else { //se nao for Aluno
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        if (Auth::user()->type == 'student') { // autenticação
            $petition = Petition::find($request['id']);
            if ($petition != null) { // não requisitar id indisponível
                if ($petition->student_ok == 'false') { //aluno esta editando Peticao RECUSADA
                    if ($request->botao == 'ENVIAR') { //aluno vai ENVIAR a Petição RECUSADA editada
                        $this->service->newVersion($request, $petition);

                        $request->session()->flash('status', 'Petição Enviada com sucesso!');

                    } else if ($request->botao == 'SALVAR') { //aluno vai apenas salvar as alterações e nao vai ENVIAR a Petição RECUSADA
                        $this->service->updateDraft($request, $petition);
                        $request->session()->flash('status', 'Alterações foram salvas com Sucesso!!');
                    }
                    return redirect('Aluno/Peticoes');

                } else if ($petition->student_ok == null) { //aluno esta editando Peticao RASCUNHO
                    if ($request->botao == 'ENVIAR') { //aluno vai enviar Petição RASCUNHO editada

                        $petition->student_ok = 'true'; //student_ok era null, agr é true
                        $this->service->updateDraft($request, $petition);

                        $doubleStudent = DoubleStudent::find($petition->doubleStudent_id);
                        $this->service->countPetition($doubleStudent);

                        $request->session()->flash('status', 'Petição Enviada com sucesso!');

                    } else if ($request->botao == 'SALVAR') { //aluno vai salvar Petição RASCUNHO editada
                        $this->service->updateDraft($request, $petition);
                        $request->session()->flash('status', 'Alterações foram salvas com Sucesso!!');
                    }

                    return redirect('Aluno/Peticoes');
                }
            } else { // se petição nula
                return redirect()->back();
            }
        } else { // se usuário não é aluno
            return redirect()->back();
        }
    }

    public function changePetition(Request $request)
    {

        $p = Petition::find($request['id']); //Peticao escolhida
        $pOld = Petition::all()->where('petitionFirst', $p->petitionFirst)->where('visible', 'true')->first(); //Peticao antiga
        if ($p != null && $pOld != null && $p != $pOld) {
            if ($pOld->defender_ok != 'true') { //Se defensor nao tiver APROVADO a peticao antiga, FAZ A TROCA DAS PETICOES
                // if separado para não quebrar caso $pOld seja null
                $this->service->changePetition($p, $pOld);
                return response()->json(['status' => $status]);

            }
        }

        return redirect()->back(); // se não passar nos dois ifs

    }

    public function copyPetition(Request $request)
    {
        $petition = Petition::find($request['id']); //Peticao escolhida
        //$pOld = Petition::all()->where('petitionFirst',$p->petitionFirst)->where('visible','true')->first();//Peticao antiga

        if ($petition != null) { //se a petição ja tiver sido finalizada FAZ A COPIA DA PETIÇÃO

            //cria nova versao da peticao
            $status = $this->service->copyPetition($petition);
            return response()->json(['status' => $status]);
        }
        return redirect()->back();
    }

    public function escolherTemplate(Request $request)
    {
        $template = Template::find($request->template_id);
        return view('student.petitionCadastrar')->with(['template' => $template]);
    }

    public function delete(Request $request)
    {
        $petition = Petition::find($request['id']);
        if ($petition != null && $petition->student_ok == '') { //peticao diferente de nulo e sendo RASCUNHO
            $this->service->delete($petition);
            $request->session()->flash('status', 'Petição rascunho excluida com Sucesso!!');
        }
        return redirect('Aluno/Peticoes');

    }

    public function edit(Request $request, $id)
    {
        $petition = Petition::find($id);

        if ($petition != null) {
            $hu = Human::all()->where('user_id', Auth::user()->id)->first();
            $doubleHu = DoubleStudent::all()->where('student_id', $hu->id)->where('id', $petition->doubleStudent_id)->first();
            if ($doubleHu == null) {
                $doubleHu = DoubleStudent::all()->where('student2_id', $hu->id)->where('id', $petition->doubleStudent_id)->first();
            } // somente quem for da dupla pode editar

            if ($doubleHu != null && $petition->visible == 'true' && $petition->student_ok != 'true') {
                // se usuário é da dupla, se a petição não foi deletada e se não foi enviada
                $dados = $this->service->edit($petition);
                return view('student.petitionEditar')->with($dados);
            }
        }
        return redirect()->back();

    }

    public function show(Request $request, $id)
    {
        $petition = Petition::find($id);

        if ($petition != null) {
            $hu = Human::all()->where('user_id', Auth::user()->id)->first();
            $doubleHu = DoubleStudent::all()->where('student_id', $hu->id)->where('id', $petition->doubleStudent_id)->first();
            if ($doubleHu == null) {
                $doubleHu = DoubleStudent::all()->where('student2_id', $hu->id)->where('id', $petition->doubleStudent_id)->first();
            } // somente quem for da dupla pode editar

            if ($doubleHu != null) { //se o usuario estiver consultando a sua peticao entoa OK
                $dados = $this->service->show($petition);
                return view('student.petitionShow')->with($dados);
            }
        }
        return redirect()->back();

    }

    public function avaliar(Request $request, $id)
    {
        if (Auth::user()->type == 'teacher' || Auth::user()->type == 'defender') {
            $petition = Petition::find($id);
            if ($petition != null) {
                $human = Human::all()->where('user_id', Auth::user()->id)->first();
                if (Auth::user()->type == 'teacher') {
                    $group = Group::find($petition->group_id);
                    if ($human->id != $group->teacher_id) {
                        return redirect()->back();
                    }
                }
                if ($petition != null && $petition->visible == 'true') {
                    $dados = $this->service->avaliar($petition);
                    return view('defender.petitionAvaliable')->with($dados);
                }
            }
        }

        return redirect()->back();
    }
}
