<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySupervisorTypeRequest;
use App\Http\Requests\StoreSupervisorTypeRequest;
use App\Http\Requests\UpdateSupervisorTypeRequest;
use App\Models\SupervisorDiscountApprovalLimit;
use App\Models\SupervisorType;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class SupervisorTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('supervisor_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisorTypes = SupervisorType::all();
        // $productBrands = ProductBrand::pluck('name', 'id');

        return view('admin.supervisorTypes.index', compact('supervisorTypes'));
    }

    public function create()
    {
        abort_if(Gate::denies('supervisor_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $productBrands = ProductBrand::pluck('name', 'id');

        return view('admin.supervisorTypes.create');
    }

    public function store(StoreSupervisorTypeRequest $request)
    {
        SupervisorType::create($request->validated());
        // $supervisorType = SupervisorType::create($request->except('discount_approval_limit_percentage'));
        // foreach ($request->discount_approval_limit_percentage as $product_brand_id => $limit) {
        //     SupervisorDiscountApprovalLimit::create([
        //         'supervisor_type_id' => $supervisorType->id,
        //         'product_brand_id' => $product_brand_id,
        //         'limit' => (int)$limit,
        //     ]);
        // }
        return redirect()->route('admin.supervisor-types.index');
    }

    public function edit(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $supervisorTypeLimits = $supervisorType->supervisorDiscountApprovalLimits->pluck('limit', 'product_brand_id');
        // $productBrands = ProductBrand::pluck('name', 'id');

        return view('admin.supervisorTypes.edit', ['supervisorType' => $supervisorType, 'supervisorTypeLimits' => $supervisorTypeLimits]);
    }

    public function update(UpdateSupervisorTypeRequest $request, SupervisorType $supervisorType)
    {
        $supervisorType->update($request->validated());
        // $supervisorType->update($request->except('discount_approval_limit_percentage','_method','_token'));
        // $discount_approval_limit_percentage = $request->discount_approval_limit_percentage;
        // foreach ($discount_approval_limit_percentage as $product_brand_id => $limit) {
        //     $data = SupervisorDiscountApprovalLimit::where('supervisor_type_id', $supervisorType->id)->where('product_brand_id', $product_brand_id)->exists();

        //     if ($data) {
        //         SupervisorDiscountApprovalLimit::where('supervisor_type_id', $supervisorType->id)->where('product_brand_id', $product_brand_id)->update(['limit' => (int)$limit]);
        //     } else {
        //         SupervisorDiscountApprovalLimit::create([
        //             'supervisor_type_id' => $supervisorType->id,
        //             'product_brand_id' => $product_brand_id,
        //             'limit' => (int)$limit,
        //         ]);
        //     }
        // }

        return redirect()->route('admin.supervisor-types.index');
    }

    public function show(SupervisorType $supervisorType)
    {
        abort_if(Gate::denies('supervisor_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervisorType->load('supervisorTypeUsers');

        return view('admin.supervisorTypes.show', compact('supervisorType'));
    }

    public function destroy(SupervisorType $supervisorType)
    {
        $supervisorTypeId = $supervisorType->id;
        abort_if(Gate::denies('supervisor_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($supervisorType->delete()) {
            SupervisorDiscountApprovalLimit::where('supervisor_type_id', $supervisorTypeId)->delete();
        }

        return back();
    }

    public function massDestroy(MassDestroySupervisorTypeRequest $request)
    {
        if (SupervisorType::whereIn('id', request('ids'))->delete()) {
            SupervisorDiscountApprovalLimit::whereIn('supervisor_type_id', request('ids'))->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
