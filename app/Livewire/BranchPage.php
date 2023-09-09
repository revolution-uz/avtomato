<?php

namespace App\Livewire;

use App\Constants\PaginatorConstants;
use App\Models\Company\Branch;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BranchPage extends Component
{
    use WithPagination;

    /**
     * Means which branch is editing now
     * @var int|null
     */
    public ?int $branch_id = null;
    /**
     * Means the editing form is shown or not.
     * @var bool|null
     */
    public bool $isFormVisible = false;
    /**
     * The search term.
     * @var string|null
     */
    public ?string $term = null;
    /**
     * The elements count to show in the table.
     * @var int
     */
    public int $perPage = PaginatorConstants::DEFAULT_PER_PAGE;

    #[Rule('required|min:3|max:255')]
    public string $name = '';

    #[Rule('max:16')]
    public string $inn = '';

    #[Rule('max:255')]
    public string $address = '';

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->isFormVisible = false;
        $this->branch_id = null;
        $this->name = '';
        $this->inn = '';
        $this->address = '';
    }

    public function createOrUpdate(): void
    {
        $validatedData = $this->validate([
            'name' => 'required|string|min:3',
            'inn' => 'max:16',
            'address' => 'max:255',
        ]);

        if ($this->branch_id) {
            /** @var Branch $branch */
            $branch = Branch::query()->find($this->branch_id);
            $branch->fill($validatedData);
            $branch->saveOrFail();
            $this->resetForm();
            session()->flash('success', 'Branch updated successfully!');
        } else {
            Branch::query()->create($validatedData);
            session()->flash('success','Branch created successfully!');
        }

        $this->resetForm();
    }

    public function showForm($id = null): void
    {
        if ($id) {
            /** @var Branch $branch */
            $branch = Branch::query()->findOrFail($id);
            $this->branch_id = $id;
            $this->name = $branch->name;
            $this->inn = $branch->inn;
            $this->address = $branch->address;
        } else {
            $this->resetForm();
        }

        $this->isFormVisible = true;
    }

    public function delete($id): void
    {
        if($id) {
            Branch::query()->where('id', $id)->delete();
            session()->flash('success','Branch deleted successfully!');
        }
    }

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $query = Branch::query();

        if ($this->term) {
            $query = $query
                ->where('name', 'like', "%$this->term%")
                ->where('inn', 'like', "%$this->term%")
                ->where('address', 'like', "%$this->term%");
        }

        return view('livewire.branch-page', ['branches' => $query->latest()->paginate($this->perPage)]);
    }
}
