<?php

namespace Esyede\NobuBifast\Exceptions;

class NobuBiFastException extends \Exception
{
    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
            'previous' => $this->getPrevious(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTrace(),
            'traces' => $this->getTraceAsString(),
        ];
    }

    public function toJson($jsonOptions = 0)
    {
        return json_encode($this->toArray(), $jsonOptions);
    }
}
