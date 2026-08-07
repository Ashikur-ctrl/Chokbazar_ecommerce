<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeFolderImportProductJob;
use App\Models\ImportBatch;
use App\Models\ImportProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AiProductUploadController extends Controller
{
    public function index(): View
    {
        $sellerId = auth()->user()->seller_id;

        $drafts = ImportProduct::with('product')
            ->where('seller_id', $sellerId)
            ->latest()
            ->paginate(10);

        return view('seller.ai-products.index', compact('drafts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'note' => 'nullable|string|max:500',
        ]);

        $sellerId = auth()->user()->seller_id;

        if (!$sellerId) {
            return back()->with('error', 'Your seller account is not fully set up yet. Contact support.');
        }

        $storedPath = null;

        try {
            $batch = DB::transaction(function () use ($request, $validated, $sellerId, &$storedPath) {
                $batch = ImportBatch::create([
                    'source' => 'seller_ai',
                    'total_products' => 1,
                    'created_by' => auth()->id(),
                ]);

                $sourceId = 'seller-' . $sellerId . '-' . Str::lower(Str::random(8));
                $storedPath = $request->file('image')->store("imports/{$batch->id}/seller-ai", 'public');

                $item = ImportProduct::create([
                    'import_batch_id' => $batch->id,
                    'seller_id' => $sellerId,
                    'source_offer_id' => $sourceId,
                    'raw_payload' => [
                        'source' => 'seller_ai',
                        'notes' => $validated['note'] ?? null,
                    ],
                    'images' => [[
                        'source_path' => $request->file('image')->getClientOriginalName(),
                        'local_path' => $storedPath,
                    ]],
                    'status' => 'pending',
                ]);

                return [$batch, $item];
            });
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            return back()->with('error', 'Something went wrong saving your image. Please try again.');
        }

        AnalyzeFolderImportProductJob::dispatch($batch[1]->id);

        return redirect()->route('seller.ai-products.index')
            ->with('success', 'Image received. Chokbazar AI is preparing the product draft.');
    }

    public function retry(Request $request, ImportProduct $importProduct): RedirectResponse
    {
        if ($importProduct->seller_id !== auth()->user()->seller_id || $importProduct->status !== 'failed') {
            abort(403);
        }

        $importProduct->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        AnalyzeFolderImportProductJob::dispatch($importProduct->id);

        return redirect()->route('seller.ai-products.index')
            ->with('success', 'Analysis restarted. Check back shortly.');
    }

    public function destroy(Request $request, ImportProduct $importProduct): RedirectResponse
    {
        if ($importProduct->seller_id !== auth()->user()->seller_id) {
            abort(403);
        }

        if (!$importProduct->product_id) {
            foreach ($importProduct->images ?? [] as $image) {
                $localPath = is_array($image) ? ($image['local_path'] ?? null) : null;

                if ($localPath) {
                    Storage::disk('public')->delete($localPath);
                }
            }
        }

        $importProduct->delete();

        return redirect()->route('seller.ai-products.index')
            ->with('success', 'Draft deleted.');
    }
}
