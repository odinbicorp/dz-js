<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\File;

class DropzoneController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return view('welcome');
    }
    
    public function store(Request $request)
    {
        foreach($request->input('document', []) as $file) {
            //your file to be uploaded
            return $file;
        }
    }

    public function uploads(Request $request, $postId=1)
    {
        $uploadPath = storage_path('tmp/uploads');
        $chunksPath = $uploadPath . '/chunks';

        return $this->handleFileUpload($request, $postId, $uploadPath);
    }

    public function deleteChunk(Request $request)
    {
        $chunkFilePath = $request->input('chunkFilePath');

        if (File::exists($chunkFilePath)) {
            File::delete($chunkFilePath);
            return response()->json(['status' => 'success', 'message' => 'Chunk deleted successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Chunk not found']);
        }
    }
    
    public function cancelUpload(Request $request)
    {
        return $this->handleCancelUpload($request);
    }
}
