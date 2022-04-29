<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $id
 * @property mixed $title
 * @property mixed $categories
 * @property mixed $price
 * @property mixed $created_at
 */
class ProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['id' => "mixed", 'title' => "mixed", 'price' => "mixed", 'categories' => "\App\Http\Resources\CategoryCollection", 'created_at' => "mixed"])] public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'categories' => new CategoryCollection($this->categories),
        ];
    }
}
