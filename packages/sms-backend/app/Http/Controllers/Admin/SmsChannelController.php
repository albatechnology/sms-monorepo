<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\SmsChannel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SmsChannelController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sms_channel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = SmsChannel::select(sprintf('%s.*', (new SmsChannel)->table));
            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = false;
                $editGate      = 'sms_channel_edit';
                $deleteGate    = 'sms_channel_delete';
                $crudRoutePart = 'sms-channels';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('channel_id', function ($row) {
                return $row->channel_id ? $row->channel->name : "";
            });
            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }
        return view('admin.smsChannels.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sms_channel_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $channels = Channel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.smsChannels.create', ['channels' => $channels]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:sms_channels',
            'channel_id' => 'required|exists:channels,id'
        ]);
        SmsChannel::create($validated);
        return redirect()->route('admin.sms-channels.index');
    }

    public function edit(SmsChannel $smsChannel)
    {
        abort_if(Gate::denies('sms_channel_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $channels = Channel::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.smsChannels.edit', compact('smsChannel','channels'));
    }

    public function update(Request $request, SmsChannel $smsChannel)
    {
        $validated = $request->validate([
            'name' => 'required|unique:sms_channels,name,' . $smsChannel->id,
            'channel_id' => 'required|exists:channels,id'
        ]);
        $smsChannel->update($validated);
        return redirect()->route('admin.sms-channels.index');
    }

    public function show(SmsChannel $smsChannel)
    {
        abort_if(Gate::denies('sms_channel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.smsChannels.show', compact('smsChannel'));
    }

    public function destroy(SmsChannel $smsChannel)
    {
        abort_if(Gate::denies('sms_channel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $smsChannel->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array'
        ]);
        SmsChannel::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
