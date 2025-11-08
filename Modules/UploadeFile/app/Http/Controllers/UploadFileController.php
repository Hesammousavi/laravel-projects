<?php

namespace Modules\UploadeFile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileController extends Controller
{
    public function uploadChunkFile(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required','file'],
            "chunk_number" => ['required' , 'integer' , 'min:1'],
            'total_chunks' => ['required' , 'integer' , 'min:1'],
            'file_identifier' => ['required' , 'string'],
            'original_name' => ['required' , 'string']
        ]);

        $chunk = $validated['file'];
        $chunkNumber = $validated['chunk_number'];
        $totalChunk = $validated['total_chunks'];
        $fileIdentifier = $validated['file_identifier'];
        $originalName = $validated['original_name'];

        $tempDisk = 'local';
        $tempDirectory = "chunks/{$fileIdentifier}";

        Storage::disk($tempDisk)->putFileAs($tempDirectory, $chunk , "{$chunkNumber}.part");

        $storedChunks = collect(Storage::disk($tempDisk)->files($tempDirectory))
            ->filter(fn($path) => str_ends_with($path , '.part'));


        if($storedChunks->count() < $totalChunk) {
            return response()->json([
                'chunk_received' => $chunkNumber,
                'status' => 'waiting for more chunks',
            ]);
        }

       ProccessFile

       $fileName = (string) Str::ulid() . "-$originalName";
       $finalPath = "/uploads/$fileName";

       $fullPath = Storage::disk('public')->path($finalPath);
       $outputStream = fopen($fullPath , 'ab');

       $storedChunks = $storedChunks
           ->sortBy(fn($path) => (int) pathinfo($path , PATHINFO_FILENAME))
           ->values();

       foreach($storedChunks as $path)
       {
           $chunkStream = Storage::disk($tempDisk)->readStream($path);
           stream_copy_to_stream($chunkStream, $outputStream);
           fclose($chunkStream );
       }

       fclose($outputStream);

       Storage::disk($tempDisk)->deleteDirectory($tempDirectory);

       Storage::disk('public')->size($path);
       // Validation Final File ( Size , MimeTypes )

       // store file data in database

       return response()->json([
           'done' => true,
           // 'file' => $fileRecord,
       ]);
    }
}
