<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceDropNotification extends Notification
{
    use Queueable;

    protected $product;
    protected $oldPrice;
    protected $newPrice;

    /**
     * Create a new notification instance.
     */
    public function __construct($product, $oldPrice, $newPrice)
    {
        $this->product = $product;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Adding a unique timestamp/identifier forces Gmail to treat each as a separate email thread
        $uniqueSubject = 'Price Drop Alert for ' . $this->product->name . ' (' . now()->format('H:i:s') . ')';

        return (new MailMessage)
            ->subject($uniqueSubject)
            ->line("Great news! The price of {$this->product->name} has dropped.")
            ->line("Old Price: " . number_format($this->oldPrice) . " FCFA")
            ->line("New Price: " . number_format($this->newPrice) . " FCFA")
            ->action('View Product', route('product.show', $this->product->id))
            ->line('Thank you for using our price comparison service!');
    }
    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'old_price' => $this->oldPrice,
            'new_price' => $this->newPrice,
        ];
    }
}