<?php

namespace App\View\Components;

use App\Models\BaseModel;
use App\Models\Company;
use Illuminate\View\Component;
use Illuminate\View\View;

class SelectCompany extends Component
{
    public $companies;

    public function __construct(
        BaseModel $model,
        public mixed $value = '',
    )
    {
        // model is provided for edit
        if ($model->id) {
            $this->value = $model->company_id;
        }
        $this->companies = Company::tenanted()->get()->pluck('name', 'id');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.select-company');
    }
}
