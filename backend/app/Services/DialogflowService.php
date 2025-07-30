<?php

namespace App\Services;

use Exception;
use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;
use Illuminate\Support\Facades\Log;

class DialogflowService
{
    protected $sessionsClient;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = config('services.dialogflow.project_id');

        $this->sessionsClient = new SessionsClient([
            'credentials' => storage_path('app/reservationbot-ccwd-f2fc0b804bb8.json'),
        ]);
    }

    public function detectIntent($sessionId, $message, $languageCode = 'vi')
    {
        $start = microtime(true);

        $session = $this->sessionsClient->sessionName($this->projectId, $sessionId);

        $textInput = new TextInput();
        $textInput->setText($message);
        $textInput->setLanguageCode($languageCode);

        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        $request = new DetectIntentRequest();
        $request->setSession($session);
        $request->setQueryInput($queryInput);

        $response = $this->sessionsClient->detectIntent($request);

        $duration = microtime(true) - $start;
        // Log::info("Dialogflow detectIntent internal duration: " . $duration);

        return $response->getQueryResult();
    }


    public function resetSessionContexts($sessionId, $languageCode = 'vi')
    {
        $session = $this->sessionsClient->sessionName($this->projectId, $sessionId);

        $textInput = new TextInput();
        $textInput->setText("reset chat");
        $textInput->setLanguageCode($languageCode);

        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
        $request = new DetectIntentRequest();
        $request->setSession($session);
        $request->setQueryInput($queryInput);
        try {
            $response = $this->sessionsClient->detectIntent($request);

            $resetOutputContexts = [];
            foreach ($response->getQueryResult()->getOutputContexts() as $context) {
                $resetOutputContexts[] = $context->getName();
            }
            // Log::info('Output contexts after reset attempt: ' . json_encode($resetOutputContexts));

            return true;
        } catch (Exception $e) {
            Log::error('Error sending reset trigger to Dialogflow: ' . $e->getMessage());
            throw $e;
        }
    }


    public function close()
    {
        $this->sessionsClient->close();
    }
}
