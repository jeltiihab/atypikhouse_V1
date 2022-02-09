<?php

namespace App\Mail;

use App\Models\Attribute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttributeAdded extends Mailable
{
    use Queueable, SerializesModels;

    private Attribute $attribute;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Attribute $attribute)
    {
        //
        $this->attribute = $attribute;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.attributeAdded', [
            "attribute" => $this->attribute
        ]);
    }
}
