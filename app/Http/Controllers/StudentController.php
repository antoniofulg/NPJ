<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\Human;
// use App\Entities\DoubleStudent;
// use App\Entities\Petition;
// use App\Entities\Group;
use App\User;
use Auth;
// use Validator;

// use Iluminate\Http\Response;

use App\Services\StudentService;

class StudentController extends Controller
{
  public function __construct(StudentService $service) {
    $this->service = $service;
  }

  public function index()
  {
    if(Auth::user()->type == 'admin') {
      $students = Human::all();
      return view('admin.student')->with(['students'=>$students]);

    } else if(Auth::user()->type == 'student') {
      $dados = $this->service->index();
      if (!isset($dados['error'])){
        return view('student.home')->with($dados);
      } else {//se o aluno nao tiver dupla
        return redirect('Sair')->with('erro', $dados['error']);
      }
    }
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|min:3',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:4',
    ]);

    if(Auth::user()->type != 'admin'){
      return redirect()->back();
    }
    return $this->service->store($request);              
  }


  public function update(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string|min:3',
      'email' => 'required|string|email|max:255|unique:users,email,'.$request['id'],
      'password' => 'required|string|min:4',
    ]);

    if(Auth::user()->type != 'admin'){
      return redirect()->back();
    }

    $human = Human::find($request['id']);
    $user = User::find($human->user->id);

    return $this->service->update($human, $user, $request);
  }

  public function destroy(Request $request)
  {  
    if(Auth::user()->type != 'admin'){
      return redirect()->back();
    }
    $student = Human::find($request['id']);
       
    return $this->service->destroy($request, $student);
  }

  public function desactivate(Request $request)
  {  
    if(Auth::user()->type != 'admin'){
      return redirect()->back();
    }
    $student = Human::find($request['id']);
       
    return $this->service->desactivate($request, $student);
  }

  public function activate(Request $request)
  {  
    if(Auth::user()->type != 'admin'){
      return redirect()->back();
    }
    $student = Human::find($request['id']);
       
    return $this->service->activate($request, $student);
  }

  public function preferences()
  {
      $user = User::find(Auth::user()->id);
      $human = Human::where('user_id', $user->id)->first();
      return view('student.preferences')->with(['user' => $user, 'human' => $human]);
  }

  public function preferencesEditar(Request $request)
  {
    $user = User::find($request['idUser']);
    $human = Human::find($request['idHuman']);

    return $this->service->editar($human, $user, $request);
  }
}
