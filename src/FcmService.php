<?php

namespace DevKandil\NotiFire;

use DevKandil\NotiFire\Enums\MessagePriority;
use DevKandil\NotiFire\Exceptions\FcmRequestException;
use DevKandil\NotiFire\Exceptions\UnsupportedTokenFormat;
use Exception;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DevKandil\NotiFire\Contracts\FcmServiceInterface;

class FcmService implements FcmServiceInterface
{
    /**
     * The notification title
     *
     * @var string|null
     */
    private ?string $title = null;

    /**
     * The notification body text
     *
     * @var string|null
     */
    private ?string $body = null;

    /**
     * The action to perform when notification is clicked
     *
     * @var string|null
     */
    private ?string $clickAction = null;

    /**
     * The URL of the image to display in the notification
     *
     * @var string|null
     */
    private ?string $image = null;

    /**
     * The icon to display with the notification
     *
     * @var string|null
     */
    private ?string $icon = null;
    
    /**
     * The color to use for the notification
     *
     * @var string|null
     */
    private ?string $color = null;

    /**
     * Additional data to send with the notification
     *
     * @var array|null
     */
    private ?array $additionalData = null;

    /**
     * The sound to play when the notification is received
     *
     * @var string|null
     */
    private ?string $sound = null;

    /**
     * The notification priority level
     *
     * @var MessagePriority
     */
    private MessagePriority $priority = MessagePriority::NORMAL;

    /**
     * The notification data from array format
     *
     * @var array|null
     */
    private ?array $fromArray = null;

    /**
     * The FCM authentication key
     *
     * @var string|null
     */
    private ?string $authenticationKey = null;

    /**
     * The raw message data for FCM
     *
     * @var array|null
     */
    private ?array $fromRaw = null;

    /**
     * Create a new instance of the FcmService
     *
     * @return static
     */
    public static function build(): static
    {
        return new self();
    }

    /**
     * Set the notification title
     *
     * @param string $title The title text
     * @return static
     */
    public function withTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the notification body text
     *
     * @param string $body The body text
     * @return static
     */
    public function withBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set the action to perform when notification is clicked
     *
     * @param ?string $action The click action URL or identifier
     * @return static
     */
    public function withClickAction(?string $action): static
    {
        $this->clickAction = $action;

        return $this;
    }

