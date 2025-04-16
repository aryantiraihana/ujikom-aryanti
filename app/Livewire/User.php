<?php

namespace App\Livewire;

use App\Exports\ExportUser;
use App\Models\User as ModelsUser;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class User extends Component
{
    public $menuOptions = 'lihat';

    public $name;
    public $email;
    public $password;
    public $role;

    public $selectedUser;

    public $page = 1;
    public $searchUser = '';
    protected $queryString = ['page', 'searchUser'];
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [5, 10, 25, 50, 100];

    public function updatingSearchUser(){
        $this->resetPage();
    }

    public function updatingPerPage(){
        $this->resetPage();
    }

    public function exportUser(){
        return Excel::download(new ExportUser, 'data_pengguna.xlsx');
    }

    public function cancel(){
        $this->reset();
    }

    public function toBeDelete($id){
        $this->selectedUser = ModelsUser::findOrFail($id);

        $this->name = $this->selectedUser->name;
        $this->menuOptions = 'delete';
    }

    public function delete(){

        $this->selectedUser->delete();
        $this->reset();
    }

    public function toBeEdited($id){
        $this->selectedUser = ModelsUser::findOrFail($id);

        $this->name = $this->selectedUser->name;
        $this->email = $this->selectedUser->email;
        $this->role = $this->selectedUser->role;

        $this->menuOptions = 'edit';
    }

    public function saveEdit()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'. $this->selectedUser->id,
            'role' => 'required'
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email telah digunakan',
            'role.required' => 'Peran harus diisi'
        ]);

        $save = $this->selectedUser;
        $save->name = $this->name;
        $save->email = $this->email;
        if($this->password){
            $save->password = bcrypt($this->password);
        }
        $save->role = $this->role;
        $save->save();

        $this->reset(['name', 'email', 'password', 'role', 'selectedUser']);
        $this->menuOptions = 'lihat';
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required'
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'role.required' => 'Peran harus diisi'
        ]);

        $save = new ModelsUser();
        $save->name = $this->name;
        $save->email = $this->email;
        $save->password = bcrypt($this->password);
        $save->role = $this->role;
        $save->save();

        $this->reset(['name', 'email', 'password', 'role']);
        $this->menuOptions = 'lihat';
    }

    public function selectMenu($menu){
        $this->menuOptions = $menu;
    }

    public function render()
    {
        $users = ModelsUser::where('name', 'like', '%'.$this->searchUser.'%')
                ->orWhere('email', 'like', '%'.$this->searchUser.'%')
                ->orWhere('role', 'like', '%'.$this->searchUser.'%')
                ->paginate($this->perPage);
        return view('livewire.user', [
        'users' => $users
        ]);

        // return view('livewire.user')->with([
        //     'users' => ModelsUser::all()
        // ]);
    }
}
