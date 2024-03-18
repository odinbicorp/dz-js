<?php 

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait FileUploadTrait
{
    public function handleFileUpload(Request $request, $postId = 1, $uploadPath = null, $chunksPath = null)
    {
        if ($uploadPath === null) {
            $uploadPath = storage_path('tmp/uploads');
        }
    
        if ($chunksPath === null) {
            $chunksPath = storage_path('tmp/uploads/chunks');
        }

        $extension = $request->file('file')->getClientOriginalExtension();
        $filePath = $uploadPath . '/final_file_' . $postId . '.' . $extension;
        $originalFileName = '';

        if ($request->has('dzuuid') && $request->has('dztotalchunkcount') && $request->hasFile('file')) {
            $uuid = $request->input('dzuuid');
            $totalChunks = (int) $request->input('dztotalchunkcount');
            $chunkIndex = (int) $request->input('dzchunkindex');
            $chunkFilePath = $chunksPath . '/' . $uuid . '_chunk_' . $chunkIndex;

            if ($chunkIndex === 0) {
                $originalFileName = $request->file('file')->getClientOriginalName();
            }

            // Move the received chunk to the chunks directory
            $request->file('file')->move($chunksPath, $chunkFilePath);

            // Check if all chunks have been received
            if ($chunkIndex == $totalChunks - 1) {
                // All chunks have been received, concatenate them into the final file
                $this->concatenateChunks($uuid, $totalChunks, $chunksPath, $filePath);

                // Clean up: delete the individual chunks
                $this->deleteChunks($uuid, $totalChunks, $chunksPath);

                // // Lưu thông tin về file gốc vào database hoặc nơi lưu trữ thông tin khác
                // $this->saveOriginalFileInfo($postId, $originalFileName, $extension);
            }

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error']);
    }

    public function handleCancelUpload(Request $request,$chunksPath)
    {
        if ($chunksPath === null) {
            $chunksPath = storage_path('tmp/uploads/chunks');
        }

        $uuid = $request->input('dzuuid');
     
        for ($i = 0; $i < 1000; $i++) { 
            $chunkFilePath = $chunksPath . '/' . $uuid . '_chunk_' . $i;
            if (File::exists($chunkFilePath)) {
                File::delete($chunkFilePath);
            } else {
                break; 
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Chunks deleted successfully']);
    }

    private function concatenateChunks($uuid, $totalChunks, $chunksPath, $filePath)
    {
        $finalFile = fopen($filePath, 'ab');
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilePath = $chunksPath . '/' . $uuid . '_chunk_' . $i;
            $chunkContent = file_get_contents($chunkFilePath);
            fwrite($finalFile, $chunkContent);
        }
        fclose($finalFile);
    }

    private function deleteChunks($uuid, $totalChunks, $chunksPath)
    {
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilePath = $chunksPath . '/' . $uuid . '_chunk_' . $i;
            unlink($chunkFilePath);
        }
    }

    private function saveOriginalFileInfo($postId, $originalFileName, $extension)
    {
        // Thực hiện lưu thông tin về file gốc vào database hoặc nơi lưu trữ thông tin khác
        // Ví dụ:
        // YourModel::create([
        //     'post_id' => $postId,
        //     'original_file_name' => $originalFileName,
        //     'file_extension' => $extension,
        //     // Các trường khác nếu cần
        // ]);
    }
}
