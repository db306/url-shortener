<?php

declare(strict_types=1);

namespace App\API\Input;

use Symfony\Component\Validator\Constraints as Assert;

class ShortenUrlInput
{
    /**
     * @Assert\Url(
     *     message = "The url '{{ value }}' is not a valid url",
     *     protocols = {"http", "https"}
     * )
     */
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