    /**
     * Set the image URL to display in the notification
     *
     * @param ?string $image The image URL
     * @return static
     */
    public function withImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Set the icon to display with the notification
     *
     * @param ?string $icon The icon identifier or URL
     * @return static
     */
    public function withIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }
    
    /**
     * Set the color to use for the notification
     *
     * @param ?string $color The color in hexadecimal format (e.g., #RRGGBB)
     * @return static
     */
    public function withColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Set the sound to play when the notification is received
     *
     * @param ?string $sound The sound identifier
     * @return static
     */
    public function withSound(?string $sound): static
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Set the notification priority level
     *
     * @param MessagePriority $priority The priority level
     * @return static
     */
    public function withPriority(MessagePriority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set additional data to send with the notification
     *
     * @param array $data Key-value pairs of additional data
     * @return static
     */
    public function withAdditionalData(array $data): static
    {
        $this->additionalData = collect($data)->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])->toArray();

        return $this;
    }

    /**
     * Set the FCM authentication key
     *
     * @param string $authenticationKey The FCM authentication key
     * @return static
     */
    public function withAuthenticationKey(string $authenticationKey): static
    {
        $this->authenticationKey = $authenticationKey;

        return $this;
    }

    /**
     * Set the notification data from an array
     *
     * @param array $fromArray The notification data array
     * @return static
     */
    public function fromArray(array $fromArray): static
    {
        $this->fromArray = $fromArray;

        return $this;
    }

    /**
     * Set a raw FCM message to be sent later
     *
     * @param array $message The raw FCM message payload
     * @return static
     */
    public function fromRaw(array $message): static
    {
        $this->fromRaw = $message;
        return $this;
    }

    /**
     * Send FCM notification to a specific token or array of tokens
     *
     * @param string|array $token Single FCM token or array of tokens
     * @return bool Whether all notifications were sent successfully
     * @throws UnsupportedTokenFormat When token is not a string or array
     * @throws FcmRequestException When the FCM API request fails
     * @throws Exception For other unexpected errors
     */
    public function sendNotification($token): bool
    {
        try {
            if (empty($token)) {
                $this->log('warning', 'Empty FCM token provided');
                return false;
            }
            
            if (!is_string($token) && !is_array($token)) {
                throw new UnsupportedTokenFormat();
            }
            
            // Convert to array for consistent handling
            $tokens = is_string($token) ? [$token] : $token;

            $accessToken = $this->getGoogleAccessToken();

            $success = true;
            
            // Send to each token
            foreach ($tokens as $singleToken) {
                $fields = [
                    'message' => [
                        'notification' => [
                            'title' => $this->title,
                            'body' => $this->body,
                        ],
                        'android' => [
                            'priority' => $this->priority->value,
                            'notification' => [
                                'icon' => $this->icon,
                                'color' => $this->color,
                                'image' => $this->image,
                                'sound' => $this->sound ?? 'default',
                            ]
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'content-available' => 1,
                                    'mutable-content' => 1,
                                    'sound' => $this->sound ?? 'default'
                                ]
                            ],
                            'fcm_options' => [
                                'image' => $this->image
                            ]
                        ],
                        'webpush' => [
                            'notification' => [
                                'title' => $this->title,
                                'body' => $this->body,
                                'icon' => $this->icon,
                                'image' => $this->image,
                                'sound' => $this->sound ?? 'default',
                            ],
                        ],
                        'token' => $singleToken
                    ],
                ];

                // Add click_action to the appropriate location
                if ($this->clickAction) {
                    $fields['message']['android']['notification']['click_action'] = $this->clickAction;
                    // For iOS, we need to add category for click_action
                    $fields['message']['apns']['payload']['aps']['category'] = $this->clickAction;

                    // For WebPush, parse URL and extract path for fcm_options['link']
                    $parsedUrl = parse_url($this->clickAction);
                    $webpushLink = $parsedUrl['path'] ?? $this->clickAction;
                    // Only add fcm_options if we actually have a link
                    $fields['message']['webpush']['fcm_options'] = [
                        'link' => $webpushLink
                    ];
                }

                if ($this->additionalData) {
                    $fields['message']['data'] = $this->additionalData;
                    // Merge additionalData into webpush data field as well
                    $fields['message']['webpush']['data'] = $this->additionalData;
                }

                try {
                    $response = $this->callApi($fields, $accessToken);
                    
                    if (isset($response['name'])) {
                        $this->log('info', 'FCM notification sent successfully', [
                            'token' => $singleToken,
                            'message_id' => $response['name']
                        ]);
                    } else {
                        $this->log('error', 'Failed to send FCM notification', [
                            'token' => $singleToken,
                            'error' => $response['error'] ?? 'Unknown error'
                        ]);
                        $success = false;
                    }
                } catch (Exception $e) {
                    $this->log('error', 'FCM notification failed', [
                        'token' => $singleToken,
                        'error' => $e->getMessage()
                    ]);
                    $success = false;
                }
            }
            
            return $success;
        } catch (FcmRequestException $e) {
            $this->log('error', 'FCM notification failed', [
                'error' => $e->getMessage(),
                'response_data' => $e->getResponseData(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        } catch (Exception $e) {
            $this->log('error', 'FCM notification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Call the FCM API to send a notification
     *
     * @param array $fields The notification payload
     * @param string $accessToken The Google OAuth access token
     * @return array The API response
     * @throws FcmRequestException When the API request fails
     * @throws Exception For other unexpected errors
     */
    private function callApi(array $fields, string $accessToken): array
    {
        $projectId = config('fcm.project_id');
        
        if (empty($projectId)) {
            throw new Exception('Firebase project ID is not set. Please check your .env file for FIREBASE_PROJECT_ID');
        }
        
        $apiUrl = config('fcm.api_url');
        
        if (empty($apiUrl)) {
            $apiUrl = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])
            ->retry(3, 100)
            ->post($apiUrl, $fields);

            if (!$response->successful()) {
                throw new FcmRequestException(
                    'Failed to send FCM notification', 
                    ['response' => $response->body(), 'status' => $response->status()]
                );
            }

            return $response->json();
        } catch (Exception $e) {
            if ($e instanceof FcmRequestException) {
                throw $e;
            }
            throw new FcmRequestException('Failed to send FCM notification: ' . $e->getMessage());
        }
    }

    /**
     * Get Google OAuth access token for FCM API authentication
     *
     * @return string The access token
     * @throws Exception When credentials file is missing or token retrieval fails
     */
    private function getGoogleAccessToken(): string
    {
        try {
            $credentialsFilePath = config('fcm.credentials_path');

            if (!file_exists($credentialsFilePath)) {
                throw new Exception('Firebase credentials file not found at: ' . $credentialsFilePath);
            }

            $client = new Google_Client();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();

            return $token['access_token'];
        } catch (Exception $e) {
            $this->log('error', 'Failed to get Google access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Send a raw FCM message using the previously set raw message data
     *
     * @return array The API response
     * @throws FcmRequestException When the FCM API request fails
     * @throws Exception For other unexpected errors
     */
    public function send(): array
    {
        try {
            $response = $this->callApi($this->fromRaw, $this->getGoogleAccessToken());

            if (isset($response['name'])) {
                $this->log('info', 'Raw FCM message sent successfully', [
                    'message_id' => $response['name']
                ]);
            } else {
                $this->log('error', 'Failed to send raw FCM message', [
                    'error' => $response['error'] ?? 'Unknown error'
                ]);
            }

            return $response;
        } catch (FcmRequestException $e) {
            $this->log('error', 'Failed to send raw FCM message', [
                'error' => $e->getMessage(),
                'response_data' => $e->getResponseData(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } catch (Exception $e) {
            $this->log('error', 'Failed to send raw FCM message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Send FCM notification to one or multiple topics
     *
     * @param string|array $topics Single topic (string) or multiple topics (array)
     * @return bool Whether the notification was sent successfully
     * @throws Exception For unexpected errors
     */
    public function sendToTopics(string|array $topics): bool
    {
        try {
            if (empty($topics)) {
                $this->log('warning', 'Empty topics provided');
                return false;
            }

            $accessToken = $this->getGoogleAccessToken();

            // Build the message payload
            $fields = [
                'message' => [
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'android' => [
                        'priority' => $this->priority->value,
                        'notification' => [
                            'icon' => $this->icon,
                            'color' => $this->color,
                            'image' => $this->image,
                            'sound' => $this->sound ?? 'default',
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'content-available' => 1,
                                'mutable-content' => 1,
                                'sound' => $this->sound ?? 'default'
                            ]
                        ],
                        'fcm_options' => [
                            'image' => $this->image
                        ]
                    ],
                    'webpush' => [
                        'notification' => [
                            'title' => $this->title,
                            'body' => $this->body,
                            'icon' => $this->icon,
                            'image' => $this->image,
                            'sound' => $this->sound ?? 'default',
                        ],
                    ],
                ],
            ];

            // Handle topic or condition based on input type
            if (is_string($topics)) {
                // Single topic
                $fields['message']['topic'] = $topics;
            } else {
                // Multiple topics - use condition with OR operator
                $conditions = array_map(fn($topic) => "'{$topic}' in topics", $topics);
                $fields['message']['condition'] = implode(' || ', $conditions);
            }

            // Add click_action to the appropriate location
            if ($this->clickAction) {
                $fields['message']['android']['notification']['click_action'] = $this->clickAction;
                // For iOS, we need to add category for click_action
                $fields['message']['apns']['payload']['aps']['category'] = $this->clickAction;

                // For WebPush, parse URL and extract path for fcm_options['link']
                $parsedUrl = parse_url($this->clickAction);
                $webpushLink = $parsedUrl['path'] ?? $this->clickAction;
                $fields['message']['webpush']['fcm_options'] = [
                    'link' => $webpushLink
                ];
            }

            if ($this->additionalData) {
                $fields['message']['data'] = $this->additionalData;
                // Merge additionalData into webpush data field as well
                $fields['message']['webpush']['data'] = $this->additionalData;
            }

            try {
                $response = $this->callApi($fields, $accessToken);

                if (isset($response['name'])) {
                    $this->log('info', 'FCM notification sent to topics successfully', [
                        'topics' => is_string($topics) ? $topics : implode(', ', $topics),
                        'message_id' => $response['name']
                    ]);
                    return true;
                } else {
                    $this->log('error', 'Failed to send FCM notification to topics', [
                        'topics' => is_string($topics) ? $topics : implode(', ', $topics),
                        'error' => $response['error'] ?? 'Unknown error'
                    ]);
                    return false;
                }
            } catch (Exception $e) {
                $this->log('error', 'FCM notification to topics failed', [
                    'topics' => is_string($topics) ? $topics : implode(', ', $topics),
                    'error' => $e->getMessage()
                ]);
                return false;
            }
        } catch (FcmRequestException $e) {
            $this->log('error', 'FCM notification to topics failed', [
                'error' => $e->getMessage(),
                'response_data' => $e->getResponseData(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        } catch (Exception $e) {
            $this->log('error', 'FCM notification to topics failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Log a message if logging is enabled
     *
     * @param string $level The log level (info, error, warning, etc.)
     * @param string $message The log message
     * @param array $context Additional context data
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if (config('fcm.logging_enabled', true)) {
            Log::log($level, $message, $context);
        }
    }
}