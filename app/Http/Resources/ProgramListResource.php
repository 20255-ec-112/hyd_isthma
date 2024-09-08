<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'datetime' => $this->datetime ? date('Y-m-d h:m:A', strtotime($this->datetime)) : 'NA',
            'session_theme' => $this->sessionTheme->theme_name,
            'theme_type' => $this->sessionTheme->theme_type,
            'speaker_name' => $this->programSpeaker->name,
            'speaker_bio' => $this->programSpeaker->bio,
            'speaker_image' => $this->programSpeaker->getMedia('speaker_image')->first() ? $this->programSpeaker->getMedia('speaker_image')->first()->getUrl() : '/assets/img/no-image.png',
        ];

        return $data;
    }
}