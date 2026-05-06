<?php

namespace App\Http\Controllers;

use App\Models\BodyRecord;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BodyRecordController extends Controller
{
    public function index(Request $request)
    {
        $records = BodyRecord::where('user_id', $request->user()->id)->latest('recorded_date')->paginate(15);
        $latest = BodyRecord::where('user_id', $request->user()->id)->latest('recorded_date')->first();
        $base = BodyRecord::where('user_id', $request->user()->id)->where('recorded_date', '<=', now()->subDays(30))->latest('recorded_date')->first();

        return view('body-records.index', compact('records', 'latest', 'base'));
    }

    public function create()
    {
        return view('body-records.form', ['record' => new BodyRecord(['recorded_date' => now()])]);
    }

    public function store(Request $request)
    {
        try {
            BodyRecord::create($this->validated($request) + ['user_id' => $request->user()->id]);
        } catch (QueryException) {
            return back()->withErrors('同じ日付の身体データはすでに登録されています。')->withInput();
        }

        return redirect()->route('body-records.index')->with('status', '身体データを登録しました。');
    }

    public function edit(Request $request, BodyRecord $bodyRecord)
    {
        $this->authorizeOwner($request, $bodyRecord);

        return view('body-records.form', ['record' => $bodyRecord]);
    }

    public function update(Request $request, BodyRecord $bodyRecord)
    {
        $this->authorizeOwner($request, $bodyRecord);
        try {
            $bodyRecord->update($this->validated($request));
        } catch (QueryException) {
            return back()->withErrors('同じ日付の身体データはすでに登録されています。')->withInput();
        }

        return redirect()->route('body-records.index')->with('status', '身体データを更新しました。');
    }

    public function destroy(Request $request, BodyRecord $bodyRecord)
    {
        $this->authorizeOwner($request, $bodyRecord);
        $bodyRecord->delete();

        return redirect()->route('body-records.index')->with('status', '身体データを削除しました。');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'recorded_date' => ['required', 'date'],
            'weight_kg' => ['required', 'numeric', 'between:1,999.9'],
            'body_fat_pct' => ['nullable', 'numeric', 'between:0,99.9'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function authorizeOwner(Request $request, BodyRecord $bodyRecord): void
    {
        abort_unless($bodyRecord->user_id === $request->user()->id, 403);
    }
}
