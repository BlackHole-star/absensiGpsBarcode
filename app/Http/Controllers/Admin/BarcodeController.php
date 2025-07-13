<?php

namespace App\Http\Controllers\Admin;

use App\Models\Barcode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeController extends Controller
{
    public function index()
    {
        $barcodes = Barcode::latest()->get();
        return view('admin.barcodes.index', compact('barcodes'));
    }

    public function create()
    {
        return view('admin.barcodes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'value' => 'required|unique:barcodes',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        Barcode::create($request->all());

        return redirect()->route('admin.barcodes.index')->with('success', 'Barcode berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barcode = Barcode::findOrFail($id);
        return view('admin.barcodes.edit', compact('barcode'));
    }

    public function update(Request $request, $id)
    {
        $barcode = Barcode::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string',
            'value' => 'required|unique:barcodes,value,' . $barcode->id,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        $barcode->update($request->all());

        return redirect()->route('admin.barcodes.index')->with('success', 'Barcode berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barcode = Barcode::findOrFail($id);
        $barcode->delete();

        return redirect()->route('admin.barcodes.index')->with('success', 'Barcode berhasil dihapus.');
    }

    public function download($id)
    {
        $barcode = Barcode::findOrFail($id);

        $qr = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($barcode->value);

        $filename = 'barcode_' . $barcode->id . '.svg';

        return response($qr)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
