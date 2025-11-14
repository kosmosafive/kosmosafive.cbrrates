<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http\Adapter;

use Kosmosafive\CBRRates\Http\Client;

abstract class AbstractAdapter implements AdapterInterface
{
    protected Options $options;

    public function __construct(
        protected Client $client,
        Options $options = null
    ) {
        $this->options = $options ?: new Options();
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setOptions(Options $options): AbstractAdapter
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }
}
