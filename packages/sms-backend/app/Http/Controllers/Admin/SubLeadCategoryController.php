<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySubLeadCategoryRequest;
use App\Models\LeadCategory;
use App\Models\SubLeadCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubLeadCategoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sub_lead_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subLeadCategories = SubLeadCategory::get();

        return view('admin.subLeadCategories.index', compact('subLeadCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('sub_lead_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $leadCategories = LeadCategory::tenanted()->pluck('name', 'id');
        return view('admin.subLeadCategories.create', ['leadCategories' => $leadCategories]);
    }

    public function store(Request $request)
    {
        $request = $request->validate([
            'lead_category_id' => 'required',
            'name' => 'required',
            'description' => 'nullable',
        ]);
        SubLeadCategory::create($request);

        return redirect()->route('admin.sub-lead-categories.index');
    }

    public function edit(SubLeadCategory $subLeadCategory)
    {
        abort_if(Gate::denies('sub_lead_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $leadCategories = LeadCategory::tenanted()->pluck('name', 'id');
        return view('admin.subLeadCategories.edit', compact('subLeadCategory', 'leadCategories'));
    }

    public function update(Request $request, SubLeadCategory $subLeadCategory)
    {
        $request = $request->validate([
            'lead_category_id' => 'required',
            'name' => 'required',
            'description' => 'nullable',
        ]);
        $subLeadCategory->update($request);

        return redirect()->route('admin.sub-lead-categories.index');
    }

    public function destroy(SubLeadCategory $subLeadCategory)
    {
        abort_if(Gate::denies('sub_lead_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subLeadCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroySubLeadCategoryRequest $request)
    {
        SubLeadCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
