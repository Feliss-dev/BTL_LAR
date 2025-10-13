<?php

namespace App\Livewire\Admin;

use App\User;
use Livewire\Component;

class UserTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = User::query();

        if (!empty($this->filter)) {
            $query->where('name', 'like', '%' . $this->filter . '%');
        }

        $users = $query->paginate(10);

        return view('livewire.admin.user-table', [
            'users' => $users,
        ]);
    }
}
