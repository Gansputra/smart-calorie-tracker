<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * AiServerService
 *
 * Handles all communication between the Laravel application and the
 * FastAPI AI server. This service is the single point of contact for
 * AI-related operations, making it easy to swap or update the AI server
 * endpoint without touching any other part of the application.
 */
class AiServerService
{
    protected Client $client;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.ai_server.url', env('AI_SERVER_URL', 'http://localhost:8001'));
        $this->timeout = (int) config('services.ai_server.timeout', env('AI_SERVER_TIMEOUT', 30));

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => $this->timeout,
        ]);
    }

    /**
     * Send an image to the AI server for food prediction.
     *
     * @param  UploadedFile  $image  The uploaded image file
     * @return array{
     *     success: bool,
     *     food_name: string,
     *     calories: float,
     *     protein: float,
     *     confidence: float,
     *     error?: string
     * }
     */
    public function predictFood(UploadedFile $image): array
    {
        try {
            $response = $this->client->post('/predict', [
                'multipart' => [
                    [
                        'name'     => 'image',
                        'contents' => fopen($image->getRealPath(), 'r'),
                        'filename' => $image->getClientOriginalName(),
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AiServerService: Invalid JSON response', [
                    'body' => $response->getBody()->getContents(),
                ]);

                return $this->errorResponse('Respons dari AI Server tidak valid.');
            }

            return $data;
        } catch (ConnectException $e) {
            Log::warning('AiServerService: Cannot connect to AI Server', [
                'url'   => $this->baseUrl,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('AI Server tidak dapat dijangkau. Pastikan server AI sedang berjalan.');
        } catch (RequestException $e) {
            Log::error('AiServerService: Request failed', [
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
                'error'  => $e->getMessage(),
            ]);

            return $this->errorResponse('Terjadi kesalahan saat memproses gambar di AI Server.');
        } catch (\Exception $e) {
            Log::error('AiServerService: Unexpected error', [
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Terjadi kesalahan tidak terduga.');
        }
    }

    /**
     * Check if the AI server is online.
     */
    public function healthCheck(): array
    {
        try {
            $response = $this->client->get('/health');
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'online'  => true,
                'status'  => $data['status'] ?? 'ok',
                'message' => $data['message'] ?? 'AI Server is running',
            ];
        } catch (\Exception $e) {
            return [
                'online'  => false,
                'status'  => 'offline',
                'message' => 'AI Server tidak dapat dijangkau.',
            ];
        }
    }

    /**
     * Build a standardized error response.
     */
    protected function errorResponse(string $message): array
    {
        return [
            'success'    => false,
            'food_name'  => null,
            'calories'   => 0,
            'protein'    => 0,
            'confidence' => 0,
            'error'      => $message,
        ];
    }
}
