<?php

namespace App\Livewire;

use App\Models\HospitalType;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class HospitalsListing extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search'];
    protected $listeners = ['hospitalType'];
    public $search;
    public $type;
    public $resetFilter;

    public function render()
    {

        $data = [];

        $hospitals_data = User::with(['department', 'media'])->withoutGlobalScope(new \Stancl\Tenancy\Database\TenantScope())
            ->where('department_id', '=', User::USER_ADMIN)
            ->whereNotNull('username')
            ->whereNotNull('hospital_name');

        $data['hospitals'] = $hospitals_data->paginate(3);
        $data['hospital_types'] = HospitalType::get();

        if(!is_null($this->search) && !empty($this->search)){
            $data['hospitals'] = $hospitals_data->where('hospital_name', 'like',
            '%'.$this->search.'%')->paginate(3);
        }

        if(!is_null($this->type) && !empty($this->type)){
            $data['hospitals'] = $hospitals_data->where('hospital_type_id',$this->type)->paginate(3);
        }
        if(!empty($this->type) && !empty($this->search)){
            $data['hospitals'] = $hospitals_data->where('hospital_name', 'like',
            '%'.$this->search.'%')->where('hospital_type_id',$this->type)->paginate(3);
        }

        return view('livewire.hospitals-listing', compact('data'));
    }

    function hospitalType($type)
    {   
        $this->type = $type;
    }

    function clearFilter()
    {
        $this->search = [];
        $this->type = '';
    }
}
