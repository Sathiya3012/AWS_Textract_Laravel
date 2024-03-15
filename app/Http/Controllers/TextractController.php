<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Textract\TextractClient;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\View;

class TextractController extends Controller
{
    public function showUploadForm()
    {
        return view('upload-form');
    }

    public function extractText(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file',
        ]);

        // Create a Textract client
        $textractClient = new TextractClient([
            'version' => 'latest',
            'region' => env('AWS_REGION'),
        ]);

        // Create an S3 client
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_REGION'),
        ]);

        try {
            $file = $request->file('file');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = uniqid() . '.' . $fileExtension;
            $filePath = 'uploads/' . $fileName;

            // Upload file to S3
            $s3Client->putObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $filePath,
                'Body' => fopen($file->getRealPath(), 'r'),
            ]);

            // Specify the document location in an Amazon S3 bucket
            $documentLocation = [
                'S3Object' => [
                    'Bucket' => env('AWS_BUCKET'),
                    'Name' => $filePath,
                ],
            ];

            // Start the asynchronous text detection job
            $result = $textractClient->startDocumentTextDetection([
                'DocumentLocation' => $documentLocation,
            ]);

            $jobId = $result['JobId'];

            $text = $this->getTextDetectionResults($textractClient, $jobId);

            return View::make('text-result')->with('text', $text);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getTextDetectionResults($textractClient, $jobId)
    {
        $maxAttempts = 20; 
        $waitIntervalSeconds = 5; 

        for ($i = 0; $i < $maxAttempts; $i++) {

            $result = $textractClient->getDocumentTextDetection(['JobId' => $jobId]);

            if ($result['JobStatus'] === 'SUCCEEDED') {
                $text = '';
                foreach ($result['Blocks'] as $block) {
                    if ($block['BlockType'] === 'LINE') {
                        $text .= $block['Text'];
                    }
                }

                return $text;
            } elseif ($result['JobStatus'] === 'FAILED') {
                throw new \Exception('Text detection job failed.');
            }

            sleep($waitIntervalSeconds);
        }

        throw new \Exception('Text detection job did not complete within the specified time.');
    }
    
}
