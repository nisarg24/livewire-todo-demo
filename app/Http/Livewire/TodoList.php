<?php

namespace App\Http\Livewire;

use App\Todo;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    public $name;

    public $search;

    public $editName;

    public $editTodoId;

    public function create() {
        $validated = $this->validate([
            'name' => 'required|min:3|max:50'
        ]);

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'saved');

        $this->resetPage();
    }

    public function delete($todoID)
    {
        try{
            Todo::findOrFail($todoID)->delete();
        } catch(Exception $e) {
            session()->flash('error', 'Failed to delete Todo!');
        }
        return;
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editTodoId = $todoID;
        $this->editName = Todo::find($todoID)->name;
    }

    public function cancelEdit()
    {
        $this->reset('editTodoId', 'editName');
    }

    public function update()
    {
        $this->validate([
            'editName' => 'required|min:3|max:50'
        ]);
        Todo::find($this->editTodoId)->update([
            'name' => $this->editName
        ]);

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
