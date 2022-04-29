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
 * @property mixed $status
 */
class ProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['id' => "mixed", 'title' => "mixed", 'price' => "mixed", 'status' => "mixed", 'created_at' => "mixed", 'categories' => "\App\Http\Resources\CategoryCollection"])] public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'categories' => new CategoryCollection($this->categories),
        ];
    }
}
