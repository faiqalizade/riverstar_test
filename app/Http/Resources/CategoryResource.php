<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $title
 * @property mixed $products_count
 * @property mixed $id
 * @property mixed $created_at
 */
class CategoryResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['id' => "mixed", 'title' => "mixed", 'products_count' => "mixed", 'created_at' => "mixed"])] public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'products_count' => $this->products_count,
            'created_at' => $this->created_at,
        ];
    }
}
