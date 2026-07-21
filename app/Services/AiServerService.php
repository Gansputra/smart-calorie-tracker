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
                        'name'     => 'file',
                        'contents' => fopen($image->getRealPath(), 'r'),
                        'filename' => $image->getClientOriginalName(),
                    ],
                ],
            ]);

            $contents = $response->getBody()->getContents();
            $data = json_decode($contents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AiServerService: Invalid JSON response', [
                    'body' => $contents,
                ]);

                return $this->errorResponse('Respons dari AI Server tidak valid.');
            }

            if (isset($data['status']) && $data['status'] === 'error') {
                return $this->errorResponse('Gagal memprediksi makanan. Silakan coba gunakan gambar lain dengan pencahayaan yang lebih baik.');
            }

            return [
                'success'    => true,
                'food_name'  => $data['nama_makanan'] ?? 'Unknown',
                'calories'   => (float) ($data['kalori_dasar'] ?? 0),
                'protein'    => (float) ($data['protein_dasar'] ?? 0),
                'confidence' => (float) ($data['confidence'] ?? 0),
            ];
        } catch (ConnectException $e) {
            Log::warning('AiServerService: Cannot connect to AI Server', [
                'url'   => $this->baseUrl,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Server Mati — Silakan hubungi admin atau aktifkan server AI Anda.');
        } catch (RequestException $e) {
            Log::error('AiServerService: Request failed', [
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
                'error'  => $e->getMessage(),
            ]);

            return $this->errorResponse('Terjadi kesalahan saat memproses gambar.');
        } catch (\Exception $e) {
            Log::error('AiServerService: Unexpected error', [
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Terjadi kesalahan tidak terduga.');
        }
    }

    /**
     * Check if the AI server is online.
     *
     * Uses a raw socket connection test to avoid noisy HTTP logs
     * (e.g. "GET / 404") in the FastAPI terminal.
     */
    public function healthCheck(): array
    {
        $parsed = parse_url($this->baseUrl);
        $host = $parsed['host'] ?? '127.0.0.1';
        $port = $parsed['port'] ?? 8080;

        $socket = @fsockopen($host, $port, $errno, $errstr, 1.5);

        if ($socket) {
            fclose($socket);

            return [
                'online'  => true,
                'status'  => 'online',
                'message' => 'AI Server is running',
            ];
        }

        return [
            'online'  => false,
            'status'  => 'offline',
            'message' => 'AI Server tidak dapat dijangkau.',
        ];
    }

    /**
     * Get target recommendations (calories and protein) from AI Server.
     *
     * @param array{gender: int, age: int, weight: float, height: float, activity_level: int, goal: int} $profileData
     * @return array{success: bool, recommended_calories?: int, recommended_protein?: int, error?: string}
     */
    public function recommendTargets(array $profileData): array
    {
        try {
            $response = $this->client->post('/recommend-target', [
                'json' => [
                    'gender'         => (int) $profileData['gender'],
                    'age'            => (int) $profileData['age'],
                    'weight'         => (float) $profileData['weight'],
                    'height'         => (float) $profileData['height'],
                    'activity_level' => (int) $profileData['activity_level'],
                    'goal'           => (int) $profileData['goal'],
                ],
            ]);

            $contents = $response->getBody()->getContents();
            $data = json_decode($contents, true);

            if (isset($data['status']) && $data['status'] === 'success') {
                return [
                    'success'              => true,
                    'recommended_calories' => $data['recommended_calories'],
                    'recommended_protein'  => $data['recommended_protein'],
                    'source'               => $data['source'] ?? 'machine_learning',
                ];
            }

            return ['success' => false, 'error' => 'Gagal mendapatkan rekomendasi AI.'];
        } catch (\Exception $e) {
            Log::error('AiServerService recommendTargets error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'AI Server tidak merespons. Pastikan Uvicorn aktif.'];
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
