<?php

namespace App\Livewire;

use App\Constants\PaginatorConstants;
use App\Models\Company\Tariffs;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TariffsPage extends Component
{
    use WithPagination;

    /**
     * Means which tariff is editing now
     * @var int|null
     */
    public ?int $tariff_id = null;
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

    #[Rule('required')]
    public bool $is_annual = false;

    #[Rule('required')]
    public float $percent = 0;

    #[Rule('required')]
    public int $min_amount = 0;

    #[Rule('required')]
    public int $max_amount = 0;

    #[Rule('required')]
    public int $min_initial_fee = 0;

    #[Rule('required')]
    public int $max_initial_fee = 0;

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->isFormVisible = false;
        $this->tariff_id = null;
        $this->name = '';
        $this->is_annual = false;
        $this->percent = 0;
        $this->min_amount = 0;
        $this->max_amount = 0;
        $this->min_initial_fee = 0;
        $this->max_initial_fee = 0;
    }

    public function createOrUpdate(): void
    {
        // TODO check permissions to create/update branches
        $validatedData = $this->validate([
            'name' => 'required|string|min:3',
            'is_annual' => 'required',
            'percent' => 'required',
            'min_amount' => 'required',
            'max_amount' => 'required',
            'min_initial_fee' => 'required',
            'max_initial_fee' => 'required',
        ]);

        if ($this->tariff_id) {
            /** @var Tariffs $tariff */
            $tariff = Tariffs::query()->find($this->tariff_id);
            $tariff->fill($validatedData);
            $tariff->saveOrFail();
            $this->resetForm();
            session()->flash('success', 'Tariff updated successfully!');
        } else {
            Tariffs::query()->create($validatedData);
            session()->flash('success', 'Tariff created successfully!');
        }

        $this->resetForm();
    }

    public function showForm($id = null): void
    {
        if ($id) {
            /** @var Tariffs $tariff */
            $tariff = Tariffs::query()->findOrFail($id);
            $this->tariff_id = $id;
            $this->is_annual = $tariff->is_annual;
            $this->percent = $tariff->percent;
            $this->min_amount = $tariff->min_amount;
            $this->max_amount = $tariff->max_amount;
            $this->min_initial_fee = $tariff->min_initial_fee;
            $this->max_initial_fee = $tariff->max_initial_fee;
        } else {
            $this->resetForm();
        }

        $this->isFormVisible = true;
    }

    public function delete($id): void
    {
        // TODO check permissions to delete branches
        if ($id) {
            Tariffs::query()->where('id', $id)->delete();
            session()->flash('success', 'Tariff deleted successfully!');
        }
    }

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        // TODO check permissions to view the list of branches
        $query = Tariffs::query();

        if ($this->term) {
            $query = $query
                ->where('name', 'like', "%$this->term%")
                ->where('is_annual', '=', $this->term)
                ->where('percent', '=', $this->term)
                ->where('min_amount', '=', $this->term)
                ->where('max_amount', '=', $this->term)
                ->where('min_initial_fee', '=', $this->term)
                ->where('max_initial_fee', '=', $this->term);
        }

        return view('livewire.tariff-page', ['tariffs' => $query->latest()->paginate($this->perPage)]);
    }
}
