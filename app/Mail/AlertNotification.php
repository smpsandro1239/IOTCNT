<?php

namespace Appail;

use Illuminateusueueable;
use IlluminateontractsueuehouldQueue;
use Illuminateailailable;
use Illuminateailailablesontent;
use Illuminateailailablesnvelope;
use IlluminateueueerializesModels;

class AlertNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The message instance.
     *
     * @var string
     */
    public $message;
    
    /**
     * The alert level.
     *
     * @var string
     */
    public $level;
    
    /**
     * The timestamp.
     *
     * @var string
     */
    public $timestamp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $level)
    {
        $this->message = $message;
        $this->level = $level;
        $this->timestamp = now()->format('d/m/Y H:i:s');
    }

    /**
     * Get the message envelope.
     *
     * @return lluminateailailablesnvelope
     */
    public function envelope()
    {
        $subject = match ($this->level) {
            'critical' => '🚨 CRITICAL ALERT - IOTCNT',
            'error' => '❌ ERROR ALERT - IOTCNT',
            'warning' => '⚠️ WARNING ALERT - IOTCNT',
            'info' => 'ℹ️ INFO ALERT - IOTCNT',
            default => '📢 ALERT - IOTCNT',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return lluminateailailablesontent
     */
    public function content()
    {
        return new Content(
            view: 'emails.alert-notification',
            with: [
                'message' => $this->message,
                'level' => $this->level,
                'timestamp' => $this->timestamp,
                'emoji' => $this->getEmojiByLevel($this->level),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
    
    /**
     * Get emoji based on alert level.
     */
    private function getEmojiByLevel($level)
    {
        return match ($level) {
            'critical' => '🚨',
            'error' => '❌',
            'warning' => '⚠️',
            'info' => 'ℹ️',
            default => '📢',
        };
    }
}
