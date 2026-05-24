<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MeetingSummaryService
{
    protected $apiKey;
    protected $endpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
    }

    /**
     * Summarize the given transcript using OpenAI API.
     *
     * @param string $transcript
     * @return string
     * @throws \Exception
     */
    public function summarize(string $transcript): string
    {
        Log::info('MeetingSummaryService: Starting summarization', ['transcript_length' => strlen($transcript)]);

        if (empty($this->apiKey)) {
            Log::warning('MeetingSummaryService: OpenAI API key is missing. Returning mock response.');
            return "【AI要約機能】\nOpenAI APIキーが設定されていません。.envファイルにOPENAI_API_KEYを設定してください。\n\n現在の文字起こし文字数: " . mb_strlen($transcript) . "文字";
        }

        $truncatedTranscript = mb_substr($transcript, 0, 30000);
        $client = new Client();

        try {
            Log::info('MeetingSummaryService: Sending request to OpenAI');
            $response = $client->post($this->endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                // LaravelのHttpファサードが勝手にセットするcrypto_methodを避けるためGuzzleを直接使用
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'あなたはプロフェッショナルな議事録作成アシスタントです。'
                        ],
                        [
                            'role' => 'user',
                            'content' => "以下の会議の文字起こしを、重要な決定事項とネクストアクションを中心に日本語で要約してください。箇条書きで見やすく整理してください。\n\n---\n\n" . $truncatedTranscript
                        ]
                    ],
                    'temperature' => 0.5,
                    'max_tokens' => 1000,
                ],
                'timeout' => 60,
            ]);

            $body = json_decode($response->getBody(), true);
            $content = $body['choices'][0]['message']['content'] ?? null;

            if ($content) {
                Log::info('MeetingSummaryService: Success');
                return $content;
            }

            Log::error('MeetingSummaryService: Empty response from OpenAI');
            return '要約の生成に失敗しました（空のレスポンス）。';

        } catch (GuzzleException $e) {
            Log::error('MeetingSummaryService Guzzle Error: ' . $e->getMessage());
            throw new \Exception('AI要約の生成に失敗しました (Network Error): ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('MeetingSummaryService General Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
